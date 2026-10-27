-- Reduce oversized varbinary column sizes
-- Generated audit: AES_ENCRYPT() output is at most 16 * ceil((plaintext_len+1)/16) bytes,
-- so columns declared as varbinary(500/1000/2000) for short encrypted values (amounts, codes,
-- names, contact fields) waste storage and can force on-disk temp tables for ORDER BY/GROUP BY/
-- DISTINCT/JOIN queries against these tables. Sizes below keep generous safety margins.
-- Review and back up before running against a live database.

SET FOREIGN_KEY_CHECKS=0;

-- adj_stock
ALTER TABLE `adj_stock`
  MODIFY `type2` varbinary(150) NOT NULL;

-- audit_stock
ALTER TABLE `audit_stock`
  MODIFY `pvoucher` varbinary(150) NOT NULL,
  MODIFY `voucher` varbinary(150) NOT NULL,
  MODIFY `astockstr` varbinary(150) NOT NULL,
  MODIFY `pstockstr` varbinary(150) NOT NULL,
  MODIFY `astock` varbinary(100) NOT NULL,
  MODIFY `pstock` varbinary(100) NOT NULL,
  MODIFY `type2` varbinary(150) NOT NULL;

-- branch
ALTER TABLE `branch`
  MODIFY `name` varbinary(200) NOT NULL;

-- bulk_details
ALTER TABLE `bulk_details`
  MODIFY `uploaded_id` varbinary(150) NOT NULL,
  MODIFY `invoices` varbinary(150) NOT NULL;

-- company_information
ALTER TABLE `company_information`
  MODIFY `company_name` varbinary(200) NOT NULL,
  MODIFY `email` varbinary(200) NOT NULL,
  MODIFY `address` varbinary(300) NOT NULL,
  MODIFY `mobile` varbinary(200) NOT NULL,
  MODIFY `website` varbinary(200) NOT NULL,
  MODIFY `vat_no` varbinary(200) DEFAULT NULL,
  MODIFY `cr_no` varbinary(200) DEFAULT NULL,
  MODIFY `footer_text` varbinary(300) NOT NULL,
  MODIFY `password` varbinary(300) NOT NULL,
  MODIFY `header_text` varbinary(300) DEFAULT NULL;

-- customer_information
ALTER TABLE `customer_information`
  MODIFY `customer_name` varbinary(200) DEFAULT NULL,
  MODIFY `customer_address` varbinary(300) DEFAULT NULL,
  MODIFY `address2` varbinary(300) NOT NULL,
  MODIFY `customer_mobile` varbinary(200) DEFAULT NULL,
  MODIFY `customer_email` varbinary(200) DEFAULT NULL,
  MODIFY `email_address` varbinary(200) DEFAULT NULL,
  MODIFY `contact` varbinary(200) DEFAULT NULL,
  MODIFY `phone` varbinary(200) DEFAULT NULL,
  MODIFY `customer_calling_name` varbinary(200) NOT NULL,
  MODIFY `customer_billing_name` varbinary(200) NOT NULL,
  MODIFY `nic_no` varbinary(200) NOT NULL;

-- gdn_stock
ALTER TABLE `gdn_stock`
  MODIFY `gdn_id` varbinary(150) NOT NULL,
  MODIFY `type2` varbinary(150) NOT NULL;

-- grn_stock
ALTER TABLE `grn_stock`
  MODIFY `grn_id` varbinary(150) NOT NULL;

-- phystock_details
ALTER TABLE `phystock_details`
  MODIFY `stock` varbinary(100) NOT NULL;

-- product_group_details
ALTER TABLE `product_group_details`
  MODIFY `qty` varbinary(100) NOT NULL;

-- product_information
ALTER TABLE `product_information`
  MODIFY `price` varbinary(100) DEFAULT NULL,
  MODIFY `cost_price` varbinary(100) NOT NULL;

-- purchase
ALTER TABLE `purchase`
  MODIFY `purchase_id` varbinary(150) NOT NULL,
  MODIFY `discount` varbinary(100) NOT NULL,
  MODIFY `total_discount_ammount` varbinary(100) NOT NULL,
  MODIFY `total_vat_amnt` varbinary(100) NOT NULL,
  MODIFY `grandTotal` varbinary(100) NOT NULL,
  MODIFY `total` varbinary(100) NOT NULL,
  MODIFY `type2` varbinary(150) NOT NULL;

