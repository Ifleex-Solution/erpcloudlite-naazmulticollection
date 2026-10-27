-- Store Transfer-type Inventory Requests only ever captured the "From Store"
-- in inventory_request_details.store — the "To Store" was never persisted
-- anywhere, so neither the request's own edit screen nor the "New Inventory"
-- convert flow could repopulate the From/To Store fields for a transfer
-- request. Adds the missing to_store column (0 for non-transfer rows).
-- Safe to re-run: column-existence guarded.

SET @col_exists = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'inventory_request_details' AND COLUMN_NAME = 'to_store'
);
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE inventory_request_details ADD COLUMN to_store INT(11) NOT NULL DEFAULT 0 AFTER store',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
