# unit_cost – How rows travel

Complements the summary in [07-unit-cost-engine.md](07-unit-cost-engine.md). Code: `stock/models/Unitcost_model.php`; callers `Purchase.php`, `Invoice.php`, `Stock.php`.

## The ledger
`unit_cost` has **one row per costed stock movement**, per **Product + Batch** (store/branch are stored for audit only – the average is *not* per store).

Plain columns: `product`, `batch`, `date`, `save_seq`, `source_type`, `source_no` (voucher id), `line_id` (detail row id), `branch`, `store`, `created_at`.
Encrypted: `qty_in`, `qty_out`, `value_in`, `value_out`, `unit_cost_before`, `unit_cost_after`, `available_qty_after`, `available_value_after`.

**Current cost** of a product+batch = the latest row ordered by `date DESC, save_seq DESC, id DESC` (`current()`); nothing = qty 0, cost 0. `cost_as_of($date)` = `unit_cost_after` of the last row with `date <= $date`.
`save_seq` increments per product+batch+date so same-day movements keep their true save order.

## The formula (`record()`)
```
qty_after   = before.qty + qty_in − qty_out
value_after = before.value + value_in − value_out
cost_after  = value_after / qty_after
```
Special cases, in this order:
1. **Reset** (`reset_on_purchase`, set by Purchase and Opening Stock): if `before.qty <= 0` and `qty_in > 0` → `cost = value_in / qty_in` (ignores stale value).
2. `qty_after ≈ 0` → qty/value set to 0, **keep last cost**.
3. `value_after <= 0` with `qty_after > 0` (odd purchase-return case) → keep previous cost, value = qty × cost.
4. Otherwise normal weighted average.

Outgoing movements are always priced at the cost current *before* the call, so they never change the average.

## Journey of a row, document by document

| Document | When written | `source_type` | Quantity / value | Stamped back on the document line |
|---|---|---|---|---|
| Opening Stock (Inventory Transaction) | per line, on save | `opening_stock` | in: qty, value = user cost × qty (required > 0); reset rule | `stock_details.unit_cost`, `value`, `unit_cost_id` |
| Purchase | per stock line after `purchase_details` insert | `purchase` | in: qty, value = line `total_price` (net of discount, incl. VAT as posted); reset rule | `purchase_details.base_qty`, `net_purchase_unit_cost`, `unit_cost_id` |
| Purchase Return | per line | `purchase_return` | out: qty, value = qty × the **original purchase line's** `net_purchase_unit_cost` (via `invoicedetail`/`source_detail_id`), fallback current cost | `return_unit_cost`, `returned_stock_value`, `source_detail_id`, `unit_cost_id` |
| Sale | per line | `sale` | out: qty × current cost (fallback: product fixed purchase price when never costed) | `sale_details.base_qty`, `line_unit_cost`, `line_cost_value`, `unit_cost_id` |
| Sales Return | per line | `sales_return` | in: qty × the **original sale line's** `line_unit_cost`, fallback current | `return_unit_cost`, `returned_cost_value`, `source_detail_id` |
| Stock Adjustment increase | per Actual line | `adj_increase` | in at current cost | `stock_details.unit_cost`, `value` |
| Stock Adjustment decrease | per Actual line | `adj_decrease` | out at current cost | same |
| Stock Disposal | per line | `disposal` | out at current cost | same |
| Store Transfer, Physical-only adjustment, GRN, GDN, Services | **never** | – | no cost effect | – |

Only products with stock enabled (`isstock = 1` / `pi.stock = 1`) are costed.

## Back-dated saves and edits
After each `record()`, the caller asks `has_later_movements(product, batch, date)`. If rows exist with a *later* date (same-day does not count), it calls `rerun_chain(product, batch, date)` so the new line is placed in true chronological order.

**`rerun_chain($product, $batch, $from_date)`**
1. Snapshot all rows `date >= from_date` (order by date, save_seq, id) – identity only.
2. Delete them.
3. Replay each via `_replay_one()`, re-reading the **current** source line and re-calling `record()`; a row whose source line no longer exists is dropped (this is how deletes/cancels remove a movement).

What each replay re-reads vs recomputes:

| source_type | Re-read from source | Recomputed |
|---|---|---|
| `purchase` | `purchase_details.quantity`, `total_price` | cost blend; refreshes `net_purchase_unit_cost` |
| `opening_stock` | `stock_details.stock`, `value` (user cost) | blend/reset |
| `sale` | `sale_details.quantity` | value = qty × cost at that point in the replay; refreshes `line_unit_cost` |
| `adj_*`, `disposal` | `stock_details.stock` | value = qty × replay-time cost |
| `purchase_return` | `rqty` + original purchase line's *new* `net_purchase_unit_cost` | value |
| `sales_return` | `rqty` + original sale line's *new* `line_unit_cost` | value |

Because each replay rewrites the line's cost columns, **an edit of an old purchase ripples forward** into later sales' `line_unit_cost` and returns.

**When the rerun is triggered:** purchase / purchase-return / sale / sales-return / inventory-transaction **update** (from the earlier of old and new date, for every product+batch touched, old and new), **delete**, and sale **cancel** (`_rerun_affected_pairs`). Each controller captures the affected `(product, batch)` pairs and the old date *before* mutating anything.

## Reading the cost elsewhere
* Sale/disposal/adjustment pricing: `_current_cost_or_fallback()` – current cost, else `product_information.cost_price` (the product's "Fixed Purchase Price").
* Inventory Transaction form preview: `stock/getCurrentUnitCost` (read-only; the backend recomputes at save time).
* Gross-profit style reports use `line_cost_value` / `line_unit_cost` on sale lines.

## One-time migration (`unit_cost_migration` screen)
`rebuild_from_history()` wipes `unit_cost`, zeroes every `unit_cost_id`, and replays **all** historical purchases, returns, sales, adjustments, disposals and opening stock in date order (same-day tie-break: Opening Stock → Purchase → Purchase Return → Sale → Sales Return → Adjustment → Disposal), then reconciles each product+batch's final qty with `SUM(stock_details.stock)` and returns any mismatches.
**Blocker:** every pre-existing opening-stock line needs a cost first (`find_uncosted_opening_stock`, `set_opening_stock_cost`); otherwise it refuses to run.

## Things to watch
* Reconciliation mismatches (cost-ledger qty ≠ stock ledger) mean a movement was skipped or stock data was edited outside the app.
* Negative stock sales are costed at the last known cost; a later purchase resets the average (rule 1) rather than blending with negative value.
* Money/qty columns are encrypted – inspect with `AES_DECRYPT(col, key)`.
