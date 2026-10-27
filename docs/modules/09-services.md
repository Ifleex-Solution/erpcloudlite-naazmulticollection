# Services (sidebar: Service master + Sales of Service)

Module `application/modules/service` · Controller `Service.php` · Model `Service_model.php`.
Service sales are the **non-stock twin of product sales** ([04-sales-invoice.md](04-sales-invoice.md)): same header fields, invoice types and credit handling, but **no stock, batch, store or unit-cost effect**.

| Sidebar | Route | Permission slug |
|---|---|---|
| **Service** (top-level master) | `add_service`, `manage_service`, `edit_service/{id}` | `create_service`, `manage_service` |
| Sales of Service → **Service Order** | `serviceorder_invoice[/…]`, `manage_serviceorder_invoice`, `edit_serviceorder_invoice/{id}`, `convert_service/{id}` | `serviceorder_invoice`, `manage_serviceorder_invoice` |
| Sales of Service → **Service** (invoice) | `add_service_invoice`, `manage_service_invoice`, `edit_service_invoice/{id}`, `service_details/{id}` | `service_invoice`, `manage_service_invoice` |

---

## 1. Service master (`product_service`)

Fields: Service ID/code (unique), Service Name, **Charge**, VAT %, Description, Status (Active/Inactive); optional extra tax columns `tax0…taxN` (stored as percent/100) driven by Tax settings.
* Duplicate check: `check_duplicate_service`; quick-add from invoice forms: `insert_service_quick`; CSV import: `save_service_from_csv` / `uploadCsv_service`.
* **Delete** only if the service is not used in `service_details`; otherwise "already referenced" alert.
* Only Active services are offered on invoices (`service_model->service_list`).

## 2. Service Order (`service_order`)

Tables `service_order`, `service_order_details`. Number from **1000000001** (single series, not split by type2).
Methods: `save_service_order`, `update_serviceorder`, `delete_serviceorder`, `getServiceOrderById`, `getservicesorderidbybranch`.
No due or financial effect. **Status** 0 open · 1 converted to invoice · 2 cancelled (`update_serviceeorderstatuscancel`, `update_serviceorderstatusredo`). **Convert** (`convert_service/{id}`) opens the Service invoice form pre-filled (`pagetype = convert`); saving marks the order 1, and deleting that invoice re-opens it (status 0).

## 3. Service invoice (`service`)

Tables `service` (header) and `service_details` (lines: `service`, quantity, rate, discount %/value, VAT %/value, totals). Same view is shared for add/edit/convert (`add_invoice_form.php`).
Number: `service_id` **1000000001** (A) / **3000000001** (B), `MAX+1` per type2.

Header: Date, **EOD Date** (`eod_date`, expected delivery/completion), Branch, Customer (quick-add via `invoice/save_customer`), Salesman (`employee_id`), Invoice Type (`cash`, `credit`, `cash_vat`, `credit_vat`), payment method, Service Order (loaded by branch), discount, VAT, totals, details.
Line grid: Service, Qty, Price, Discount, Dis.val, VAT, VAT.val, Total.

**Save (`save_service`)**
1. Credit / Credit VAT: reject when `web_setting.service_credit_limit` would be exceeded by open service dues of that type2 (limit 0 = unlimited). This limit is separate from the product sale credit limit.
2. Insert `service` (linked `service_order_id`; order set to status 1).
3. Credit invoices: `customer_due` row with `service_id` (+grandTotal), `sale_id = 0`.
4. Insert `service_details`; log; return print HTML (`service/pos_print`, with terms & conditions).

**Edit:** `update_service` (loads via `getServiceById`).
**Cancel (`cancel_service`)** – needs `manage_service_invoice` update permission: sets `status = 2`, **zeroes** all amounts on header and lines (so the record stays but is worth 0), removes the `customer_due` rows. Refused if already cancelled or a Customer Receive settled it.
**Delete (`delete_service`)** – blocked if cancelled or a Customer Receive settled it; otherwise re-opens the source order, deletes lines, dues and header.
Other: invoice view/PDF/mail (`service_invoice_view`, `invoice_pdf_generate`, `servicedetails_download`, `setmail`), print `service_print`, `service_order_print`.

## 4. Settlement
Service dues are collected in **Customer Receive** by choosing **Source of Sale = Sale of Service**; details are stored with `service_id` (a receive handles either products or services, never both). See [05](05-supplier-payment-customer-receive.md).

## Differences from product sales
| | Product sale | Service invoice |
|---|---|---|
| Stock / phystock / audit rows | yes | none |
| Unit cost, batch, store | yes | none |
| GDN / sales return | yes | none (no return screen) |
| Incident type Retail/Wholesale | yes | not used |
| Credit limit setting | `sale_credit_limit` | `service_credit_limit` |
| Cancel | status 2, stock reversed | status 2, amounts zeroed |
| Number series | 3000000001 / 3300000001 | 1000000001 / 3000000001 |
