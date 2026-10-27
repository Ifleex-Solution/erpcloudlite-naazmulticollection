-- Adds a dedicated "Credit" payment type so Credit/Credit VAT sales (which
-- aren't paid at the point of sale) have a proper payment method to freeze
-- the Sale screen's Payment Type dropdown to, instead of leaving whatever
-- cash/bank method was last selected.
-- Safe to re-run: guarded by NOT EXISTS, same pattern as other migrations here.

INSERT INTO payment_type (name, nature, status)
SELECT 'Credit', 'Cash Nature', 1
WHERE NOT EXISTS (SELECT 1 FROM payment_type WHERE name = 'Credit');

-- Adds a Payment Type field to customer_receive, matching how `sale.payment_type`
-- is stored (plain int FK into payment_type.id, not AES-encrypted — it's just a
-- reference id, not sensitive data).
-- Safe to re-run: guarded by a column-existence check.

SET @col_exists := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'customer_receive'
      AND COLUMN_NAME = 'payment_type'
);

SET @sql := IF(@col_exists = 0,
    'ALTER TABLE customer_receive ADD COLUMN payment_type INT(11) NULL DEFAULT NULL AFTER customer',
    'SELECT 1'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
