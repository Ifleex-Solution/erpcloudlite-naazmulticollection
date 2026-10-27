<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Unit Cost Logic Specification v1.1 — core costing engine (Phase 1).
 *
 * Moving weighted-average cost per Product + Batch (UCL-01, UCL-02). This model is the only
 * place that writes to `unit_cost` or computes a new unit cost — every transaction's save path
 * calls record() and stores the returned values on its own line; nothing here knows about
 * purchases, sales or any other screen.
 *
 * record()'s blend formula is deliberately one routine, not branched per transaction type. The
 * spec's "No change" movements (Sale, Adjustment-Decrease, Disposal) are no-ops on the average by
 * construction: the caller always computes value_out from the cost that was current *before* the
 * call, so blending it back out reproduces the same cost. Verified by hand against the spec's
 * Appendix A worked example (Product X, Default batch, no substock):
 *
 *   #1 Opening Stock +100 @2,000           -> qty 100,  value 200,000.00, cost 2,000.00
 *   #2 Sale -40 @ cost 2,000 (value_out=before.cost*40=80,000)
 *                                           -> qty 60,   value 120,000.00, cost 2,000.00 (unchanged)
 *   #3 Purchase +140 @2,600, disc 14,000 (value_in=350,000, reset_on_purchase=true but
 *      before.qty=60>0 so no reset, plain blend)
 *                                           -> qty 200,  value 470,000.00, cost 2,350.00
 *   #4 Sales Return +10 @ original sale cost 2,000 (value_in=20,000)
 *                                           -> qty 210,  value 490,000.00, cost 2,333.33...
 *   #5 Purchase Return -20 @ original purchase cost 2,500 (value_out=50,000)
 *                                           -> qty 190,  value 440,000.00, cost 2,315.78...
 *   #7 Disposal -5 @ cost 2,315.789... (value_out=before.cost*5)
 *                                           -> qty 185,  value 428,421.05..., cost unchanged
 *   #9 Sale -85 @ cost 2,315.789...
 *                                           -> qty 100,  value 231,578.94..., cost unchanged
 *
 * matches the spec's published figures to the cent. Steps #6 (Store Transfer) and #8 (Physical
 * adjustment) never call record() at all — they have no cost effect (UCL-04).
 *
 * Every money/quantity column this model touches (qty_in, qty_out, value_in, value_out,
 * unit_cost_before, unit_cost_after, available_qty_after, available_value_after, and the
 * base_qty/net_purchase_unit_cost/line_unit_cost/line_cost_value/return_unit_cost/
 * returned_stock_value/returned_cost_value/unit_cost/value columns on the transaction-detail
 * tables) is stored AES_ENCRYPT'd in a varbinary column, matching this codebase's existing
 * convention for every other money/quantity column (purchase_details.quantity, stock_details.stock,
 * product_information.cost_price, etc.) — never plain DECIMAL. Only id/FK-like and non-sensitive
 * columns (product, batch, save_seq, source_type, source_no, line_id, branch, store, unit_cost_id,
 * source_detail_id, date, created_at) stay plain, also matching the existing convention.
 */
