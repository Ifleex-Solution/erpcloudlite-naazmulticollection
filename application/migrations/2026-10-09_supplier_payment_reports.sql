-- Registers the two Supplier Payment-related reports (Supplier Payment Report,
-- Supplier Due Report) under the existing "report" module, same pattern as
-- customer_receive_reports (now folded into 2026-10-03_customer_receive.sql).
-- Safe to re-run: every insert is NOT EXISTS-guarded and computes MAX(id)+1
-- rather than hardcoding ids.

INSERT INTO sub_module (id, mid, name, description, image, directory, status)
SELECT (SELECT MAX(id) FROM sub_module) + 1, (SELECT id FROM module WHERE name = 'report'), 'supplier_payment_report', NULL, NULL, 'supplier_payment_report', 1
WHERE NOT EXISTS (SELECT 1 FROM sub_module WHERE name = 'supplier_payment_report');

INSERT INTO sub_module (id, mid, name, description, image, directory, status)
SELECT (SELECT MAX(id) FROM sub_module) + 1, (SELECT id FROM module WHERE name = 'report'), 'supplier_due_report', NULL, NULL, 'supplier_due_report', 1
WHERE NOT EXISTS (SELECT 1 FROM sub_module WHERE name = 'supplier_due_report');

INSERT INTO language (id, phrase, english)
SELECT (SELECT MAX(id) FROM language) + 1, 'supplier_payment_report', 'Supplier Payment Report'
WHERE NOT EXISTS (SELECT 1 FROM language WHERE phrase = 'supplier_payment_report');

INSERT INTO language (id, phrase, english)
SELECT (SELECT MAX(id) FROM language) + 1, 'supplier_due_report', 'Supplier Due Report'
WHERE NOT EXISTS (SELECT 1 FROM language WHERE phrase = 'supplier_due_report');
