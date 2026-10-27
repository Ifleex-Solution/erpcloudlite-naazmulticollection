-- Supplier Payment feature: mirrors Customer Receive exactly, but settles
-- Purchase invoices (and is reduced by Purchase Returns) instead of Sales.
-- Tables mirror customer_receive / customer_receive_details / customer_due:
--   supplier_payment         (header — one row per payment voucher)
--   supplier_payment_details (one row per purchase settled by that voucher)
--   supplier_due             (ledger: +purchase, -purchase_return, -supplier_payment)
-- Safe to re-run: CREATE TABLE IF NOT EXISTS + NOT EXISTS-guarded inserts.

CREATE TABLE IF NOT EXISTS supplier_payment (
    id INT(11) NOT NULL AUTO_INCREMENT,
    voucher_no VARBINARY(2000) NOT NULL,
    date VARCHAR(255) NOT NULL,
    branch INT(11) NOT NULL,
    supplier INT(11) NOT NULL,
    payment_type INT(11) NULL DEFAULT NULL,
    total_amount VARBINARY(500) NOT NULL,
    type2 VARBINARY(2000) NOT NULL,
    created_by INT(11) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS supplier_payment_details (
    id INT(11) NOT NULL AUTO_INCREMENT,
    supplier_payment_id INT(11) NOT NULL,
    purchase_id INT(11) NOT NULL,
    settle_amount VARBINARY(500) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS supplier_due (
    id INT(11) NOT NULL AUTO_INCREMENT,
    branch INT(11) NOT NULL,
    supplier INT(11) NOT NULL,
    purchase_id INT(11) NOT NULL DEFAULT 0,
    purchase_return_id INT(11) NOT NULL DEFAULT 0,
    supplier_payment_id INT(11) NOT NULL DEFAULT 0,
    amount VARBINARY(500) NOT NULL,
    type2 VARBINARY(2000) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Register Supplier Payment as its own module group in the Role permission
-- page (same pattern as customer_receive.sql) — it's a standalone sidebar
-- section next to "Purchase of Product", not nested under the Purchase
-- module, so it needs its own module row rather than reusing purchase's mid.
INSERT INTO module (id, name, description, image, directory, status)
SELECT (SELECT MAX(id) FROM module) + 1, 'supplier_payment', NULL, NULL, NULL, 1
WHERE NOT EXISTS (SELECT 1 FROM module WHERE name = 'supplier_payment');

INSERT INTO sub_module (id, mid, name, description, image, directory, status)
SELECT (SELECT MAX(id) FROM sub_module) + 1, (SELECT id FROM module WHERE name = 'supplier_payment'), 'new_supplier_payment', NULL, NULL, 'new_supplier_payment', 1
WHERE NOT EXISTS (SELECT 1 FROM sub_module WHERE name = 'new_supplier_payment');

INSERT INTO sub_module (id, mid, name, description, image, directory, status)
SELECT (SELECT MAX(id) FROM sub_module) + 1, (SELECT id FROM module WHERE name = 'supplier_payment'), 'manage_supplier_payment', NULL, NULL, 'manage_supplier_payment', 1
WHERE NOT EXISTS (SELECT 1 FROM sub_module WHERE name = 'manage_supplier_payment');

-- If an earlier run of this migration registered the two sub_modules under
-- 'purchase' (before this feature became a standalone sidebar section),
-- re-point them at the new dedicated module instead of leaving them orphaned
-- there.
UPDATE sub_module
SET mid = (SELECT id FROM module WHERE name = 'supplier_payment')
WHERE name IN ('new_supplier_payment', 'manage_supplier_payment')
  AND mid = (SELECT id FROM module WHERE name = 'purchase');

INSERT INTO language (id, phrase, english)
SELECT (SELECT MAX(id) FROM language) + 1, 'supplier_payment', 'Supplier Payment'
WHERE NOT EXISTS (SELECT 1 FROM language WHERE phrase = 'supplier_payment');

INSERT INTO language (id, phrase, english)
SELECT (SELECT MAX(id) FROM language) + 1, 'new_supplier_payment', 'New Supplier Payment'
WHERE NOT EXISTS (SELECT 1 FROM language WHERE phrase = 'new_supplier_payment');

INSERT INTO language (id, phrase, english)
SELECT (SELECT MAX(id) FROM language) + 1, 'manage_supplier_payment', 'Manage Supplier Payment'
WHERE NOT EXISTS (SELECT 1 FROM language WHERE phrase = 'manage_supplier_payment');
