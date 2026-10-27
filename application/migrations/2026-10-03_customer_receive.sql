-- Customer Receive module — all schema changes (2026-09-13)

CREATE TABLE IF NOT EXISTS `customer_due` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `branch` int(11) NOT NULL,
  `customer` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL DEFAULT 0,
  `sales_return_id` int(11) NOT NULL DEFAULT 0,
  `customer_receive_id` int(11) NOT NULL DEFAULT 0,
  `amount` varbinary(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `customer_due` ADD `type2` VARBINARY(250) NOT NULL AFTER `amount`;

CREATE TABLE IF NOT EXISTS `customer_receive` (
  `id`           int(11)          NOT NULL AUTO_INCREMENT,
  `voucher_no`   varbinary(250)  NOT NULL,
  `date`         varchar(255)     NOT NULL,
  `branch`       int(11)          NOT NULL,
  `customer`     int(11)          NOT NULL,
  `total_amount` varbinary(250)   NOT NULL,
  `type2`        varbinary(250)  NOT NULL,
  `created_by`   int(11)          NOT NULL DEFAULT 0,
  `created_at`   datetime         NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `customer_receive_details` (
  `id`                   int(11)         NOT NULL AUTO_INCREMENT,
  `customer_receive_id`  int(11)         NOT NULL,
  `sale_id`              int(11)         NOT NULL,
  `settle_amount`        varbinary(250)  NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_crd_cr`   (`customer_receive_id`),
  KEY `fk_crd_sale` (`sale_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `customer_information`
  ADD COLUMN `due_date` INT(11) NOT NULL DEFAULT 0;

ALTER TABLE `sale`
  ADD COLUMN `due_date` INT(11) NOT NULL DEFAULT 0;

-- Register Customer Receive as its own module group in the Role permission
-- page, with New Customer Receive / Manage Customer Receive as its assignable
-- sub-permissions, plus display() labels (language) for all three. Guarded
-- with NOT EXISTS so this is safe to run more than once.

INSERT INTO module (id, name, description, image, directory, status)
SELECT (SELECT MAX(id) FROM module) + 1, 'customer_receive', NULL, NULL, NULL, 1
WHERE NOT EXISTS (SELECT 1 FROM module WHERE name = 'customer_receive');

INSERT INTO sub_module (id, mid, name, description, image, directory, status)
SELECT (SELECT MAX(id) FROM sub_module) + 1, (SELECT id FROM module WHERE name = 'customer_receive'), 'new_customer_receive', NULL, NULL, 'new_customer_receive', 1
WHERE NOT EXISTS (SELECT 1 FROM sub_module WHERE name = 'new_customer_receive');

INSERT INTO sub_module (id, mid, name, description, image, directory, status)
SELECT (SELECT MAX(id) FROM sub_module) + 1, (SELECT id FROM module WHERE name = 'customer_receive'), 'manage_customer_receive', NULL, NULL, 'manage_customer_receive', 1
WHERE NOT EXISTS (SELECT 1 FROM sub_module WHERE name = 'manage_customer_receive');

INSERT INTO language (id, phrase, english)
SELECT (SELECT MAX(id) FROM language) + 1, 'customer_receive', 'Customer Receive'
WHERE NOT EXISTS (SELECT 1 FROM language WHERE phrase = 'customer_receive');

INSERT INTO language (id, phrase, english)
SELECT (SELECT MAX(id) FROM language) + 1, 'new_customer_receive', 'New Customer Receive'
WHERE NOT EXISTS (SELECT 1 FROM language WHERE phrase = 'new_customer_receive');

INSERT INTO language (id, phrase, english)
SELECT (SELECT MAX(id) FROM language) + 1, 'manage_customer_receive', 'Manage Customer Receive'
WHERE NOT EXISTS (SELECT 1 FROM language WHERE phrase = 'manage_customer_receive');

-- Registers the two Customer Receive-related reports (Customer Receive Report,
-- Customer Due Report) under the existing "report" module so they can be
-- permission-gated and assigned to roles the same way every other report is.
-- Safe to re-run: every insert is NOT EXISTS-guarded and computes MAX(id)+1
-- rather than hardcoding ids, so it works across databases with different
-- existing data (matches the pattern used in customer_receive.sql).

INSERT INTO sub_module (id, mid, name, description, image, directory, status)
SELECT (SELECT MAX(id) FROM sub_module) + 1, (SELECT id FROM module WHERE name = 'report'), 'customer_receive_report', NULL, NULL, 'customer_receive_report', 1
WHERE NOT EXISTS (SELECT 1 FROM sub_module WHERE name = 'customer_receive_report');

INSERT INTO sub_module (id, mid, name, description, image, directory, status)
SELECT (SELECT MAX(id) FROM sub_module) + 1, (SELECT id FROM module WHERE name = 'report'), 'customer_due_report', NULL, NULL, 'customer_due_report', 1
WHERE NOT EXISTS (SELECT 1 FROM sub_module WHERE name = 'customer_due_report');

INSERT INTO language (id, phrase, english)
SELECT (SELECT MAX(id) FROM language) + 1, 'customer_receive_report', 'Customer Receive Report'
WHERE NOT EXISTS (SELECT 1 FROM language WHERE phrase = 'customer_receive_report');

INSERT INTO language (id, phrase, english)
SELECT (SELECT MAX(id) FROM language) + 1, 'customer_due_report', 'Customer Due Report'
WHERE NOT EXISTS (SELECT 1 FROM language WHERE phrase = 'customer_due_report');