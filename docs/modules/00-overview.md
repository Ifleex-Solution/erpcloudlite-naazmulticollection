# ERP Modules Overview (Stock, Warehouse, Purchase, Sales, Payments, Vouchers)

Reference for how the main transaction sections work. Organised in the **same order as the sidebar** (`application/modules/template/views/includes/sidebar.php`).

| Sidebar group | Menu item | Route | Doc |
|---|---|---|---|
| **Stock** | Stock Batch | `stockbatchlist`, `stockbatch_form` | [01-stock-inventory.md](01-stock-inventory.md) |
| | Inventory Transaction Request | `manage_inventory_request`, `new_inventory_request` | 01 |
| | Inventory Transaction | `manage_inventory_transection`, `new_inventory_transection` | 01 |
| **Warehouse Management** | GRN | `manage_grn`, `new_grn` | [02-warehouse-grn-gdn.md](02-warehouse-grn-gdn.md) |
| | GDN | `manage_gdn`, `new_gdn` | 02 |
| **Purchase of Product** | Purchase Order | `manage_purchase_order`, `new_purchase_order` | [03-purchase.md](03-purchase.md) |
| | Purchase | `purchase_list`, `add_purchase` | 03 |
| | Purchase Return | `manage_purchase_return`, `new_purchase_return` | 03 |
| **Invoice** | Sales Order | `manage_quotation`, `new_quotation` | [04-sales-invoice.md](04-sales-invoice.md) |
| | New Invoice / New POS / New GUI POS | `add_invoice`, `new_pos`, `touch_invoice` | 04 |
| | Manage Invoice | `invoice_list` | 04 |
| | Sales Return | `manage_sales_return`, `new_sales_return` | 04 |
| **Supplier Payment** | New / Manage | `new_supplier_payment`, `manage_supplier_payment` | [05-supplier-payment-customer-receive.md](05-supplier-payment-customer-receive.md) |
| **Customer Receive** | New / Manage | `new_customer_receive`, `manage_customer_receive` | 05 |
| **Vouchers** | Payment/Receipt Type | `payment_receipt_type_list/_form` | [06-vouchers.md](06-vouchers.md) |
| | Payment / Receipt / Contra Voucher | `manage_*_voucher`, `new_*_voucher` | 06 |

| **Service / Sales of Service** | Service, Service Order, Service invoice | `manage_service`, `serviceorder_invoice`, `add_service_invoice` | [09-services.md](09-services.md) |
| Reports → Inventory | Stock Audit Report | `stock_audit_report` | [08-stock-audit.md](08-stock-audit.md) |

Cross-cutting: [07-unit-cost-engine.md](07-unit-cost-engine.md) · row-by-row flows: [10-due-ledgers-flow.md](10-due-ledgers-flow.md) (customer_due / supplier_due) and [11-unit-cost-flow.md](11-unit-cost-flow.md) (unit_cost).
Out of scope here: HRM, other Reports, Settings. (The old `quotation` module is superseded by Sales Order inside `invoice`.)

Code lives in `application/modules/<module>/{controllers,models,views,config/routes.php}`:
`stock`, `store`, `purchase`, `invoice`, `customer_receive`, `supplier_payment`, `account`, `return` (legacy).

## Conventions used everywhere

**Menu pattern.** Each item links to the *manage* (list) page if the user has `read` permission, otherwise to the *new* form (`create`). Permissions are checked with `$this->permission1->method('<slug>', 'create|read|update|delete')->access()`; failing redirects to the previous page.

**Encrypted columns.** Money, quantities, document numbers and `type2` are stored with `AES_ENCRYPT(value, Config::$encryption_key)` and read with `AES_DECRYPT`. Any raw SQL against these tables must decrypt.

**`type2` = Live / Testing (A / B).** Users with `user_level2 == 3` are "testing" users (`type2 = 'B'`); everyone else is `'A'`. Both live in the **same database**; each has its own document-number series and lists are filtered by `type2`. The forms warn "TESTING ENVIRONMENT … connected to the live database".