-- purchase_details
ALTER TABLE `purchase_details`
  MODIFY `quantity` varbinary(100) NOT NULL,
  MODIFY `product_rate` varbinary(100) NOT NULL,
  MODIFY `discount` varbinary(100) NOT NULL,
  MODIFY `discount_value` varbinary(100) NOT NULL,
  MODIFY `vat_percent` varbinary(100) NOT NULL,
  MODIFY `vat_value` varbinary(100) NOT NULL,
  MODIFY `total_price` varbinary(100) NOT NULL,
  MODIFY `total_discount` varbinary(100) NOT NULL,
  MODIFY `all_discount` varbinary(100) NOT NULL,
  MODIFY `type2` varbinary(150) NOT NULL;

-- purchase_order
ALTER TABLE `purchase_order`
  MODIFY `purchase_id` varbinary(150) NOT NULL,
  MODIFY `discount` varbinary(100) NOT NULL,
  MODIFY `total_discount_ammount` varbinary(100) NOT NULL,
  MODIFY `total_vat_amnt` varbinary(100) NOT NULL,
  MODIFY `grandTotal` varbinary(100) NOT NULL,
  MODIFY `total` varbinary(100) NOT NULL,
  MODIFY `type2` varbinary(150) NOT NULL;

-- purchase_order_details
ALTER TABLE `purchase_order_details`
  MODIFY `quantity` varbinary(100) NOT NULL,
  MODIFY `product_rate` varbinary(100) NOT NULL,
  MODIFY `discount` varbinary(100) NOT NULL,
  MODIFY `discount_value` varbinary(100) NOT NULL,
  MODIFY `vat_percent` varbinary(100) NOT NULL,
  MODIFY `vat_value` varbinary(100) NOT NULL,
  MODIFY `total_price` varbinary(100) NOT NULL,
  MODIFY `total_discount` varbinary(100) NOT NULL,
  MODIFY `all_discount` varbinary(100) NOT NULL,
  MODIFY `type2` varbinary(150) NOT NULL;

-- purchase_return
ALTER TABLE `purchase_return`
  MODIFY `purchase_return_id` varbinary(150) NOT NULL,
  MODIFY `discount` varbinary(100) NOT NULL,
  MODIFY `total_discount_ammount` varbinary(100) NOT NULL,
  MODIFY `total_vat_amnt` varbinary(100) NOT NULL,
  MODIFY `grandTotal` varbinary(100) NOT NULL,
  MODIFY `total` varbinary(100) NOT NULL,
  MODIFY `type2` varbinary(150) NOT NULL;

-- purchase_return_details
ALTER TABLE `purchase_return_details`
  MODIFY `quantity` varbinary(100) NOT NULL,
  MODIFY `product_rate` varbinary(100) NOT NULL,
  MODIFY `discount` varbinary(100) NOT NULL,
  MODIFY `discount_value` varbinary(100) NOT NULL,
  MODIFY `vat_percent` varbinary(100) NOT NULL,
  MODIFY `vat_value` varbinary(100) NOT NULL,
  MODIFY `rqty` varbinary(100) NOT NULL,
  MODIFY `rdeduction` varbinary(100) NOT NULL,
  MODIFY `total_price` varbinary(100) NOT NULL,
  MODIFY `total_discount` varbinary(100) NOT NULL,
  MODIFY `all_discount` varbinary(100) NOT NULL,
  MODIFY `type2` varbinary(150) NOT NULL;

-- sale
ALTER TABLE `sale`
  MODIFY `sale_id` varbinary(150) NOT NULL,
  MODIFY `discount` varbinary(100) NOT NULL,
  MODIFY `total_discount_ammount` varbinary(100) NOT NULL,
  MODIFY `total_vat_amnt` varbinary(100) NOT NULL,
  MODIFY `grandTotal` varbinary(100) NOT NULL,
  MODIFY `total` varbinary(100) NOT NULL,
  MODIFY `type2` varbinary(150) NOT NULL;

-- sale_details
ALTER TABLE `sale_details`
  MODIFY `quantity` varbinary(100) NOT NULL,
  MODIFY `product_rate` varbinary(100) NOT NULL,
  MODIFY `discount` varbinary(100) NOT NULL,
  MODIFY `discount_value` varbinary(100) NOT NULL,
  MODIFY `vat_percent` varbinary(100) NOT NULL,
  MODIFY `vat_value` varbinary(100) NOT NULL,
  MODIFY `total_price` varbinary(100) NOT NULL,
  MODIFY `total_discount` varbinary(100) NOT NULL,
  MODIFY `all_discount` varbinary(100) NOT NULL,
  MODIFY `type2` varbinary(150) NOT NULL;

