# customer_due & supplier_due – How rows travel

Both tables are **signed ledgers**: every credit event adds a row, and a document's outstanding balance is `SUM(AES_DECRYPT(amount))` of its rows. Nothing is ever "marked paid"; a settlement is simply a negative row. The two tables are mirror images.

| | `customer_due` | `supplier_due` |
|---|---|---|
| Party / branch | `customer`, `branch` | `supplier`, `branch` |
| Document keys | `sale_id`, `service_id`, `sales_return_id`, `customer_receive_id` | `purchase_id`, `purchase_return_id`, `supplier_payment_id` |
| `amount` | AES-encrypted signed number | same |
| `type2` | AES `A`/`B` (live/testing) | same |

Only invoice types **`credit`** and **`credit_vat`** touch these tables. `cash` / `cash_vat` documents never create rows.

## Row lifecycle (customer side; supplier side is identical with the names swapped)

```
Credit Sale ──────────► +grandTotal   (sale_id=S, sales_return_id=0, customer_receive_id=0)
Credit Service ───────► +grandTotal   (service_id=V, sale_id=0)
Sales Return (credit) ► −grandTotal   (sale_id=S, sales_return_id=R)
Customer Receive ─────► −settle       (sale_id=S or service_id=V, customer_receive_id=C)  one row per settled invoice
```

| Event | Where | Row effect |
|---|---|---|
| Credit sale saved | `Invoice::save_sale` | insert `+grandTotal` |
| Credit service saved | `Service::save_service` | insert `+grandTotal` with `service_id` |
| Sales return of a credit sale | `Invoice::save_sales_return` | insert `−grandTotal` (`sale_id` taken from the original sale) |
| Customer Receive saved | `Customer_receive::saveCustomerReceive` | one `−settle` row per invoice row |
| Customer Receive edited | same | all rows with that `customer_receive_id` deleted, then re-inserted |
| Customer Receive deleted | `_voidCustomerReceive` | rows with that `customer_receive_id` deleted (invoice becomes due again) |
| Sale edited | `Invoice::update_sale` | **all** rows for `sale_id` deleted; one fresh `+grandTotal` inserted if still credit |
| Sale cancelled | `cancel_sale` | rows for `sale_id` deleted |
| Sale deleted | `delete_sale` | rows for `sale_id` deleted |
| Sales return deleted | `delete_salesreturn` | rows with that `sales_return_id` deleted |
| Service edited / cancelled / deleted | `Service::update_service`, `cancel_service`, `delete_service` | rows for `service_id` deleted (re-inserted on edit if credit) |

Supplier side:

| Event | Where | Row effect |
|---|---|---|
| Credit purchase saved | `Purchase::save_purchase` | insert `+grandTotal` (`purchase_id`) |
| Purchase return of a credit purchase | `save_purchase_return` | insert `−grandTotal` (`purchase_id`, `purchase_return_id`) |
| Supplier Payment saved/edited/deleted | `Supplier_payment` | `−settle` rows per purchase (`supplier_payment_id`), same replace-on-edit / delete-on-void behaviour |
| Purchase deleted | `delete_purchase` | rows for `purchase_id` deleted |
| Purchase return deleted | `delete_purchasereturn` | rows for `purchase_return_id` deleted |

## Guards that keep the ledger consistent
* A **sale / service** with a Customer Receive cannot be deleted or cancelled (`customer_receive_details`). A sale with a sales return or GDN also cannot.
* A **purchase** with a Supplier Payment, Purchase Return or GRN cannot be deleted. The Purchase Return picker only lists purchases with no return and no payment.
* A **cancelled** sale/service can't be edited, approved or deleted.
* Credit limits (checked at save): `web_setting.sale_credit_limit` and `service_credit_limit` compare the sum of open dues (per `type2`, excluding the document being edited) + the new amount; `0` disables the check. There is no equivalent limit on the supplier side.

## Who reads the ledger
* **Customer Receive** (`getCustomerDueSummary`, `getCustomerDueInvoices`) – per invoice: Invoice Amt = rows with no return and no receive; Returned Amt = `ABS(sum(rows with sales_return_id)`; Paid Amt = `ABS(sum(rows with customer_receive_id))`; Due = `SUM(all rows)`. Only invoices with `HAVING due > 0` are listed. Due date = invoice date + `sale.due_date` days (services have no due date). Branch-scoped for non-admins; "Total Due" intentionally spans all branches.
* **Supplier Payment** (`getSupplierDueSummary`, `getSupplierDuePurchases`) – `SUM(amount)` grouped by `purchase_id`, filtered by supplier (and branch).
* **Reports:** Customer Due Report / Supplier Due Report (`Report::customer_due_reportinvoicewise`, `bdtask_supplier_due_report`), derived entirely from these ledgers.
* Credit-limit checks above.

## Points to verify / known quirks (from code reading, not tested)
1. **Purchase edit does not touch `supplier_due`.** `update__purchase` and `update_purchase_return` contain no `supplier_due` logic, so changing the grand total, invoice type (e.g. credit→cash) or supplier of an existing purchase leaves the original row unchanged. The sale and service edits, by contrast, re-sync. If supplier dues look wrong after an edit, check this first.
2. **Sale edit deletes by `sale_id` only**, which also matches return/receive rows carrying that `sale_id`. It is safe only because a sale with a receive or return cannot be edited/deleted elsewhere – confirm that edit is blocked in the same cases before relying on it.
3. Rows hold the **grand total** (incl. VAT/discount), so settlement is by invoice, not by line.
4. `branch` is stored on each row, but dues are filtered by branch only in the pickers.
