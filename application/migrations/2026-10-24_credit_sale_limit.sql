-- Company Total Credit Limit (Preference page):
--   - web_setting gains two global caps on outstanding credit due —
--     sale_credit_limit and service_credit_limit — kept separate since
--     Sale and Service post to customer_due independently (via sale_id vs
--     service_id) and the client wants them capped independently.
--   - 0 (the default) means "no limit", same convention already used by
--     customer_information.branch_limit/company_limit in
--     2026-10-10_credit_limit.sql.
-- Safe to re-run: column-existence guarded via dynamic ALTER.

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'web_setting' AND COLUMN_NAME = 'sale_credit_limit');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE web_setting ADD COLUMN sale_credit_limit DECIMAL(15,2) NOT NULL DEFAULT 0', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'web_setting' AND COLUMN_NAME = 'service_credit_limit');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE web_setting ADD COLUMN service_credit_limit DECIMAL(15,2) NOT NULL DEFAULT 0', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