-- sale_group_details
ALTER TABLE `sale_group_details`
  MODIFY `quantity` varbinary(100) NOT NULL,
  MODIFY `product_rate` varbinary(100) NOT NULL,
  MODIFY `discount` varbinary(100) NOT NULL,
  MODIFY `discount_value` varbinary(100) NOT NULL,
  MODIFY `vat_percent` varbinary(100) NOT NULL,
  MODIFY `vat_value` varbinary(100) NOT NULL,
  MODIFY `total_price` varbinary(100) NOT NULL,
  MODIFY `total_discount` varbinary(100) NOT NULL,
  MODIFY `all_discount` varbinary(100) NOT NULL,
  MODIFY `type2` varbinary(150) NOT NULL;

-- sales_order
ALTER TABLE `sales_order`
  MODIFY `sale_id` varbinary(150) NOT NULL,
  MODIFY `discount` varbinary(100) NOT NULL,
  MODIFY `total_discount_ammount` varbinary(100) NOT NULL,
  MODIFY `total_vat_amnt` varbinary(100) NOT NULL,
  MODIFY `grandTotal` varbinary(100) NOT NULL,
  MODIFY `total` varbinary(100) NOT NULL,
  MODIFY `type2` varbinary(150) NOT NULL;

-- sales_order_details
ALTER TABLE `sales_order_details`
  MODIFY `quantity` varbinary(100) NOT NULL,
  MODIFY `product_rate` varbinary(100) NOT NULL,
  MODIFY `discount` varbinary(100) NOT NULL,
  MODIFY `discount_value` varbinary(100) NOT NULL,
  MODIFY `vat_percent` varbinary(100) NOT NULL,
  MODIFY `vat_value` varbinary(100) NOT NULL,
  MODIFY `total_price` varbinary(100) NOT NULL,
  MODIFY `total_discount` varbinary(100) NOT NULL,
  MODIFY `all_discount` varbinary(100) NOT NULL,
  MODIFY `type2` varbinary(150) NOT NULL;

-- sales_order_group_details
ALTER TABLE `sales_order_group_details`
  MODIFY `quantity` varbinary(100) NOT NULL,
  MODIFY `product_rate` varbinary(100) NOT NULL,
  MODIFY `discount` varbinary(100) NOT NULL,
  MODIFY `discount_value` varbinary(100) NOT NULL,
  MODIFY `vat_percent` varbinary(100) NOT NULL,
  MODIFY `vat_value` varbinary(100) NOT NULL,
  MODIFY `total_price` varbinary(100) NOT NULL,
  MODIFY `total_discount` varbinary(100) NOT NULL,
  MODIFY `all_discount` varbinary(100) NOT NULL,
  MODIFY `type2` varbinary(150) NOT NULL;

-- sales_return
ALTER TABLE `sales_return`
  MODIFY `sales_return_id` varbinary(150) NOT NULL,
  MODIFY `discount` varbinary(100) NOT NULL,
  MODIFY `total_discount_ammount` varbinary(100) NOT NULL,
  MODIFY `total_vat_amnt` varbinary(100) NOT NULL,
  MODIFY `grandTotal` varbinary(100) NOT NULL,
  MODIFY `total` varbinary(100) NOT NULL,
  MODIFY `type2` varbinary(150) NOT NULL;

-- sales_return_details
ALTER TABLE `sales_return_details`
  MODIFY `quantity` varbinary(100) NOT NULL,
  MODIFY `product_rate` varbinary(100) NOT NULL,
  MODIFY `discount` varbinary(100) NOT NULL,
  MODIFY `discount_value` varbinary(100) NOT NULL,
  MODIFY `vat_percent` varbinary(100) NOT NULL,
  MODIFY `vat_value` varbinary(100) NOT NULL,
  MODIFY `rqty` varbinary(100) NOT NULL,
  MODIFY `rdeduction` varbinary(100) NOT NULL,
  MODIFY `total_price` varbinary(100) NOT NULL,
  MODIFY `total_discount` varbinary(100) NOT NULL,
  MODIFY `all_discount` varbinary(100) NOT NULL,
  MODIFY `type2` varbinary(150) NOT NULL;

