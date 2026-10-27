# Stock Audit (Reports → Inventory Reports → Stock Audit Report)

Module `report` · Controller `Report.php` (`bdtask_stock_audit_report`, `audit_stock_report`, `audit_stock_report_sync`, `set_audit_session`, `generate_auditreport`) · View `report/views/stock_audit_report.php`.
Route `stock_audit_report` · Permission `stock_audit_report` (read). Sidebar: Reports → *Inventory Reports* → Stock Audit Report.

The report is a **movement-by-movement trail of one product** read from the `audit_stock` table, showing both the actual (book) and physical effect of every document. It is what lets you trace *why* a product's actual and physical balances differ. Ledger background: [00-overview.md](00-overview.md).

## Where audit rows come from

Every stock-affecting save writes rows into `audit_stock` in the same transaction; deleting the document deletes its audit rows (`pid` + `scenario`).

| `scenario` | Written by | `incident` value | Notes |
|---|---|---|---|
| `purchase` | Purchase save | Local Purchase / International Purchase | One row per stock line for actual; another row for physical when store `auto_grn = 0`. |
| `sale` | Invoice save | Retail / Wholesale | Same actual + physical (`auto_gdn = 0`) pattern. |
| `Purchase Return` | Purchase Return save | purchase-return incident | |
| Sales return | Sales Return save | Sales Return | |
| `Inventory Transaction` | Inventory Transaction save | `openingstock`, `storetransfer`, `stockdisposal`, `stockadjustment` | Actual rows fill `astock*`; physical rows fill `pstock*`. |
| `GRN` | GRN save | GRN type (purchase / salesreturn / storetransfer) | Physical only. |
| `GDN` | GDN save | GDN type | Physical only (negative). |

Columns (encrypted unless noted): `product`, `batch`, `date` (document date), `scenario`, `incident`, `pvoucher` (parent voucher, e.g. the purchase/sale a GRN/GDN refers to), `voucher` (document no.), `pid` (source header id), `store`, `astockstr` / `pstockstr` (quantity as typed, in the entered unit, e.g. `-5`), `astock` / `pstock` (quantity in master unit, signed), `lastupdateddate`, `type2`.
An actual-stock row has `astock*` filled and `pstock*` empty; a physical-stock row is the reverse, so each document usually produces one of each.

## The screen

Filters:
* **Product** — required.
* **Date** — From required; To required unless **Single Date** is ticked (then To = From).
* **Store** — the user's stores (`store/getstorebyuserid`).
* **Scenario** — Purchase Invoice, Sale Invoice, Purchase Return, Stock (= Inventory Transaction), GRN, GDN.
* **Incident** — list changes with the chosen scenario (e.g. Purchase → Local/International Purchase; Stock → Opening Stock, Store Transfer, Stock Disposal, Stock Adjustment; Sale → Sale/Whole Sale …).
* **Password** — required. The check is client-side and hard-coded in the page JavaScript (`audit123`), so treat it as a UI gate only, not security.
* **Sync** button — runs stored procedure `sp_insert_stock_audit(encryption_key)` to (re)build/backfill `audit_stock` from the transactional tables. The procedure is **not in this repository**; it lives in the database.
* Buttons: **PDF** and **Excel**. If no rows match: "No records found for the selected filter."

## Query (`audit_stock_report`)

`audit_stock` joined to `product_information`, `store`, `stockbatch` (batch name; empty for batch 0), the product's first sub-unit and `conversion_ratio`. Filters on `date` between the dates, plus product / store / incident / scenario (scenario keys map: `purchaseinvoice→purchase`, `saleinvoice→sale`, `stock→Inventory Transaction`, `GRN`, `GDN`, `purchasereturn→Purchase Return`). Sorted by `lastupdateddate` ascending (time saved, not document date). Also returns the master unit and sub-units of the product.

> Note: there is no opening balance row – totals are the sum of the movements inside the selected date range only, not the closing stock. Branch is not filtered, and `type2` (live/testing) is not applied.

## Output columns

Date · Scenario (`purchase`→"Purchase Invoice", `sale`→"Sale Invoice") · Incident (mapped to a friendly name) · Parent Voucher · Voucher No · Store · Batch · **Actual Stock** (as entered, e.g. `2 Box 3 pcs`) · **Physical Stock** · **Actual Stock (Std.)** · **Physical Stock (Std.)** (master-unit decimals).
Final row **Available Quantity**: totals of the std. columns, plus the same totals expressed as master + sub units via `convertmasterstock(...)` using the conversion ratio.

* **PDF:** the page posts the rows to `set_audit_session` (stored in `$_SESSION['audit_report']`) and opens `generate_auditreport` in a new tab. TCPDF, A4 landscape, company header, product name, master unit and sub-unit/ratio lines. Fails with 400 "No audit data received" if the session is empty.
* **Excel:** built client-side (`generateAuditExcel`) from the same JSON.

## How to read it
* Actual total ≠ physical total → some documents are awaiting a GRN/GDN (stores with GRN/GDN enabled), or a physical-only adjustment was made.
* A purchase with no later GRN shows only the actual row until the GRN appears.
* Deleted/edited documents vanish and reappear (edits delete and re-insert audit rows), so ordering is by `lastupdateddate`, not by `date`.
