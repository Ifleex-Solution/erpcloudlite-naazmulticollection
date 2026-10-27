# Warehouse Management (sidebar: GRN, GDN)

Module: `stock` (same controller as Stock). Menu group "Warehouse Management" appears if the user has any of `new_grn`, `manage_grn`, `new_gdn`, `manage_gdn`.
GRN/GDN only move **physical stock** (`phystock_details`). Actual stock was already updated by the source document (purchase, sale, return, inventory transaction).

Only stores with **GRN = Enable** (`auto_grn=1`) are offered on GRN, and **GDN = Enable** (`auto_gdn=1`) on GDN; store id 1 ("N/A") is excluded. Non-admin users see only their assigned stores (`sec_store`). See [00-overview.md](00-overview.md).

---

## GRN – Goods Received Note

| | |
|---|---|
| Routes | `new_grn[/id]`, `manage_grn` |
| Permissions | `manage_grn` create / update / read |
| Header | `grn_stock` (`grn_id`, date, detail, `vehicleno`, `type`, `voucherno`, `supplier_id`, `type2`) |
| Lines | `phystock_details` with `type='grn_stock'`, `pid = grn_stock.id`, `pid2 = source line id`, `invoicetype` |
| Save/update/delete | `save_grn`, `update_grn`, `delete_grnstock/{id}` |
| Print | `stock/pos_print` |

**Type (source document)** → `invoicetype`:

| GRN type | invoicetype | Source |
|---|---|---|
| Purchase | 1 | lines of a Purchase (`getPurchaseByVoucherNo`, only purchases with `status = 0`, stock-enabled products, in the chosen store) |
| Sales Return | 2 | lines of a Sales Return |
| Store Transfer | 0 | an Inventory Transaction (store transfer) |

**Flow:** choose type + voucher no → lines load with *ordered qty* and *already received* (`arquatity`, summed from existing GRN lines on `pid2`); user enters the received quantity; save inserts `+qty` physical rows and an `audit_stock` row (`scenario 'GRN'`).

**Effects:** purchases/returns into GRN-enabled stores keep physical stock at 0 until the GRN is posted (see the `auto_grn` rule in Purchase save).
**Delete:** removes the `grn_stock` header, its `phystock_details` and `audit_stock` rows. Purchase deletion is blocked while a GRN references it (`grn_stock.voucherno`/`type='purchase'`).
`is_grnorgdnthere` lets sales/purchase forms check whether a GRN/GDN already exists for a document (to lock editing).

---

## GDN – Goods Dispatch Note

| | |
|---|---|
| Routes | `new_gdn[/id]`, `manage_gdn` |
| Permissions | `manage_gdn` create / update / read |
| Header | `gdn_stock` (`gdn_id`, date, detail, `vehicleno`, `type`, `voucherno`, `customer_id`, `employee_id`, `type2`) |
| Lines | `phystock_details` with `type='gdn_stock'`, negative quantities |
| Save/update/delete | `save_gdn`, `update_gdn`, `delete_gdnstock/{id}` |
| Numbering | 6000000001 (A) / 6600000001 (B), `MAX+1` per type2 |
| Print | `stock/pos_print2` |

**Type options:** Retail (`sale`), Wholesale, Purchase Return, Store Transfer, Stock Disposal.

| GDN type | invoicetype | Source |
|---|---|---|
| Retail / Wholesale | 3 | `sale_details` of the invoice (`getSaleByVoucherNo`) |
| Purchase Return | 4 | `purchase_return_details` (open, status 0) |
| Store Transfer / Stock Disposal | 0 | Inventory Transaction |

Customer and employee (salesman) are captured on the header. Quantity is limited by remaining undispatched quantity per source line (`arquatity`). Saving writes `−qty` into `phystock_details` and an `audit_stock` row (`scenario 'GDN'`).

**Linkage rules elsewhere:** a Sale cannot be deleted or cancelled while a GDN references it (`gdn_stock.voucherno`, `type='sale'`); a Purchase Return cannot be deleted while a GDN exists.
