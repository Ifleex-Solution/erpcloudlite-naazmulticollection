-- Unit Cost Logic Specification v1.1 — Phase 0 (schema only).
-- New unit_cost table (Section 9) plus system cost columns on the transaction-detail
-- tables that will carry them (Section 8.2).
-- Every money/quantity-bearing column follows this codebase's existing convention
-- (see purchase_details.quantity, stock_details.stock, product_information.cost_price):
-- stored as AES_ENCRYPT'd varbinary(100), decrypted via AES_DECRYPT on read with the
-- shared key in Config::$encryption_key. Only id/FK-like and non-sensitive columns
-- (product, batch, save_seq, source_type, source_no, line_id, branch, store, unit_cost_id,
-- source_detail_id, date, created_at, recalculated_at) stay plain.
-- Safe to re-run: every statement is existence-guarded.

CREATE TABLE IF NOT EXISTS `unit_cost` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product` int(11) NOT NULL,
  `batch` int(11) NOT NULL,
  `date` date NOT NULL,
  `save_seq` int(11) NOT NULL DEFAULT 0,
  `source_type` varchar(50) NOT NULL,
  `source_no` int(11) NOT NULL,
  `line_id` int(11) NOT NULL,
  `branch` int(11) NOT NULL DEFAULT 0,
  `store` int(11) NOT NULL DEFAULT 0,
  `qty_in` varbinary(100) NOT NULL,
  `qty_out` varbinary(100) NOT NULL,
  `value_in` varbinary(100) NOT NULL,
  `value_out` varbinary(100) NOT NULL,
  `unit_cost_before` varbinary(100) NOT NULL,
  `unit_cost_after` varbinary(100) NOT NULL,
  `available_qty_after` varbinary(100) NOT NULL,
  `available_value_after` varbinary(100) NOT NULL,
  `created_at` datetime NOT NULL,
  `recalculated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_batch_date` (`product`, `batch`, `date`, `save_seq`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- purchase_details
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchase_details' AND COLUMN_NAME = 'base_qty');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE purchase_details ADD COLUMN base_qty VARBINARY(100) NOT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchase_details' AND COLUMN_NAME = 'net_purchase_unit_cost');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE purchase_details ADD COLUMN net_purchase_unit_cost VARBINARY(100) NOT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchase_details' AND COLUMN_NAME = 'unit_cost_id');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE purchase_details ADD COLUMN unit_cost_id INT(11) NOT NULL DEFAULT 0', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- purchase_return_details
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchase_return_details' AND COLUMN_NAME = 'return_unit_cost');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE purchase_return_details ADD COLUMN return_unit_cost VARBINARY(100) NOT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchase_return_details' AND COLUMN_NAME = 'returned_stock_value');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE purchase_return_details ADD COLUMN returned_stock_value VARBINARY(100) NOT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchase_return_details' AND COLUMN_NAME = 'unit_cost_id');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE purchase_return_details ADD COLUMN unit_cost_id INT(11) NOT NULL DEFAULT 0', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- sale_details
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sale_details' AND COLUMN_NAME = 'base_qty');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE sale_details ADD COLUMN base_qty VARBINARY(100) NOT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sale_details' AND COLUMN_NAME = 'line_unit_cost');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE sale_details ADD COLUMN line_unit_cost VARBINARY(100) NOT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sale_details' AND COLUMN_NAME = 'line_cost_value');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE sale_details ADD COLUMN line_cost_value VARBINARY(100) NOT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sale_details' AND COLUMN_NAME = 'unit_cost_id');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE sale_details ADD COLUMN unit_cost_id INT(11) NOT NULL DEFAULT 0', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- sales_return_details
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales_return_details' AND COLUMN_NAME = 'return_unit_cost');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE sales_return_details ADD COLUMN return_unit_cost VARBINARY(100) NOT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales_return_details' AND COLUMN_NAME = 'returned_cost_value');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE sales_return_details ADD COLUMN returned_cost_value VARBINARY(100) NOT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales_return_details' AND COLUMN_NAME = 'unit_cost_id');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE sales_return_details ADD COLUMN unit_cost_id INT(11) NOT NULL DEFAULT 0', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- stock_details (Opening Stock / Stock Adjustment Actual / Stock Disposal lines only —
-- irrelevant for Store Transfer / GRN-GDN-sourced rows, left at their defaults there)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'stock_details' AND COLUMN_NAME = 'unit_cost');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE stock_details ADD COLUMN unit_cost VARBINARY(100) NOT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'stock_details' AND COLUMN_NAME = 'value');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE stock_details ADD COLUMN value VARBINARY(100) NOT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'stock_details' AND COLUMN_NAME = 'unit_cost_id');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE stock_details ADD COLUMN unit_cost_id INT(11) NOT NULL DEFAULT 0', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Phase 5 addition: persists which original purchase_details / sale_details line a return was
-- made against. Needed so chain re-run (UCL-50) can re-resolve the return's cost if that
-- original line's own cost changes during the same re-run (it's earlier in the chain).
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchase_return_details' AND COLUMN_NAME = 'source_detail_id');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE purchase_return_details ADD COLUMN source_detail_id INT(11) NOT NULL DEFAULT 0', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales_return_details' AND COLUMN_NAME = 'source_detail_id');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE sales_return_details ADD COLUMN source_detail_id INT(11) NOT NULL DEFAULT 0', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Encryption retrofit: if this migration was already applied with the old plain-DECIMAL
-- columns (pre-encryption build), convert them to varbinary now. Existing decimal data is
-- dropped (DEFAULT 0 -> empty/unencrypted), which is safe pre-go-live since every Phase 1-5
-- verification run used scratch test rows that were cleaned up afterwards, never real data.
SET @col_type = (SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'unit_cost' AND COLUMN_NAME = 'qty_in');
SET @sql = IF(@col_type LIKE 'decimal%', 'ALTER TABLE unit_cost
    MODIFY COLUMN qty_in VARBINARY(100) NOT NULL,
    MODIFY COLUMN qty_out VARBINARY(100) NOT NULL,
    MODIFY COLUMN value_in VARBINARY(100) NOT NULL,
    MODIFY COLUMN value_out VARBINARY(100) NOT NULL,
    MODIFY COLUMN unit_cost_before VARBINARY(100) NOT NULL,
    MODIFY COLUMN unit_cost_after VARBINARY(100) NOT NULL,
    MODIFY COLUMN available_qty_after VARBINARY(100) NOT NULL,
    MODIFY COLUMN available_value_after VARBINARY(100) NOT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_type = (SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchase_details' AND COLUMN_NAME = 'base_qty');
SET @sql = IF(@col_type LIKE 'decimal%', 'ALTER TABLE purchase_details
    MODIFY COLUMN base_qty VARBINARY(100) NOT NULL,
    MODIFY COLUMN net_purchase_unit_cost VARBINARY(100) NOT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_type = (SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchase_return_details' AND COLUMN_NAME = 'return_unit_cost');
SET @sql = IF(@col_type LIKE 'decimal%', 'ALTER TABLE purchase_return_details
    MODIFY COLUMN return_unit_cost VARBINARY(100) NOT NULL,
    MODIFY COLUMN returned_stock_value VARBINARY(100) NOT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_type = (SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sale_details' AND COLUMN_NAME = 'base_qty');
SET @sql = IF(@col_type LIKE 'decimal%', 'ALTER TABLE sale_details
    MODIFY COLUMN base_qty VARBINARY(100) NOT NULL,
    MODIFY COLUMN line_unit_cost VARBINARY(100) NOT NULL,
    MODIFY COLUMN line_cost_value VARBINARY(100) NOT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_type = (SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales_return_details' AND COLUMN_NAME = 'return_unit_cost');
SET @sql = IF(@col_type LIKE 'decimal%', 'ALTER TABLE sales_return_details
    MODIFY COLUMN return_unit_cost VARBINARY(100) NOT NULL,
    MODIFY COLUMN returned_cost_value VARBINARY(100) NOT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_type = (SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'stock_details' AND COLUMN_NAME = 'unit_cost');
SET @sql = IF(@col_type LIKE 'decimal%', 'ALTER TABLE stock_details
    MODIFY COLUMN unit_cost VARBINARY(100) NOT NULL,
    MODIFY COLUMN value VARBINARY(100) NOT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
