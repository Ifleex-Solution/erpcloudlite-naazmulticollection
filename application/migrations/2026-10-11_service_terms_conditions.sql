-- Service Terms & Conditions preference field:
--   web_setting gains service_terms_conditions (TEXT, same shape as the
--   existing terms_conditions column, used for Sales/POS invoices).
--   The existing terms_conditions field is relabeled "Sales Terms &
--   Conditions" in the preference page (no column rename, UI label only)
--   to distinguish it from this new Service-specific field.
-- Safe to re-run: column-existence guarded via dynamic ALTER.

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'web_setting' AND COLUMN_NAME = 'service_terms_conditions');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE web_setting ADD COLUMN service_terms_conditions TEXT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
