-- Credit Limit feature for Customer and Supplier:
--   - customer_information / supplier_information gain branch_limit and
--     company_limit (plain DECIMAL, not AES-encrypted — same convention as
--     the existing due_date column, since these are numeric, not PII).
--   - supplier_information gains due_date too (customer already has it,
--     added earlier for Customer Receive; supplier never got the
--     equivalent — needed now since "Credit Control" puts Due Date (Days)
--     in the same section as Branch/Company Limit for both entities).
--   - web_setting gains credit_reminder_days (global preference, same shape
--     as the existing expiry_alert_days column).
-- Safe to re-run: column-existence guarded via dynamic ALTER.

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customer_information' AND COLUMN_NAME = 'branch_limit');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE customer_information ADD COLUMN branch_limit DECIMAL(15,2) NOT NULL DEFAULT 0', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customer_information' AND COLUMN_NAME = 'company_limit');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE customer_information ADD COLUMN company_limit DECIMAL(15,2) NOT NULL DEFAULT 0', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supplier_information' AND COLUMN_NAME = 'due_date');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE supplier_information ADD COLUMN due_date INT(11) NOT NULL DEFAULT 0', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supplier_information' AND COLUMN_NAME = 'branch_limit');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE supplier_information ADD COLUMN branch_limit DECIMAL(15,2) NOT NULL DEFAULT 0', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supplier_information' AND COLUMN_NAME = 'company_limit');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE supplier_information ADD COLUMN company_limit DECIMAL(15,2) NOT NULL DEFAULT 0', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'web_setting' AND COLUMN_NAME = 'credit_reminder_days');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE web_setting ADD COLUMN credit_reminder_days INT(11) NOT NULL DEFAULT 0', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
