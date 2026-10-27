-- Extends Customer Receive to also settle "Sale of Service" invoices (the
-- `service` table), not just "Sale of Product" (`sale`). A receive settles
-- either product sales or service sales, never both in the same voucher —
-- mirrors the existing sale_id/sales_return_id/customer_receive_id mutually-
-- exclusive pattern already used on customer_due.
-- Safe to re-run: guarded by column-existence checks.

SET @col_exists := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'customer_due'
      AND COLUMN_NAME = 'service_id'
);
SET @sql := IF(@col_exists = 0,
    'ALTER TABLE customer_due ADD COLUMN service_id INT(11) NOT NULL DEFAULT 0 AFTER sale_id',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists2 := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'customer_receive_details'
      AND COLUMN_NAME = 'service_id'
);
SET @sql2 := IF(@col_exists2 = 0,
    'ALTER TABLE customer_receive_details ADD COLUMN service_id INT(11) NOT NULL DEFAULT 0 AFTER sale_id',
    'SELECT 1'
);
PREPARE stmt2 FROM @sql2;
EXECUTE stmt2;
DEALLOCATE PREPARE stmt2;
