-- "Inventory Request" — a new screen that is structurally identical to
-- "New Inventory" (same header fields: Date/Incident Type/Stock Type/Reason,
-- same product/store/qty/batch/unit row grid) but is purely a draft/order
-- document: saving it does NOT touch stock_details/phystock_details/
-- audit_stock at all. It mirrors the existing Sales Order -> Sale and
-- Purchase Order -> Purchase conversion pattern: a request gets its own
-- voucher number and a status flag (0 = open, 1 = converted); the "New
-- Inventory" screen (stock/stock/bdtask_new_stock_form) gains a dropdown to
-- pick an open request, which pulls its lines into the form via
-- getInventoryRequestById(); saving the real inventory transaction then
-- flips the source request's status to 1 and records the FK on adj_stock.
--
-- Table shapes deliberately mirror adj_stock/stock_details (minus the
-- audit_stock/logs/stock-table writes) so the existing getAdjStockById-style
-- query logic could be reused almost verbatim for the new endpoints.
-- Safe to re-run: every statement is existence-guarded.

CREATE TABLE IF NOT EXISTS `inventory_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_no` varbinary(150) NOT NULL,
  `date` varchar(255) NOT NULL,
  `reason` varchar(500) NOT NULL,
  `type2` varbinary(150) NOT NULL,
  `type` varchar(255) NOT NULL,
  `stocktype` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `createddate` datetime NOT NULL,
  `userid` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `inventory_request_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product` int(11) NOT NULL,
  `batch` int(11) NOT NULL,
  `store` int(11) NOT NULL,
  `stock` varbinary(100) NOT NULL,
  `type` varchar(255) NOT NULL,
  `pid` int(14) NOT NULL,
  `date` date NOT NULL,
  `conversion_id` int(20) NOT NULL,
  `unit` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product` (`product`),
  KEY `pid` (`pid`),
  KEY `store` (`store`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- FK on adj_stock back to the request it was converted from (0 = not from a
-- request), same convention as sale.sales_order_no / purchase.purchase_order_no.
SET @col_exists = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'adj_stock' AND COLUMN_NAME = 'inventory_request_no'
);
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE adj_stock ADD COLUMN inventory_request_no INT(11) NOT NULL DEFAULT 0 AFTER stocktype',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Permission rows: new_inventory_request (create) / manage_inventory_request (read),
-- under the existing "stock" module, same module id as new_inventory_transection.
INSERT INTO sub_module (id, mid, name, description, image, directory, status)
SELECT (SELECT MAX(id) FROM sub_module) + 1, (SELECT id FROM module WHERE name = 'stock'), 'new_inventory_request', NULL, NULL, 'new_inventory_request', 1
WHERE NOT EXISTS (SELECT 1 FROM sub_module WHERE name = 'new_inventory_request');

INSERT INTO sub_module (id, mid, name, description, image, directory, status)
SELECT (SELECT MAX(id) FROM sub_module) + 1, (SELECT id FROM module WHERE name = 'stock'), 'manage_inventory_request', NULL, NULL, 'manage_inventory_request', 1
WHERE NOT EXISTS (SELECT 1 FROM sub_module WHERE name = 'manage_inventory_request');

INSERT INTO language (id, phrase, english)
SELECT (SELECT MAX(id) FROM language) + 1, 'new_inventory_request', 'Inventory Request'
WHERE NOT EXISTS (SELECT 1 FROM language WHERE phrase = 'new_inventory_request');

INSERT INTO language (id, phrase, english)
SELECT (SELECT MAX(id) FROM language) + 1, 'manage_inventory_request', 'Manage Inventory Request'
WHERE NOT EXISTS (SELECT 1 FROM language WHERE phrase = 'manage_inventory_request');
