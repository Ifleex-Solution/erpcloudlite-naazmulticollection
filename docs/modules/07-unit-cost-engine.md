# Unit Cost Engine (moving weighted average)

`application/modules/stock/models/Unitcost_model.php`, called from Purchase, Invoice, and Stock controllers. Implements "Unit Cost Logic Specification v1.1" (UCL-xx references in the code).

* Cost is tracked per **Product + Batch** in the `unit_cost` ledger (qty/value in & out, `unit_cost_before/after`, available qty/value after, `save_seq`). Columns are AES-encrypted like other money fields.
* `record($args)` is the only writer: blends `value_in/qty_in` or removes `value_out/qty_out`. Outgoing movements use the cost current *before* the call, so the average is unchanged by them.
* Each transaction line stores its `unit_cost_id` plus its own cost columns.

| Movement | Source type | Cost used | Effect on average |
|---|---|---|---|
| Opening Stock | `opening_stock` | user-entered unit cost (required > 0) | resets/blends (`reset_on_purchase`) |
| Purchase | `purchase` | net line value / qty | blends; resets if prior stock ≤ 0 |
| Sale | `sale` | current cost | none |
| Sales Return | `sales_return` | original sale line cost (fallback current) | blends in |
| Purchase Return | `purchase_return` | original purchase net cost (fallback current) | out at that cost |
| Adjustment ±, Disposal | `adj_increase`, `adj_decrease`, `disposal` | current cost (fallback product fixed purchase price) | none (decrease) / blends (increase) |
| Store Transfer, Physical-only adjustments | – | – | **no cost effect** |

Helpers: `current()`, `cost_as_of($date)`, `has_later_movements()`, `rerun_chain()`.
**Back-dated or edited/deleted documents** trigger `rerun_chain(product, batch, from_date)` so later movements are recomputed in chronological order (`_rerun_affected_pairs` in each controller).
Legacy opening stock without cost: `unit_cost_migration` screen (`get_uncosted_opening_stock`, `save_opening_stock_cost_ajax`, `run_unit_cost_migration_ajax`).
