# Purchase of Product (sidebar: Purchase Order, Purchase, Purchase Return)

Module: `application/modules/purchase` · Controller `Purchase.php` · Model `Purchase_model.php`.
Menu order: **Purchase Order → Purchase → Purchase Return**.

Common header fields: Date, Branch, Supplier, Invoice Type (`cash`, `credit`, `cash_vat`, `credit_vat`), Incident Type (`1` International, `2` Local), Invoice/Chalan No, discount, VAT, totals, details; payment method (`payment_type`) on Purchase.
Line columns: Product, Store, Batch, Unit, Av.Qty, Qty, Price, Discount %, Discount value, VAT, Total. Product-wise `isstock` decides whether stock is affected (service-type items don't). Store "N/A" (id 1) is used for non-stock items.

---

## 1. Purchase Order (`purchase_order`)

| | |
|---|---|
| Routes | `new_purchase_order[/id]`, `manage_purchase_order`, `convert_purchase/{id}` |
| Tables | `purchase_order`, `purchase_order_details` (number from **2000000001**) |
| Methods | `save_purchase_order`, `update_purchase_order`, `delete_purchase_order`, `getPurchaseOrderById` |

No stock or cost effect. **Status:** 0 open · 1 converted to a Purchase · 2 cancelled (`update_purchaseorderstatuscancel/redo`).
**Convert:** `convert_purchase/{id}` opens the Purchase form pre-filled; `save_purchase` then sets the PO `status = 1`. Deleting that purchase sets the PO back to `status = 0`.

---

## 2. Purchase (`purchase`)

| | |
|---|---|
| Routes | `add_purchase`, `purchase_list`, `purchase_edit/{id}`, `purchase_details/{id}` |
| Tables | `purchase`, `purchase_details`; `purchase_order_no` links the PO |
| Methods | `save_purchase`, `update__purchase`, `delete_purchase`, `update_purchasestatus[redo]` |
| Number | 4000000001 (A) / 4400000001 (B) |
| Print | `purchase/pos_print` |

**Save (`save_purchase`), in one transaction**
1. Insert `purchase` header (and mark linked PO status 1).
2. Credit / Credit VAT → insert `supplier_due` row (`+grandTotal`).
3. For each stock item (`isstock=1`): insert `stock_details` (`type='purchase'`, +qty) and an `audit_stock` row (`scenario 'purchase'`, incident "Local/International Purchase").
4. If the line's store has `auto_grn = 0`: also insert `phystock_details` (+qty). Stores with `auto_grn = 1` wait for a GRN ([02](02-warehouse-grn-gdn.md)).
5. Insert `purchase_details`, then `_record_purchase_cost` → moving-average cost update, storing `base_qty`, `net_purchase_unit_cost`, `unit_cost_id` ([07](07-unit-cost-engine.md)).
6. Log entry.

**Edit:** `update__purchase` rewrites the header, deletes and re-inserts stock rows, then re-runs the cost chain from the earlier of old/new date for every affected product+batch.

**Status flag:** the list has approve/redo buttons (`status` 0/1) — a simple acknowledgement flag; GRN lookup only offers purchases with `status = 0`.

**Delete is blocked when** a GRN exists for it, a Purchase Return exists (`purchase_return.purchase_id`), or a Supplier Payment settled it (`supplier_payment_details.purchase_id`). When allowed: reset the PO to open, delete stock/phystock/audit/details/`supplier_due` rows and the header, re-run cost chain.

---

## 3. Purchase Return (`purchase_return`)

| | |
|---|---|
| Routes | `new_purchase_return`, `manage_purchase_return`, `edit_purchase_return/{id}` |
| Tables | `purchase_return`, `purchase_return_details` (number 7000000001 / 7700000001) |
| Methods | `save_purchase_return`, `update_purchase_return`, `delete_purchasereturn`, `getPurchaseReturnById`, `getpurchaseidbybranch` |

Choose Branch → pick an original **Purchase** (`invoice_id`); the dropdown lists only purchases with **no existing return and no supplier payment** (`purchase_id NOT IN purchase_return / supplier_payment_details`). Lines carry return qty and `rstore` (return-from store); header has purchase date and return date (`rdate`).

**Effects**
* Credit / Credit VAT: `supplier_due` row with **negative** grandTotal, linked to both purchase and return.
* Stock: `stock_details` with `type='purchase_return'` (−qty) always; `phystock_details` (−qty) only if the store's `auto_gdn = 0`, otherwise a **GDN of type Purchase Return** takes it out physically.
* Cost: valued at the original purchase line's `net_purchase_unit_cost` (via `invoicedetail`), falling back to current cost; stores `return_unit_cost`, `returned_stock_value`.

**Delete** blocked when a GDN (`type='purchasereturn'`) references it; otherwise removes stock, details, `supplier_due`, audit rows and re-runs the cost chain.

---

## Related endpoints
`bdtask_product_search_by_supplier`, `save_supplier_ajax` (quick-add supplier from the form), `getallcheques` (3rd-party cheques for payment), `pmethod_dropdown` (payment types). Supplier due is settled through [Supplier Payment](05-supplier-payment-customer-receive.md).
