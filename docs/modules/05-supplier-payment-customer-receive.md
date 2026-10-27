# Supplier Payment & Customer Receive (sidebar: top-level items)

Both are **settlement vouchers** against credit documents. They write no stock and no ledger entries – they only add negative rows to the due tables.

---

## Customer Receive

Module `customer_receive` · Controller `Customer_receive.php` · Permissions `new_customer_receive`, `manage_customer_receive` (create/read/update/delete).
Routes: `new_customer_receive`, `manage_customer_receive`; AJAX `getCustomerDueSummary`, `getCustomerDueInvoices`, `saveCustomerReceive`, `getManageCustomerReceive`, `getCustomerReceiveForEdit`, `deleteCustomerReceive`, `printCustomerReceive`.

**Form:** Date, Voucher Count, Branch, *Total Branch Due*, Customer, *Total Due*, Payment Type (excludes "Credit"), **Source of Sale** (Sale of Product / Sale of Service – one per voucher).
**Grid (due invoices):** Invoice No, Invoice Date, Due Date, Invoice Amt, Returned Amt, Paid Amt, Due Amt, **Settle Amt**.

**Save (`saveCustomerReceive`)**
1. Validate required fields and that every settle amount > 0.
2. New voucher: `CR-` + 10 digits (A from 1, B from 1000000001). Header in `customer_receive` (`total_amount`, `payment_type`, `type2`).
3. Per row: `customer_receive_details` (`sale_id` *or* `service_id`, `settle_amount`) and a **negative** `customer_due` row (`customer_receive_id`).
4. **Edit** keeps the same id/voucher number, updates the header and recreates detail + due rows.

**Delete:** removes details, due rows and header (voids the settlement; invoices become due again).
Effects elsewhere: an invoice with receipts cannot be deleted or cancelled.

---

## Supplier Payment

Module `supplier_payment` · Controller `Supplier_payment.php` · Permissions `new_supplier_payment`, `manage_supplier_payment`.
Routes: `new_supplier_payment`, `manage_supplier_payment`; AJAX `getSupplierDueSummary`, `getSupplierDuePurchases`, `saveSupplierPayment`, `getManageSupplierPayment`, `getSupplierPaymentForEdit`, `deleteSupplierPayment`, `printSupplierPayment`.

Mirror image of Customer Receive for purchases:
* Voucher `SP-` + 10 digits.
* Header `supplier_payment`; lines `supplier_payment_details (purchase_id, settle_amount)`; each line adds a **negative** `supplier_due` row (`supplier_payment_id`).
* Due per purchase = `SUM(supplier_due.amount)` for credit/credit-VAT purchases (purchase +, purchase return −, payment −), filtered by supplier and branch.
* Payment Type list = active `payment_type` rows excluding "Credit".
* A purchase with payments cannot be deleted, and cannot be selected for a Purchase Return.

Both screens' lists support date filtering, print (`*_print.php`), edit and delete.
