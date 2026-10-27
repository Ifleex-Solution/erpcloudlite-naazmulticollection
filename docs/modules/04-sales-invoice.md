# Invoice / Sales (sidebar: Sales Order, New Invoice, New POS, New GUI POS, Manage Invoice, Sales Return)

Module: `application/modules/invoice` · Controller `Invoice.php` · Model `Invoice_model.php`.
(The separate `quotation` module is legacy; the sidebar "Sales Order" uses `invoice`'s `new_quotation` / `manage_quotation` screens.)

Common header fields: Date, Branch, Customer, Salesman (`employee_id`), Invoice Type (`cash`, `credit`, `cash_vat`, `credit_vat`), Incident Type (**Retail / Wholesale**, restricted by the branch's Nature), Due Days / `due_date` (credit), payment method, discount, VAT, totals, details.
Product search (`getProductByName`, product groups via `getProductGroupByName`), batch selection (`getBatchInStockByProductAndBatchtype`), sub-units, price by batch (`getBatchPrice`) and available stock (`avg_avstock`).

---

## 1. Sales Order (`sales_order`)

| | |
|---|---|
| Routes | `new_quotation[/id]` (`edit_quotation/{id}`), `manage_quotation`, `convert_sale/{id}` |
| Tables | `sales_order`, `sales_order_details`, `sales_order_group_details` (number from **1000000001**) |
| Methods | `save_salesorder`, `update_salesorder`, `delete_salesorder`, `getSalesOrderById`, `getsalesorderidbybranch` |

No stock, due or cost effect. **Status:** 0 open · 1 converted to an invoice · 2 cancelled (`update_saleseorderstatuscancel`, `update_salesorderstatusredo`). Convert opens the invoice form pre-filled (`sales_order_no`); saving the sale sets the order to 1, deleting that sale re-opens it.

---

## 2. New Invoice / New POS / New GUI POS → `sale`

Three front-ends, one back end (`save_sale` / `update_sale`).

| Screen | Route | View | Notes |
|---|---|---|---|
| New Invoice | `add_invoice` | `add_invoice_form.php` | Full form; can load a Sales Order; group (bundle) items; cancel button. |
| New POS | `new_pos` (`edit_invoice2/{id}`) | `new_pos.php` | Fast line entry; Sales / Whole Sale toggle. |
| New GUI POS | `touch_invoice[/id]` | `touch_invoice_form.php` | Touch UI with product tiles (`ti_search_products`), quick product add (`save_product_ajax`), Salesman, Store/Batch/Unit per line. |

Tables: `sale`, `sale_details` (+ `sale_group_details`). Number **3000000001** (A) / **3300000001** (B).
Printing follows web setting `pos_print_type`: normal `pos_print`, thermal `pos_thermal_print`, dot-matrix `pos_print_dotmatrix`; plus invoice HTML/PDF/pad print/delivery note.

**Save (`save_sale`), one transaction**
1. Credit invoices: reject if the company **sale credit limit** (`web_setting.sale_credit_limit`, sum of open `customer_due` for that type2) would be exceeded.
2. Insert `sale`; mark linked Sales Order status 1.
3. Credit / Credit VAT → `customer_due` row (`+grandTotal`).
4. Per stock item: `stock_details` (`type='sales'`, −qty) + `audit_stock` (incident "Retail"/"Wholesale"); if the store has `auto_gdn = 0`, also `phystock_details` (−qty). Stores with GDN enabled are physically relieved by a GDN ([02](02-warehouse-grn-gdn.md)).
5. Insert `sale_details`, then `_record_sale_cost` stores `line_unit_cost`, `line_cost_value` at the current moving average (stock out does not change the average).
6. Group items → `sale_group_details`. Log entry. Returns print HTML.

**Status (list buttons):** 0 normal · 1 approved/acknowledged (`update_salestatus[redo]`) · **2 cancelled**.

**Cancel (`cancel_sale`)**: sets status 2, removes stock and `customer_due`, re-runs cost chain. Refused if already cancelled, or a GDN, sales return or Customer Receive already references the invoice. **Cancelled invoices cannot be deleted, approved or redone.**
**Delete (`delete_sale`)**: same blockers (GDN, sales return, `customer_receive_details`).
**Edit (`update_sale`)** rebuilds stock/due rows and re-costs from the earlier date.

---

## 3. Manage Invoice (`invoice_list`)
DataTable (`CheckInvoiceList`) with date/branch filter and per-row actions: view (`invoice_details/{id}`), edit, print variants, delivery note, status approve/redo, cancel, delete, PDF/mail.

---

## 4. Sales Return (`sales_return`)

| | |
|---|---|
| Routes | `new_sales_return`, `manage_sales_return`, `edit_sales_return/{id}` |
| Tables | `sales_return`, `sales_return_details` (number **8000000001** / 8800000001) |
| Methods | `save_sales_return`, `update_sales_return`, `delete_salesreturn`, `getSalesReturnById`, `getsalesidbybranch` |

Pick Branch → original **Invoice Id** (`sales_id`); invoice lines load via `getSaleById`; user enters return qty, return date (`rdate`) and **return store** (`rstore`, selectable per line, default shown "Default").

**Effects**
* Stock: `stock_details` `type='sales_return'` (+qty); `phystock_details` (+qty) only if the store has `auto_grn = 0`, else a **GRN of type Sales Return** adds it physically.
* Credit / Credit VAT: `customer_due` row with **negative** total (references the sale and return).
* Cost: valued at the original sale line's `line_unit_cost` (via `invoicedetail`), fallback to current cost; stored as `return_unit_cost`, `returned_cost_value`; adds quantity back at that cost.
* Delete/update re-run the cost chain; deletion is blocked when a GRN references the return.

---

## Dues
Credit sales create `customer_due` rows; they are settled via [Customer Receive](05-supplier-payment-customer-receive.md). Net due per invoice = invoice − returns − receipts.
