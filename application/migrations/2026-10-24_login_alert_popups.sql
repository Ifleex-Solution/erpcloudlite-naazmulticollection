-- Login Alert Pop-ups feature (Fexten_ERP_Login_Alert_Popups_v3.3.pdf):
--   - web_setting gains 5 company-wide on/off toggles for the Preference
--     page: a Credit master switch + separate Customer/Supplier sub-switches
--     (Expiration and Stock alerts never had an on/off switch before; they
--     always fired — this adds one for each so all four can be controlled
--     the same way).
--   - credit_alert registered as a new permission key, grouped under the
--     same 'alert' module (id 26) that stock_alert/expiry_alert already use
--     — one key covers both the Customer and Supplier Credit pop-ups, same
--     one-key-per-alert-type pattern as the other two.
-- Safe to re-run: column-existence guarded ALTERs, NOT EXISTS-guarded inserts.

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'web_setting' AND COLUMN_NAME = 'credit_alert_enable');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE web_setting ADD COLUMN credit_alert_enable TINYINT(1) NOT NULL DEFAULT 1', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'web_setting' AND COLUMN_NAME = 'credit_alert_customer_enable');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE web_setting ADD COLUMN credit_alert_customer_enable TINYINT(1) NOT NULL DEFAULT 1', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'web_setting' AND COLUMN_NAME = 'credit_alert_supplier_enable');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE web_setting ADD COLUMN credit_alert_supplier_enable TINYINT(1) NOT NULL DEFAULT 1', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'web_setting' AND COLUMN_NAME = 'expiry_alert_enable');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE web_setting ADD COLUMN expiry_alert_enable TINYINT(1) NOT NULL DEFAULT 1', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'web_setting' AND COLUMN_NAME = 'stock_alert_enable');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE web_setting ADD COLUMN stock_alert_enable TINYINT(1) NOT NULL DEFAULT 1', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

INSERT INTO sub_module (id, mid, name, description, image, directory, status)
SELECT (SELECT MAX(id) FROM sub_module) + 1, (SELECT id FROM module WHERE name = 'alert'), 'credit_alert', NULL, NULL, 'credit_alert', 1
WHERE NOT EXISTS (SELECT 1 FROM sub_module WHERE name = 'credit_alert');

INSERT INTO language (id, phrase, english)
SELECT (SELECT MAX(id) FROM language) + 1, 'credit_alert', 'Credit Alert'
WHERE NOT EXISTS (SELECT 1 FROM language WHERE phrase = 'credit_alert');

-- Show the login pop-up sequence once per calendar day per user, not on
-- every single login — set the moment the sequence is decided to fire
-- (Home::index()), not only once fully skipped through, so a user who
-- closes the browser mid-sequence isn't shown it again later the same day.
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'user_login' AND COLUMN_NAME = 'last_alert_shown_date');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE user_login ADD COLUMN last_alert_shown_date DATE NULL DEFAULT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
