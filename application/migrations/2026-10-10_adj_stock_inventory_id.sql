-- adj_stock (the "New Inventory Transaction" header table) never had a
-- proper voucher number — the print receipt was showing the raw numeric id
-- as "Transaction No". Adds inventory_id, an AES_ENCRYPT'd varbinary column
-- generated the same way as Customer Receive/Supplier Payment/Inventory
-- Request's voucher numbers (INV-0000000001 for type A, INV-1000000001 for
-- type B). Nullable: existing rows predate this column and are left blank —
-- the print falls back to "#<id>" for those.
-- Safe to re-run: column-existence guarded.

SET @col_exists = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'adj_stock' AND COLUMN_NAME = 'inventory_id'
);
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE adj_stock ADD COLUMN inventory_id VARBINARY(150) NULL AFTER id',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
