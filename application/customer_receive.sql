-- Customer Receive module — all schema changes (2026-09-13)

CREATE TABLE IF NOT EXISTS `customer_due` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `branch` int(11) NOT NULL,
  `customer` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL DEFAULT 0,
  `sales_return_id` int(11) NOT NULL DEFAULT 0,
  `customer_receive_id` int(11) NOT NULL DEFAULT 0,
  `amount` varbinary(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `customer_due` ADD `type2` VARBINARY(2000) NOT NULL AFTER `amount`;

CREATE TABLE IF NOT EXISTS `customer_receive` (
  `id`           int(11)          NOT NULL AUTO_INCREMENT,
  `voucher_no`   varbinary(2000)  NOT NULL,
  `date`         varchar(255)     NOT NULL,
  `branch`       int(11)          NOT NULL,
  `customer`     int(11)          NOT NULL,
  `total_amount` varbinary(500)   NOT NULL,
  `type2`        varbinary(2000)  NOT NULL,
  `created_by`   int(11)          NOT NULL DEFAULT 0,
  `created_at`   datetime         NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `customer_receive_details` (
  `id`                   int(11)         NOT NULL AUTO_INCREMENT,
  `customer_receive_id`  int(11)         NOT NULL,
  `sale_id`              int(11)         NOT NULL,
  `settle_amount`        varbinary(500)  NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_crd_cr`   (`customer_receive_id`),
  KEY `fk_crd_sale` (`sale_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `customer_information`
  ADD COLUMN `due_date` INT(11) NOT NULL DEFAULT 0;

ALTER TABLE `sale`
  ADD COLUMN `due_date` INT(11) NOT NULL DEFAULT 0;