-- service
ALTER TABLE `service`
  MODIFY `service_id` varbinary(150) NOT NULL,
  MODIFY `discount` varbinary(100) NOT NULL,
  MODIFY `total_discount_ammount` varbinary(100) NOT NULL,
  MODIFY `total_vat_amnt` varbinary(100) NOT NULL,
  MODIFY `grandTotal` varbinary(100) NOT NULL,
  MODIFY `total` varbinary(100) NOT NULL,
  MODIFY `type2` varbinary(150) NOT NULL;

-- service_details
ALTER TABLE `service_details`
  MODIFY `quantity` varbinary(100) NOT NULL,
  MODIFY `product_rate` varbinary(100) NOT NULL,
  MODIFY `discount` varbinary(100) NOT NULL,
  MODIFY `discount_value` varbinary(100) NOT NULL,
  MODIFY `vat_percent` varbinary(100) NOT NULL,
  MODIFY `vat_value` varbinary(100) NOT NULL,
  MODIFY `total_price` varbinary(100) NOT NULL,
  MODIFY `total_discount` varbinary(100) NOT NULL,
  MODIFY `all_discount` varbinary(100) NOT NULL,
  MODIFY `type2` varbinary(150) NOT NULL;

-- service_order
ALTER TABLE `service_order`
  MODIFY `service_order_id` varbinary(150) NOT NULL,
  MODIFY `discount` varbinary(100) NOT NULL,
  MODIFY `total_discount_ammount` varbinary(100) NOT NULL,
  MODIFY `total_vat_amnt` varbinary(100) NOT NULL,
  MODIFY `grandTotal` varbinary(100) NOT NULL,
  MODIFY `total` varbinary(100) NOT NULL,
  MODIFY `type2` varbinary(150) NOT NULL;

-- service_order_details
ALTER TABLE `service_order_details`
  MODIFY `quantity` varbinary(100) NOT NULL,
  MODIFY `product_rate` varbinary(100) NOT NULL,
  MODIFY `discount` varbinary(100) NOT NULL,
  MODIFY `discount_value` varbinary(100) NOT NULL,
  MODIFY `vat_percent` varbinary(100) NOT NULL,
  MODIFY `vat_value` varbinary(100) NOT NULL,
  MODIFY `total_price` varbinary(100) NOT NULL,
  MODIFY `total_discount` varbinary(100) NOT NULL,
  MODIFY `all_discount` varbinary(100) NOT NULL,
  MODIFY `type2` varbinary(150) NOT NULL;

-- stock_details
ALTER TABLE `stock_details`
  MODIFY `stock` varbinary(100) NOT NULL;

-- stockbatch
ALTER TABLE `stockbatch`
  MODIFY `batchid` varbinary(150) NOT NULL,
  MODIFY `mrp` varbinary(100) NOT NULL;

-- stockbatchopening
ALTER TABLE `stockbatchopening`
  MODIFY `mrp` varbinary(100) NOT NULL;

-- subunit_product
ALTER TABLE `subunit_product`
  MODIFY `subsell_price` varbinary(100) NOT NULL,
  MODIFY `subcost_price` varbinary(100) NOT NULL;

-- supplier_information
ALTER TABLE `supplier_information`
  MODIFY `supplier_name` varbinary(200) NOT NULL,
  MODIFY `address` varbinary(300) DEFAULT NULL,
  MODIFY `address2` varbinary(300) NOT NULL,
  MODIFY `mobile` varbinary(200) DEFAULT NULL,
  MODIFY `emailnumber` varbinary(200) DEFAULT NULL,
  MODIFY `email_address` varbinary(200) DEFAULT NULL,
  MODIFY `contact` varbinary(200) DEFAULT NULL,
  MODIFY `phone` varbinary(200) DEFAULT NULL,
  MODIFY `supplier_calling_name` varbinary(200) NOT NULL,
  MODIFY `supplier_billing_name` varbinary(200) NOT NULL,
  MODIFY `nic_no` varbinary(200) NOT NULL;

-- voucher
ALTER TABLE `voucher`
  MODIFY `voucher_id` varbinary(150) NOT NULL,
  MODIFY `total` varbinary(100) NOT NULL,
  MODIFY `type2` varbinary(150) NOT NULL;

-- voucher_details
ALTER TABLE `voucher_details`
  MODIFY `amount` varbinary(100) NOT NULL,
  MODIFY `type2` varbinary(150) NOT NULL;

SET FOREIGN_KEY_CHECKS=1;
