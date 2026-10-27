# Vouchers (sidebar: Vouchers)

Module `account` · Controller `Accounts.php` · Model `Accounts_model.php` · Views `new_voucher.php`, `manage_voucher.php`, `debit_voucher.php`, `credit_voucher.php`, `contra_voucher.php`.
Sidebar items: **Payment/Receipt Type**, **Payment Voucher**, **Receipt Voucher**, **Contra Voucher**.

All three vouchers share one table pair and one form (`new_voucher.php`), selected by `type`.

## 1. Payment/Receipt Type (master)

Routes: `payment_receipt_type_form[/id]`, `payment_receipt_type_list`, delete `delete_payment_receipt_type/{id}`. Table `payment_receipt_type`.
Fields: **Name** (required, ≤200), **Type** (`Payment`, `Receipt`, `Common`), Detail, Status.
These are the "heads" picked on voucher lines: Payment vouchers list types `Payment`+`Common`; Receipt vouchers list `Receipt`+`Common`.

## 2. Voucher header & lines

| Type | Name | Routes (new / manage) | Permission | "From" label | Line "To" label | Line "To" source |
|---|---|---|---|---|---|---|
| 1 | Payment Voucher | `new_payment_voucher` / `manage_payment_voucher` | `manage_payment_voucher` | Paid From | Paid To | Payment-type heads |
| 2 | Receipt Voucher | `new_receipt_voucher` / `manage_receipt_voucher` | `manage_receipt_voucher` | Received To | Received From | Receipt-type heads |
| 3 | Contra Voucher | `new_contra_voucher` / `manage_contra_voucher` | `manage_contra_voucher` | Transferred From | Transferred To | `payment_type` rows |

Tables: `voucher` (`voucher_id`, `branch`, `type`, `from`, `date`, `remark`, `total`, `type2`) and `voucher_details` (`pid`, `to`, `sto`, `comment`, `amount`, `type2`).
**From** (header) = a **Payment Method** (`payment_type`, maintained under Payment Method menu; e.g. Cash, Bank, 3rd party cheque).
Line grid: To, Sub type (`sto`, used e.g. for a selected cheque id), Ledger comment, Amount; total auto-summed.
Selecting a method named *3rd party cheque* opens a modal of held cheques (`getallcheques2`: `cheque` status Valid/Pending, unpaid); *Cash at Bank* shows Cheque No, Effective Date, Draft Date, Description fields.

Number: per type and `type2`, `MAX+1` of `voucher_id`; starts 1000000001 (A) / 3000000001 (B). Branch dropdown from the user's branches.

## 3. Actions

* Save: `account/accounts/save_voucher` (permission `create` for that voucher type; returns print HTML via `_build_voucher_print`). Update: `update_voucher` (permission `update`; replaces all detail rows). Log entry per action.
* Edit: `edit_voucher/{id}` reuses the form; Delete: `delete_voucher/{id}/{type}` removes details + header (no reversal logic; vouchers don't post stock or due).
* List: `voucherList` DataTable filtered by type, branch, date range, type2; `voucherDetails`, print `pos_print_voucher`; bulk approve `bulk_voucher_approve` and `reverseVoucher` exist for the accounting side.

## 4. Relation to the rest
Vouchers are **independent cash/bank records**; they do not change `customer_due`/`supplier_due` (use [Customer Receive / Supplier Payment](05-supplier-payment-customer-receive.md) for that). Reports ("Voucher Report": payment, receipt, contra) and Cash/Day/Bank books read from them. Journal voucher and opening balance also exist in `Accounts.php` but are not in this sidebar group.
