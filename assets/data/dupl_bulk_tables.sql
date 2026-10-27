-- ============================================================
-- DUPL Bulk Upload History Tables
-- Run this once against your database to enable upload history
-- for all DUPL upload types.
-- ============================================================

CREATE TABLE IF NOT EXISTS `bulk_product_details` (
  `id`          int(11)        NOT NULL AUTO_INCREMENT,
  `uploaded_id` varbinary(200) DEFAULT NULL,
  `date`        datetime       DEFAULT NULL,
  `uploadedby`  int(11)        DEFAULT NULL,
  `product_ids` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `bulk_conversionratio_details` (
  `id`                   int(11)        NOT NULL AUTO_INCREMENT,
  `uploaded_id`          varbinary(200) DEFAULT NULL,
  `date`                 datetime       DEFAULT NULL,
  `uploadedby`           int(11)        DEFAULT NULL,
  `conversionratio_ids`  text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `bulk_brand_details` (
  `id`          int(11)        NOT NULL AUTO_INCREMENT,
  `uploaded_id` varbinary(200) DEFAULT NULL,
  `date`        datetime       DEFAULT NULL,
  `uploadedby`  int(11)        DEFAULT NULL,
  `brand_ids`   text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `bulk_category_details` (
  `id`           int(11)        NOT NULL AUTO_INCREMENT,
  `uploaded_id`  varbinary(200) DEFAULT NULL,
  `date`         datetime       DEFAULT NULL,
  `uploadedby`   int(11)        DEFAULT NULL,
  `category_ids` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `bulk_subcategory_details` (
  `id`              int(11)        NOT NULL AUTO_INCREMENT,
  `uploaded_id`     varbinary(200) DEFAULT NULL,
  `date`            datetime       DEFAULT NULL,
  `uploadedby`      int(11)        DEFAULT NULL,
  `subcategory_ids` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `bulk_unit_details` (
  `id`          int(11)        NOT NULL AUTO_INCREMENT,
  `uploaded_id` varbinary(200) DEFAULT NULL,
  `date`        datetime       DEFAULT NULL,
  `uploadedby`  int(11)        DEFAULT NULL,
  `unit_ids`    text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `bulk_paymentmethod_details` (
  `id`                  int(11)        NOT NULL AUTO_INCREMENT,
  `uploaded_id`         varbinary(200) DEFAULT NULL,
  `date`                datetime       DEFAULT NULL,
  `uploadedby`          int(11)        DEFAULT NULL,
  `paymentmethod_ids`   text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `bulk_branch_details` (
  `id`          int(11)        NOT NULL AUTO_INCREMENT,
  `uploaded_id` varbinary(200) DEFAULT NULL,
  `date`        datetime       DEFAULT NULL,
  `uploadedby`  int(11)        DEFAULT NULL,
  `branch_ids`  text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `bulk_store_details` (
  `id`          int(11)        NOT NULL AUTO_INCREMENT,
  `uploaded_id` varbinary(200) DEFAULT NULL,
  `date`        datetime       DEFAULT NULL,
  `uploadedby`  int(11)        DEFAULT NULL,
  `store_ids`   text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `bulk_customer_details` (
  `id`            int(11)        NOT NULL AUTO_INCREMENT,
  `uploaded_id`   varbinary(200) DEFAULT NULL,
  `date`          datetime       DEFAULT NULL,
  `uploadedby`    int(11)        DEFAULT NULL,
  `customer_ids`  text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `bulk_supplier_details` (
  `id`            int(11)        NOT NULL AUTO_INCREMENT,
  `uploaded_id`   varbinary(200) DEFAULT NULL,
  `date`          datetime       DEFAULT NULL,
  `uploadedby`    int(11)        DEFAULT NULL,
  `supplier_ids`  text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `bulk_service_details` (
  `id`           int(11)        NOT NULL AUTO_INCREMENT,
  `uploaded_id`  varbinary(200) DEFAULT NULL,
  `date`         datetime       DEFAULT NULL,
  `uploadedby`   int(11)        DEFAULT NULL,
  `service_ids`  text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `bulk_productgroup_details` (
  `id`                 int(11)        NOT NULL AUTO_INCREMENT,
  `uploaded_id`        varbinary(200) DEFAULT NULL,
  `date`               datetime       DEFAULT NULL,
  `uploadedby`         int(11)        DEFAULT NULL,
  `productgroup_ids`   text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `bulk_openingstock_details` (
  `id`                 int(11)        NOT NULL AUTO_INCREMENT,
  `uploaded_id`        varbinary(200) DEFAULT NULL,
  `date`               datetime       DEFAULT NULL,
  `uploadedby`         int(11)        DEFAULT NULL,
  `openingstock_ids`   text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `bulk_stockbatch_details` (
  `id`              int(11)        NOT NULL AUTO_INCREMENT,
  `uploaded_id`     varbinary(200) DEFAULT NULL,
  `date`            datetime       DEFAULT NULL,
  `uploadedby`      int(11)        DEFAULT NULL,
  `stockbatch_ids`  text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `bulk_designation_details` (
  `id`               int(11)        NOT NULL AUTO_INCREMENT,
  `uploaded_id`      varbinary(200) DEFAULT NULL,
  `date`             datetime       DEFAULT NULL,
  `uploadedby`       int(11)        DEFAULT NULL,
  `designation_ids`  text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `bulk_employee_details` (
  `id`            int(11)        NOT NULL AUTO_INCREMENT,
  `uploaded_id`   varbinary(200) DEFAULT NULL,
  `date`          datetime       DEFAULT NULL,
  `uploadedby`    int(11)        DEFAULT NULL,
  `employee_ids`  text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `bulk_stockadjustment_details` (
  `id`                   int(11)        NOT NULL AUTO_INCREMENT,
  `uploaded_id`          varbinary(200) DEFAULT NULL,
  `date`                 datetime       DEFAULT NULL,
  `uploadedby`           int(11)        DEFAULT NULL,
  `stockadjustment_ids`  text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