**Document numbers** (`MAX + 1` per `type2`, else the start value):

| Document | A starts | B starts | Format |
|---|---|---|---|
| Sales Order | 1000000001 | same | number |
| Purchase Order | 2000000001 | same (not split by type2) | number |
| Sale (Invoice) | 3000000001 | 3300000001 | number |
| Purchase | 4000000001 | 4400000001 | number |
| GRN | see 02 | | number |
| GDN | 6000000001 | 6600000001 | number |
| Purchase Return | 7000000001 | 7700000001 | number |
| Sales Return | 8000000001 | 8800000001 | number |
| Voucher (per type) | 1000000001 | 3000000001 | number |
| Inventory Transaction | `INV-0000000001` | `INV-1000000001` | prefixed |
| Inventory Request | `IR-0000000001` | `IR-1000000001` | prefixed |
| Customer Receive | `CR-0000000001` | `CR-1000000001` | prefixed |
| Supplier Payment | `SP-0000000001` | `SP-1000000001` | prefixed |

**Save pattern.** Forms are single pages posting JSON/AJAX to `save_*` / `update_*` controller methods. Saves run inside `trans_begin()/trans_commit()`, roll back on any exception, write a row into `logs (screen, operation, pid, userid, lastupdatedate)` and return print HTML (`details`) shown in a modal. Edits generally **delete and re-insert** the detail/stock rows.

**Delete pattern.** Deletes are blocked when a downstream document references the record (e.g. a Purchase with a GRN, return or payment). Blocked deletes `alert()` and redirect to the list.

## The three stock ledgers

| Table | Meaning |
|---|---|
| `stock_details` | **Actual (book) stock.** Signed quantities (`+` in, `−` out), `type` = source (`purchase`, `sales`, `sales_return`, `purchase_return`, `adj_stock`), `pid` = source header id, keyed by product + batch + store (+ `conversion_id`, `unit`). |
| `phystock_details` | **Physical stock** (what is physically in the store). Same shape; also receives `grn_stock` / `gdn_stock` rows. |
| `audit_stock` | Human-readable audit trail per movement (`scenario`: purchase, sale, GRN, GDN, Inventory Transaction, Purchase Return …; `astock*` = actual, `pstock*` = physical). Deleted along with its voucher. |

Available stock on forms = `SUM(AES_DECRYPT(stock))` per product+batch+store (`stock/avg_avstock` for actual, `stock/avg_phystock` for physical). Sub-units use `conversion_ratio` (`+ − * /`) to show quantities in the selected unit.

### Store flags: GRN / GDN (the key branching rule)

Each **Store** (Store form) has `auto_grn` and `auto_gdn` (labelled **GRN** and **GDN**, Enable/Disable):

* `auto_grn = 0` ("GRN disabled"): receipts into this store update **physical stock immediately** together with actual stock.
* `auto_grn = 1` ("GRN enabled"): receipts update **actual stock only**; physical stock increases only when a **GRN** is entered.
* `auto_gdn` works the same way for issues (sales, purchase returns, disposals, transfers-out) and the **GDN** screen.

Only stores with the flag `= 1` (and id ≠ 1, the "N/A" store) appear in the GRN/GDN store dropdowns. So the Warehouse section is only relevant for stores that require manual physical confirmation.

### Credit tracking
* Credit / Credit VAT **sales** → `customer_due` (+grandTotal); credit sales return → negative row; Customer Receive → negative row.
* Credit / Credit VAT **purchases** → `supplier_due`; purchase return → negative; Supplier Payment → negative.
* No general-ledger posting happens in these flows; vouchers (06) are standalone.

## Branch & Store
* Branch has a **Nature** (Retail / Wholesale) which restricts the incident type on sales (`invoice/getBranchNature`).
* Users see only their assigned branches/stores (`sec_branch`, `sec_store`; admins `user_level2 == 1` see all). Settings → Area Responsibility assigns them.
