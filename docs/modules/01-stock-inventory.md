# Stock (sidebar: Stock)

Module: `application/modules/stock` · Controller `Stock.php` · Models `Stock_model.php`, `Unitcost_model.php`.
Sidebar items: **Stock Batch**, **Inventory Transaction Request**, **Inventory Transaction**.
See [00-overview.md](00-overview.md) for the ledgers (`stock_details`, `phystock_details`, `audit_stock`) and the store GRN/GDN flags.

---

## 1. Stock Batch

| | |
|---|---|
| Routes | `stockbatchlist` (list), `stockbatch_form[/id]` (add/edit) |
| Permissions | `stockbatchlist` read, `add_stockbatch` create/update |
| Table | `stockbatch` |

**Fields:** Batch Id (required, unique per product), Details, Batch Usage Type (`single` / `multiple`), Product (single-usage batches belong to one product; multiple-usage use a product list), Manufacturing / Packing date, Expiry (toggle `edate_toggle`), MRP, Status.

**Rules**
* Duplicate Batch Id for the same product is rejected (`save_stockbatch_ajax`, `check_duplicate_stockbatch`).
* Delete is blocked when the batch appears in `stock_details` or `phystock_details` ("being used in stock transactions").
* Batch can also be created from a modal inside transaction forms ("Add New Batch").
* Every product has a **Default** batch; stock is always tracked per product + batch.

---

## 2. Inventory Transaction (`adj_stock`)

| | |
|---|---|
| Routes | `new_inventory_transection[/id]`, `manage_inventory_transection` |
| Permissions | `new_inventory_transection`, `manage_inventory_transection` |
| Header table | `adj_stock` (`inventory_id` `INV-…`, date, reason, `type` incident, `stocktype`, `type2`, `inventory_request_no`) |
| Lines | `stock_details` / `phystock_details` with `type='adj_stock'`, `pid = adj_stock.id` |
| Save / update | `save_adjstock` / `update_adjstock`; Store Transfer uses `save_storetransfer` / `update_storetransfer` |
| Delete | `delete_adjstock/{id}` |
| Print | `printInventoryTransaction` |

One screen handles four **Incident Types** (`type`), which drive the form via `change_type()`:

| Incident | Stock Type | Line direction | From/To store | Effect |
|---|---|---|---|---|
| **Opening Stock** | Both (fixed, hidden) | Increase only | line store | + actual **and** physical. Requires **Unit Cost > 0** per line. |
| **Store Transfer** | Actual (fixed) | header From Store → To Store | `from_store`, `to_store` | − actual at From, + actual at To (see physical rule below). **No cost effect.** |
| **Stock Disposal** | Actual (fixed) | Decrease only | line store | − actual (and − physical if store GDN flag is 0 … see below). Valued at current cost. |
| **Stock Adjustment** | user picks Actual / Physical / Both | Increase or Decrease per line | line store | Writes to the chosen ledger(s). Actual-type lines are valued at current cost. |

**Line columns:** Product, Store, Batch, Unit (sub-unit supported), Av.Qty, Adj. Type (Increase/Decrease), Adj. Qty (Qty for non-adjustment types), Unit Cost (Opening Stock only, editable; others read-only preview from `getCurrentUnitCost`).

**Ledger write rules (`save_adjstock`)**
* `stocktype = actualstock`: insert `stock_details`; run `_record_inventory_cost`; audit row. For *Store Transfer / Stock Disposal* also insert `phystock_details` **when the store's relevant flag is 0** (`auto_grn` for increase, `auto_gdn` for decrease) — otherwise physical stock is left to a GRN/GDN.
* `stocktype = physicalstock`: `phystock_details` only (never costed).
* `stocktype = both`: both ledgers.
* Decrease quantities are stored negative.
* Store Transfer pairs the lines: `-qty` at `from_store`, `+qty` at `to_store`, each with the same flag-based physical rule (`auto_gdn` for from, `auto_grn` for to).

**Cost rules** (details in [07](07-unit-cost-engine.md)): Opening Stock writes user cost (`opening_stock` movement, may reset average); Disposal / Adjustment valued at current moving average, falling back to the product's fixed purchase price; back-dated entries trigger `rerun_chain`.

**Linking to a request:** if saved via "Convert" from an Inventory Request (`inventory_request_id`), the request becomes `status = 1`. The edit screen shows the source request number read-only.

**Delete:** reopens the source request (status → 0), deletes the stock rows (matching `stocktype`), the `adj_stock` header and its audit rows, then re-runs the cost chain for the affected product+batch pairs.

**Other helpers:** `check_inventory_transection` (DataTable, date filter `fdate/tdate`), CSV import for opening stock and stock adjustment (`save_openingstock_from_csv`, `save_stockadjustment_from_csv`), `unit_cost_migration` screen to cost legacy opening stock.

---

## 3. Inventory Transaction Request (`inventory_request`)

A request/approval stub with the same line structure as an Inventory Transaction but **no stock effect**.

| | |
|---|---|
| Routes | `new_inventory_request[/id]`, `manage_inventory_request`, `convert_inventory_request/{id}` |
| Tables | `inventory_request` (`request_no` `IR-…`, `status`), `inventory_request_details` (qty signed like adjustments, plus `to_store`) |
| Save / update | `save_inventory_request`, `update_inventory_request` |

**Status:** `0` Open · `1` Converted (an Inventory Transaction was created) · `2` Cancelled.

**Rules**
* Edit and delete are blocked once Converted (`status 1`).
* Cancel → status 2; Redo → back to 0 (`update_inventoryrequeststatuscancel/redo`).
* **Convert** opens the Inventory Transaction form pre-filled; only open requests (`status 0`) can be converted. Saving that transaction sets the request to 1; deleting the transaction re-opens it.
* The "Inventory Transaction Request" dropdown on the transaction form lists open requests.
* Needs `new_inventory_transection` create permission to convert.