class Unitcost_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Formats a numeric value for AES_ENCRYPT storage — fixed 6-decimal notation (matching the
     * precision these columns held before the encryption retrofit), never scientific notation.
     */
    private function _enc($value)
    {
        return number_format((float) $value, 6, '.', '');
    }

    /**
     * Current Available Qty / Value / Unit Cost of a Product + Batch — the latest unit_cost row.
     * Zeros when the batch has never been costed (UCL-38, UCL-42 fall back to Fixed Purchase
     * Price when this returns qty=0, cost=0).
     */
    public function current($product, $batch)
    {
        $enc = Config::$encryption_key;
        $product = (int) $product;
        $batch = (int) $batch;

        $row = $this->db->query("
            SELECT AES_DECRYPT(available_qty_after, '{$enc}') AS available_qty_after,
                   AES_DECRYPT(available_value_after, '{$enc}') AS available_value_after,
                   AES_DECRYPT(unit_cost_after, '{$enc}') AS unit_cost_after
            FROM unit_cost
            WHERE product = {$product} AND batch = {$batch}
            ORDER BY date DESC, save_seq DESC, id DESC
            LIMIT 1
        ")->row();

        if (!$row) {
            return ['qty' => 0.0, 'value' => 0.0, 'cost' => 0.0];
        }

        return [
            'qty'   => (float) $row->available_qty_after,
            'value' => (float) $row->available_value_after,
            'cost'  => (float) $row->unit_cost_after,
        ];
    }

    /**
     * Phase 5 — back-dated-entry detection: true when this Product + Batch already has a costed
     * movement strictly LATER than $date. A normal (non-back-dated) save can keep appending via
     * record() as before; when this returns true, the caller must follow up with rerun_chain()
     * from $date so the chain gets recomputed in the correct chronological order instead of
     * blending the new line against the wrong (globally-latest, not as-of-$date) "before" state.
     * A same-date entry is NOT back-dated — UCL-05's save_seq tie-break already puts it correctly
     * after whatever was saved earlier that same day.
     */
    public function has_later_movements($product, $batch, $date)
    {
        $product = (int) $product;
        $batch = (int) $batch;
        $date_esc = $this->db->escape($date);

        $row = $this->db->query("
            SELECT 1 FROM unit_cost WHERE product = {$product} AND batch = {$batch} AND date > {$date_esc} LIMIT 1
        ")->row();

        return (bool) $row;
    }

    /**
     * UCL-60: Unit Cost as at a date = Unit Cost After of the last record of that Product + Batch
     * with Date <= that date. Returns 0 when the batch had no cost yet at that date.
     */
    public function cost_as_of($product, $batch, $date)
    {
        $enc = Config::$encryption_key;
        $product = (int) $product;
        $batch = (int) $batch;
        $date_esc = $this->db->escape($date);

        $row = $this->db->query("
            SELECT AES_DECRYPT(unit_cost_after, '{$enc}') AS unit_cost_after
            FROM unit_cost
            WHERE product = {$product} AND batch = {$batch} AND date <= {$date_esc}
            ORDER BY date DESC, save_seq DESC, id DESC
            LIMIT 1
        ")->row();

        return $row ? (float) $row->unit_cost_after : 0.0;
    }

    /**
     * Records one costed movement and re-averages the Product + Batch. Returns the after-state
     * so the caller can store it on its own transaction line (e.g. sale_details.line_cost_value).
     *
     * $args keys (all required unless noted):
     *   product, batch        int
     *   date                  'YYYY-MM-DD'
     *   source_type           string, e.g. 'purchase', 'sale', 'sales_return', 'purchase_return',
     *                         'opening_stock', 'adj_increase', 'adj_decrease', 'disposal'
     *   source_no             int — the voucher id (pid)
     *   line_id               int — the detail line id
     *   branch, store         int, optional, default 0 (audit only — not part of the cost key)
     *   qty_in, qty_out       float, Base Unit, default 0 (exactly one should be non-zero)
     *   value_in, value_out   float, default 0
     *   reset_on_purchase     bool, default false — UCL-22: when true and Available Qty before
     *                         is <= 0, cost resets to this movement's own net unit cost
     *                         (value_in / qty_in) instead of blending. Purchase and Opening Stock
     *                         pass true; every other source_type leaves this false.
     *
     * Returns ['unit_cost_id', 'cost_after', 'value_after', 'qty_after'].
     */
    public function record($args)
    {
        $product = $args['product'];
        $batch   = $args['batch'];
        $qty_in    = isset($args['qty_in'])    ? (float) $args['qty_in']    : 0.0;
        $qty_out   = isset($args['qty_out'])   ? (float) $args['qty_out']   : 0.0;
        $value_in  = isset($args['value_in'])  ? (float) $args['value_in']  : 0.0;
        $value_out = isset($args['value_out']) ? (float) $args['value_out'] : 0.0;
        $reset_on_purchase = !empty($args['reset_on_purchase']);

        $before = $this->current($product, $batch);

        $qty_after = $before['qty'] + $qty_in - $qty_out;
        $raw_value_after = $before['value'] + $value_in - $value_out;

        if ($reset_on_purchase && $before['qty'] <= 0 && $qty_in > 0) {
            // UCL-22: stock was at/below zero before this purchase/opening-stock line — the new
            // cost is this line's own net unit cost, not a blend with whatever (stale) value
            // preceded it.
            $cost_after  = $value_in / $qty_in;
            $value_after = $qty_after * $cost_after;
        } elseif (abs($qty_after) < 0.000001) {
            // UCL-07: qty settles to exactly zero — clear rounding remainders, keep last cost.
            $qty_after   = 0.0;
            $value_after = 0.0;
            $cost_after  = $before['cost'];
        } elseif ($raw_value_after <= 0 && $qty_after > 0) {
            // UCL-32: rare purchase-return-leaves-negative-value edge case — keep the previous
            // unit cost and value the remaining qty at it, rather than a nonsensical cost.
            $cost_after  = $before['cost'];
            $value_after = $qty_after * $cost_after;
        } else {
            $value_after = $raw_value_after;
            $cost_after  = $value_after / $qty_after;
        }

        $save_seq_row = $this->db->select_max('save_seq')
            ->from('unit_cost')
            ->where('product', $product)
            ->where('batch', $batch)
            ->where('date', $args['date'])
            ->get()
            ->row();
        $save_seq = ($save_seq_row && $save_seq_row->save_seq !== null) ? ((int) $save_seq_row->save_seq + 1) : 1;

        $now = date('Y-m-d H:i:s');
        $enc = Config::$encryption_key;

        $this->db->set('product', $product);
        $this->db->set('batch', $batch);
        $this->db->set('date', $args['date']);
        $this->db->set('save_seq', $save_seq);
        $this->db->set('source_type', $args['source_type']);
        $this->db->set('source_no', $args['source_no']);
        $this->db->set('line_id', $args['line_id']);
        $this->db->set('branch', isset($args['branch']) ? $args['branch'] : 0);
        $this->db->set('store', isset($args['store']) ? $args['store'] : 0);
        $this->db->set('qty_in', "AES_ENCRYPT('" . $this->_enc($qty_in) . "', '{$enc}')", FALSE);
        $this->db->set('qty_out', "AES_ENCRYPT('" . $this->_enc($qty_out) . "', '{$enc}')", FALSE);
        $this->db->set('value_in', "AES_ENCRYPT('" . $this->_enc($value_in) . "', '{$enc}')", FALSE);
        $this->db->set('value_out', "AES_ENCRYPT('" . $this->_enc($value_out) . "', '{$enc}')", FALSE);
        $this->db->set('unit_cost_before', "AES_ENCRYPT('" . $this->_enc($before['cost']) . "', '{$enc}')", FALSE);
        $this->db->set('unit_cost_after', "AES_ENCRYPT('" . $this->_enc($cost_after) . "', '{$enc}')", FALSE);
        $this->db->set('available_qty_after', "AES_ENCRYPT('" . $this->_enc($qty_after) . "', '{$enc}')", FALSE);
        $this->db->set('available_value_after', "AES_ENCRYPT('" . $this->_enc($value_after) . "', '{$enc}')", FALSE);
        $this->db->set('created_at', $now);
        $this->db->insert('unit_cost');

        return [
            'unit_cost_id' => $this->db->insert_id(),
            'cost_after'   => $cost_after,
            'value_after'  => $value_after,
            'qty_after'    => $qty_after,
        ];
    }

    /**
     * UCL-50 (Phase 5) — re-runs the cost chain of one Product + Batch from $from_date onward.
     * Called whenever a stock transaction touching this Product + Batch is saved with an earlier
     * date than later movements, edited, cancelled, or reversed.
     *
     * How it works: every unit_cost row from $from_date onward is a record of something that
     * happened, in the correct order (save_seq already captured true save order when each row
     * was first created) — so that ordering doesn't need to be rediscovered. What CAN be stale
     * is each row's qty/value, because the edit that triggered this re-run may have changed an
     * earlier line's quantity, price, or cost. So: snapshot the existing rows for their order and
     * identity (source_type/source_no/line_id) only, delete them, then replay each one by
     * re-reading its *current* source line (purchase_details, sale_details, etc. — possibly just
     * edited) and re-calling record() exactly as the original save path did. A line whose source
     * row no longer exists (reversed/deleted) is simply skipped, which is exactly "remove this
     * movement from the chain".
     *
     * Per-source-type recompute rules (why each one does what it does):
     *   purchase          — value is a historical fact (what was actually paid); re-read it fresh
     *                       from purchase_details in case price/qty/discount were edited.
     *   sale               — qty is a historical fact (re-used from the snapshot); value is
     *                        ALWAYS recomputed as qty x cost-just-before-this-line, read fresh
     *                        from current() at this point in the replay — never reused from the
     *                        old row, since an earlier edit may have changed that cost (UCL-34).
     *   opening_stock      — value is the user's typed cost x qty — a historical fact, re-read
     *                        fresh in case the user edited the Unit Cost itself (UCL-52).
     *   purchase_return    — value is recomputed from the ORIGINAL purchase line's current
     *                        net_purchase_unit_cost (which the replay may have just updated,
     *                        since that purchase is chronologically earlier in this same chain).
     *   sales_return       — same idea: recomputed from the original sale line's current
     *                        line_unit_cost.
     *   adj_increase / adj_decrease / disposal — qty is a historical fact; value is always
     *                        recomputed at the current (replay-time) cost, same as 'sale'.
     *
     * Returns the number of movements replayed.
     */
    public function rerun_chain($product, $batch, $from_date)
    {
        $rows = $this->db->select('*')
            ->from('unit_cost')
            ->where('product', $product)
            ->where('batch', $batch)
            ->where('date >=', $from_date)
            ->order_by('date', 'ASC')
            ->order_by('save_seq', 'ASC')
            ->order_by('id', 'ASC')
            ->get()
            ->result_array();

        $this->db->where('product', $product)
            ->where('batch', $batch)
            ->where('date >=', $from_date)
            ->delete('unit_cost');

        $replayed = 0;
        foreach ($rows as $row) {
            if ($this->_replay_one($row)) {
                $replayed++;
            }
        }
        return $replayed;
    }

    /**
     * Replays a single snapshotted unit_cost row: re-reads its source line, recomputes qty/value
     * per the rules in rerun_chain()'s docblock, and calls record() again. Returns false (and
     * records nothing) when the source line no longer exists.
     */
    private function _replay_one($row)
    {
        $product     = $row['product'];
        $batch       = $row['batch'];
        $date        = $row['date'];
        $source_type = $row['source_type'];
        $source_no   = $row['source_no'];
        $line_id     = $row['line_id'];
        $branch      = $row['branch'];
        $store       = $row['store'];
        $enc         = Config::$encryption_key;

        switch ($source_type) {
            case 'purchase':
                $line = $this->db->query("
                    SELECT AES_DECRYPT(quantity, '{$enc}') AS quantity, AES_DECRYPT(total_price, '{$enc}') AS total_price
                    FROM purchase_details WHERE id = {$line_id}
                ")->row();
                if (!$line) return false;
                $qty_in = (float) $line->quantity;
                $value_in = (float) $line->total_price;
                if ($qty_in <= 0) return false;

                $result = $this->record([
                    'product' => $product, 'batch' => $batch, 'date' => $date,
                    'source_type' => 'purchase', 'source_no' => $source_no, 'line_id' => $line_id,
                    'branch' => $branch, 'store' => $store, 'qty_in' => $qty_in, 'value_in' => $value_in,
                    'reset_on_purchase' => true,
                ]);
                $this->db->set('base_qty', "AES_ENCRYPT('" . $this->_enc($qty_in) . "', '{$enc}')", FALSE);
                $this->db->set('net_purchase_unit_cost', "AES_ENCRYPT('" . $this->_enc($value_in / $qty_in) . "', '{$enc}')", FALSE);
                $this->db->set('unit_cost_id', $result['unit_cost_id']);
                $this->db->where('id', $line_id)->update('purchase_details');
                return true;

            case 'opening_stock':
                // stock_details carries this line's Unit Cost / Value directly (Phase 2) — both
                // are historical facts the user typed, re-read in case they were edited.
                $line = $this->db->query("
                    SELECT AES_DECRYPT(stock, '{$enc}') AS stock, AES_DECRYPT(value, '{$enc}') AS value
                    FROM stock_details WHERE id = {$line_id}
                ")->row();
                if (!$line || (float) $line->value <= 0) return false;
                $qty_in = abs((float) $line->stock);
                if ($qty_in <= 0) return false;

                $result = $this->record([
                    'product' => $product, 'batch' => $batch, 'date' => $date,
                    'source_type' => 'opening_stock', 'source_no' => $source_no, 'line_id' => $line_id,
                    'branch' => $branch, 'store' => $store, 'qty_in' => $qty_in, 'value_in' => (float) $line->value,
                    'reset_on_purchase' => true,
                ]);
                $this->db->where('id', $line_id)->update('stock_details', ['unit_cost_id' => $result['unit_cost_id']]);
                return true;

            case 'sale':
                // Always re-read `quantity`, never `base_qty`: base_qty is only stamped when the
                // line is first costed, so it's stale after update_sale() edits the quantity (and
                // empty on every sale saved before Phase 4, which the Phase 8 migration replays).
                // `quantity` is always current and is the same base-unit figure the stock ledger
                // records for the line (stock_details = -quantity). base_qty is refreshed below.
                $line = $this->db->query("
                    SELECT AES_DECRYPT(quantity, '{$enc}') AS quantity
                    FROM sale_details WHERE id = {$line_id}
                ")->row();
                if (!$line) return false;
                $qty_out = (float) $line->quantity;
                if ($qty_out <= 0) return false;
                $cost_now = $this->current($product, $batch)['cost'];
                $value_out = $qty_out * $cost_now;

                $result = $this->record([
                    'product' => $product, 'batch' => $batch, 'date' => $date,
                    'source_type' => 'sale', 'source_no' => $source_no, 'line_id' => $line_id,
                    'branch' => $branch, 'store' => $store, 'qty_out' => $qty_out, 'value_out' => $value_out,
                ]);
                $this->db->set('base_qty', "AES_ENCRYPT('" . $this->_enc($qty_out) . "', '{$enc}')", FALSE);
                $this->db->set('line_unit_cost', "AES_ENCRYPT('" . $this->_enc($cost_now) . "', '{$enc}')", FALSE);
                $this->db->set('line_cost_value', "AES_ENCRYPT('" . $this->_enc($value_out) . "', '{$enc}')", FALSE);
                $this->db->set('unit_cost_id', $result['unit_cost_id']);
                $this->db->where('id', $line_id)->update('sale_details');
                return true;

            case 'adj_increase':
            case 'adj_decrease':
            case 'disposal':
                // qty is a historical fact (how much moved) — read it from stock_details.stock,
                // the same AES-encrypted quantity every stock_details row already carries
                // (positive for increase, negative for decrease/disposal); re-valued below at
                // the current replay-time cost.
                $line = $this->db->query("
                    SELECT AES_DECRYPT(stock, '{$enc}') AS stock FROM stock_details WHERE id = {$line_id}
                ")->row();
                if (!$line) return false;
                $qty = abs((float) $line->stock);
                if ($qty <= 0) return false;
                $cost_now = $this->current($product, $batch)['cost'];
                $value_now = $qty * $cost_now;

                $args = [
                    'product' => $product, 'batch' => $batch, 'date' => $date,
                    'source_type' => $source_type, 'source_no' => $source_no, 'line_id' => $line_id,
                    'branch' => $branch, 'store' => $store,
                ];
                if ($source_type === 'adj_increase') {
                    $args['qty_in'] = $qty; $args['value_in'] = $value_now;
                } else {
                    $args['qty_out'] = $qty; $args['value_out'] = $value_now;
                }
                $result = $this->record($args);
                $this->db->set('unit_cost', "AES_ENCRYPT('" . $this->_enc($cost_now) . "', '{$enc}')", FALSE);
                $this->db->set('value', "AES_ENCRYPT('" . $this->_enc($value_now) . "', '{$enc}')", FALSE);
                $this->db->set('unit_cost_id', $result['unit_cost_id']);
                $this->db->where('id', $line_id)->update('stock_details');
                return true;

            case 'purchase_return':
                $line = $this->db->query("
                    SELECT AES_DECRYPT(rqty, '{$enc}') AS rqty, source_detail_id
                    FROM purchase_return_details WHERE id = {$line_id}
                ")->row();
                if (!$line) return false;
                $qty_out = abs((float) $line->rqty);
                if ($qty_out <= 0) return false;

                // Re-read the ORIGINAL purchase line's current cost — may have just been
                // recomputed earlier in this same replay, since it's chronologically before this
                // return in the chain.
                $return_cost = 0.0;
                if (!empty($line->source_detail_id)) {
                    $orig = $this->db->query("
                        SELECT AES_DECRYPT(net_purchase_unit_cost, '{$enc}') AS net_purchase_unit_cost
                        FROM purchase_details WHERE id = {$line->source_detail_id}
                    ")->row();
                    $return_cost = $orig ? (float) $orig->net_purchase_unit_cost : 0.0;
                }
                if ($return_cost <= 0) {
                    $return_cost = $this->current($product, $batch)['cost'];
                }
                $value_out = $qty_out * $return_cost;

                $result = $this->record([
                    'product' => $product, 'batch' => $batch, 'date' => $date,
                    'source_type' => 'purchase_return', 'source_no' => $source_no, 'line_id' => $line_id,
                    'branch' => $branch, 'store' => $store, 'qty_out' => $qty_out, 'value_out' => $value_out,
                ]);
                $this->db->set('return_unit_cost', "AES_ENCRYPT('" . $this->_enc($return_cost) . "', '{$enc}')", FALSE);
                $this->db->set('returned_stock_value', "AES_ENCRYPT('" . $this->_enc($value_out) . "', '{$enc}')", FALSE);
                $this->db->set('unit_cost_id', $result['unit_cost_id']);
                $this->db->where('id', $line_id)->update('purchase_return_details');
                return true;

            case 'sales_return':
                $line = $this->db->query("
                    SELECT AES_DECRYPT(rqty, '{$enc}') AS rqty, source_detail_id
                    FROM sales_return_details WHERE id = {$line_id}
                ")->row();
                if (!$line) return false;
                $qty_in = abs((float) $line->rqty);
                if ($qty_in <= 0) return false;

                $return_cost = 0.0;
                if (!empty($line->source_detail_id)) {
                    $orig = $this->db->query("
                        SELECT AES_DECRYPT(line_unit_cost, '{$enc}') AS line_unit_cost
                        FROM sale_details WHERE id = {$line->source_detail_id}
                    ")->row();
                    $return_cost = $orig ? (float) $orig->line_unit_cost : 0.0;
                }
                if ($return_cost <= 0) {
                    $return_cost = $this->current($product, $batch)['cost'];
                }
                $value_in = $qty_in * $return_cost;

                $result = $this->record([
                    'product' => $product, 'batch' => $batch, 'date' => $date,
                    'source_type' => 'sales_return', 'source_no' => $source_no, 'line_id' => $line_id,
                    'branch' => $branch, 'store' => $store, 'qty_in' => $qty_in, 'value_in' => $value_in,
                ]);
                $this->db->set('return_unit_cost', "AES_ENCRYPT('" . $this->_enc($return_cost) . "', '{$enc}')", FALSE);
                $this->db->set('returned_cost_value', "AES_ENCRYPT('" . $this->_enc($value_in) . "', '{$enc}')", FALSE);
                $this->db->set('unit_cost_id', $result['unit_cost_id']);
                $this->db->where('id', $line_id)->update('sales_return_details');
                return true;

            default:
                // storetransfer / physical adjustment never reach here (never costed in the
                // first place, UCL-44) — if an unrecognised type shows up, skip rather than guess.
                return false;
        }
    }

    /**
     * Phase 8 (UCL-90–UCL-93) — one-time migration job per existing client database: rebuild
     * `unit_cost` from the full transaction history, in date order, using the exact same
     * record()/_replay_one() routines every live save path already uses. This is what makes the
     * replay trustworthy — it is not a separate parallel implementation of the costing rules, it
     * is the same one, run once against history instead of once per live save.
     *
     * UCL-91's hard blocker: every pre-existing Opening Stock line must have a real Unit Cost
     * before this can run (there is no way to blend-in a Product+Batch's very first movement
     * without one — UCL-22's reset case divides by qty_in). find_uncosted_opening_stock() /
     * set_opening_stock_cost() below are the "Opening Stock Cost Review" data-collection step
     * UCL-91 describes; call find_uncosted_opening_stock() first and route an admin through
     * entering a cost for every row it returns before calling this.
     *
     * Returns ['success' => false, 'blocked' => true, 'rows' => [...]] if the blocker isn't
     * cleared (nothing is touched in that case). Otherwise wipes every existing unit_cost row and
     * every detail table's unit_cost_id back to 0, replays the full history, and returns
     * ['success' => true, 'replayed' => <int>, 'skipped' => <int>, 'reconciliation' => [...]] —
     * reconciliation lists any Product+Batch where the rebuilt chain's final Available Qty doesn't
     * match the Actual-stock ledger's own running total (UCL-61/UCL-62), which should never happen
     * if every movement replayed cleanly, so a non-empty list here means something in the replay
     * (or the pre-existing transaction data itself) needs investigation before trusting the result.
     */
    public function rebuild_from_history()
    {
        $blocked = $this->find_uncosted_opening_stock();
        if (!empty($blocked)) {
            return ['success' => false, 'blocked' => true, 'rows' => $blocked];
        }

        @ini_set('max_execution_time', 0);
        @ini_set('memory_limit', '1024M');

        $enc = Config::$encryption_key;

        $this->db->trans_begin();
        try {
            $this->db->empty_table('unit_cost');
            foreach (['purchase_details', 'purchase_return_details', 'sale_details', 'sales_return_details', 'stock_details'] as $table) {
                $this->db->set('unit_cost_id', 0)->update($table);
            }

            // One uniform row shape (product, batch, date, source_type, source_no, line_id,
            // branch, store) across every historically-costed movement type, built directly from
            // the live transaction tables rather than from unit_cost (which has just been wiped).
            // type_rank is a same-date tie-break: the true original save order is unrecoverable
            // for data that pre-dates this feature, so same-day movements are ordered
            // Opening Stock -> Purchase -> Purchase Return -> Sale -> Sales Return ->
            // Adjustment -> Disposal — stock-in before stock-out, which is the only ordering
            // assumption that can't itself create a negative-stock artefact that didn't already
            // exist in the real history.
            $rows = $this->db->query("
                SELECT product, batch, date, source_type, source_no, line_id, branch, store
                FROM (
                    SELECT pd.product AS product, pd.batch AS batch, p.date AS date,
                           'purchase' AS source_type, pd.pid AS source_no, pd.id AS line_id,
                           0 AS branch, pd.store AS store, 2 AS type_rank
                    FROM purchase_details pd
                    INNER JOIN purchase p ON p.id = pd.pid
                    INNER JOIN product_information pi1 ON pi1.id = pd.product AND pi1.stock = 1

                    UNION ALL

                    SELECT prd.product, prd.batch, pr.rdate, 'purchase_return', prd.pid, prd.id,
                           0, prd.store, 3
                    FROM purchase_return_details prd
                    INNER JOIN purchase_return pr ON pr.id = prd.pid
                    INNER JOIN product_information pi2 ON pi2.id = prd.product AND pi2.stock = 1

                    UNION ALL

                    SELECT sd.product, sd.batch, s.date, 'sale', sd.pid, sd.id, 0, sd.store, 4
                    FROM sale_details sd
                    INNER JOIN sale s ON s.id = sd.pid
                    INNER JOIN product_information pi3 ON pi3.id = sd.product AND pi3.stock = 1

                    UNION ALL

                    SELECT srd.product, srd.batch, sr.rdate, 'sales_return', srd.pid, srd.id,
                           0, srd.store, 5
                    FROM sales_return_details srd
                    INNER JOIN sales_return sr ON sr.id = srd.pid
                    INNER JOIN product_information pi4 ON pi4.id = srd.product AND pi4.stock = 1

                    UNION ALL

                    SELECT sd2.product, sd2.batch, sd2.date,
                           CASE
                               WHEN ast.type = 'openingstock' THEN 'opening_stock'
                               WHEN ast.type = 'stockdisposal' THEN 'disposal'
                               WHEN ast.type = 'stockadjustment' AND (AES_DECRYPT(sd2.stock, '{$enc}') + 0) >= 0 THEN 'adj_increase'
                               WHEN ast.type = 'stockadjustment' THEN 'adj_decrease'
                               ELSE NULL
                           END AS source_type,
                           sd2.pid, sd2.id, 0, sd2.store,
                           CASE
                               WHEN ast.type = 'openingstock' THEN 1
                               WHEN ast.type = 'stockadjustment' THEN 6
                               WHEN ast.type = 'stockdisposal' THEN 7
                               ELSE 99
                           END AS type_rank
                    FROM stock_details sd2
                    INNER JOIN adj_stock ast ON ast.id = sd2.pid
                    WHERE sd2.type = 'adj_stock' AND ast.type IN ('openingstock', 'stockdisposal', 'stockadjustment')
                ) all_movements
                WHERE source_type IS NOT NULL
                ORDER BY date ASC, type_rank ASC, line_id ASC
            ")->result_array();

            $replayed = 0;
            $skipped = 0;
            $products_batches = [];
            foreach ($rows as $row) {
                if ($this->_replay_one($row)) {
                    $replayed++;
                    $products_batches[$row['product'] . ':' . $row['batch']] = [
                        'product' => $row['product'], 'batch' => $row['batch'],
                    ];
                } else {
                    $skipped++;
                }
            }

            if ($this->db->trans_status() === false) {
                throw new Exception('Database operation failed during rebuild.');
            }
            $this->db->trans_commit();
        } catch (Exception $e) {
            $this->db->trans_rollback();
            return ['success' => false, 'error' => $e->getMessage()];
        }

        // UCL-61/UCL-62 reconciliation — the rebuilt chain's final Available Qty per Product+Batch
        // must equal the Actual-stock ledger's own running total (the independently-maintained
        // sum of stock_details.stock), or something in the replay or the source data is wrong.
        $reconciliation = [];
        foreach ($products_batches as $pb) {
            $final_qty = $this->current($pb['product'], $pb['batch'])['qty'];
            $ledger_row = $this->db->query("
                SELECT SUM(AES_DECRYPT(stock, '{$enc}') + 0) AS total
                FROM stock_details WHERE product = {$pb['product']} AND batch = {$pb['batch']}
            ")->row();
            $ledger_qty = $ledger_row ? (float) $ledger_row->total : 0.0;
            if (abs($final_qty - $ledger_qty) > 0.000001) {
                $reconciliation[] = [
                    'product' => $pb['product'], 'batch' => $pb['batch'],
                    'unit_cost_qty' => $final_qty, 'ledger_qty' => $ledger_qty,
                ];
            }
        }

        return [
            'success' => true,
            'replayed' => $replayed,
            'skipped' => $skipped,
            'reconciliation' => $reconciliation,
        ];
    }

    /**
     * UCL-91 — every pre-existing Opening Stock line (stock_details row under an adj_stock
     * voucher of type 'openingstock') with no Unit Cost recorded yet (the column reads back as
     * NULL from AES_DECRYPT — never set — or, defensively, <= 0). rebuild_from_history() refuses
     * to run while this list is non-empty.
     */
    public function find_uncosted_opening_stock()
    {
        $enc = Config::$encryption_key;

        return $this->db->query("
            SELECT
                sd.id AS stock_details_id,
                sd.product, sd.batch, sd.store, sd.date,
                AES_DECRYPT(sd.stock, '{$enc}') + 0 AS qty,
                pi.product_name,
                CAST(AES_DECRYPT(sb.batchid, '{$enc}') AS CHAR) AS batch_name,
                st.name AS store_name
            FROM stock_details sd
            INNER JOIN adj_stock ast ON ast.id = sd.pid
            INNER JOIN product_information pi ON pi.id = sd.product
            LEFT JOIN stockbatch sb ON sb.id = sd.batch
            LEFT JOIN store st ON st.id = sd.store
            WHERE sd.type = 'adj_stock' AND ast.type = 'openingstock'
              AND (AES_DECRYPT(sd.value, '{$enc}') IS NULL OR AES_DECRYPT(sd.value, '{$enc}') + 0 <= 0)
            ORDER BY sd.date ASC, sd.id ASC
        ")->result_array();
    }

    /**
     * UCL-91 — records the Unit Cost an admin enters for one pre-existing uncosted Opening Stock
     * line, found via find_uncosted_opening_stock(). Only updates stock_details.unit_cost/value
     * (the historical-fact fields _replay_one()'s 'opening_stock' case reads) — does not call
     * record() itself, since this Opening Stock line's place in the Product+Batch's cost chain is
     * only resolved once rebuild_from_history() replays the full history in order.
     */
    public function set_opening_stock_cost($stock_details_id, $unit_cost)
    {
        $stock_details_id = (int) $stock_details_id;
        $unit_cost = (float) $unit_cost;
        if ($unit_cost <= 0) {
            return ['success' => false, 'error' => 'Unit Cost must be greater than 0.'];
        }

        $enc = Config::$encryption_key;
        $line = $this->db->query("
            SELECT AES_DECRYPT(stock, '{$enc}') + 0 AS qty FROM stock_details WHERE id = {$stock_details_id}
        ")->row();
        if (!$line) {
            return ['success' => false, 'error' => 'Stock line not found.'];
        }

        $qty = abs((float) $line->qty);
        $value = $qty * $unit_cost;

        $this->db->set('unit_cost', "AES_ENCRYPT('" . $this->_enc($unit_cost) . "', '{$enc}')", FALSE);
        $this->db->set('value', "AES_ENCRYPT('" . $this->_enc($value) . "', '{$enc}')", FALSE);
        $this->db->where('id', $stock_details_id)->update('stock_details');

        return ['success' => true];
    }
}
