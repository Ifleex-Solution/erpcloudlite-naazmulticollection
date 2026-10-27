-- Converts the Cash Book report's big inline UNION ALL query (previously built
-- as a raw PHP string in Report::cashbook_reportdata()) into a stored
-- procedure, GetCashbookReport. Also:
--   1. Adds two new branches: Customer Receive (positive/cash in) and
--      Supplier Payment (negative/cash out) — these settlement screens didn't
--      exist yet when the Cash Book was first written, so real cash movement
--      through them never showed up in this report.
--   2. Excludes any row whose payment method is "Credit" — a Credit
--      Sale/Purchase/Return is not an actual cash movement; the cash only
--      moves later, when it is settled via Customer Receive / Supplier
--      Payment (which already exclude "Credit" as a selectable payment type
--      by design, so this filter is a no-op for those two branches and only
--      affects Sale/Purchase/Sales Return/Purchase Return rows).
--   3. The AES encryption key is passed in as p_encryption_key rather than
--      hardcoded, so the procedure doesn't need editing if the key rotates —
--      the caller passes Config::$encryption_key.
--   4. Adds a "Sale of Service" branch (positive/cash in) from the `service`
--      table, mirroring the Sale branch — service invoices never showed up
--      here either. Credit-paid service invoices are still excluded by the
--      same Credit filter and only appear later via Customer Receive, same
--      as credit Sale/Purchase.
-- Safe to re-run: DROP PROCEDURE IF EXISTS + CREATE PROCEDURE.

DELIMITER $$

DROP PROCEDURE IF EXISTS sp_cash_book_report $$
DROP PROCEDURE IF EXISTS GetCashbookReport $$

CREATE PROCEDURE GetCashbookReport(
    IN p_from_date       VARCHAR(20),
    IN p_to_date         VARCHAR(20),
    IN p_type2           VARCHAR(5),
    IN p_payment         INT,
    IN p_payment_nature  VARCHAR(50),
    IN p_branch          INT,
    IN p_branch_list     VARCHAR(1000),
    IN p_encryption_key  VARCHAR(255)
)
BEGIN
    SELECT
        date, incidenttype, payment_type, payment_method, payment_method_nature,
        invoice_no, grandTotal, type2, createddate, branch
    FROM (

        SELECT V.date, 'Payments' AS incidenttype, V.`from` AS payment_type, pt.name AS payment_method,
               IFNULL(pt.nature, '') AS payment_method_nature,
               AES_DECRYPT(V.voucher_id, p_encryption_key) AS invoice_no,
               -CAST(AES_DECRYPT(V.total, p_encryption_key) AS DECIMAL(18,2)) AS grandTotal,
               AES_DECRYPT(V.type2, p_encryption_key) AS type2, V.date AS createddate, V.branch
        FROM voucher V
        INNER JOIN payment_type pt ON pt.id = V.from
        WHERE V.type = 1

        UNION ALL

        SELECT V.date, 'Receipts', V.`from`, pt.name, IFNULL(pt.nature, ''),
               AES_DECRYPT(V.voucher_id, p_encryption_key),
               CAST(AES_DECRYPT(V.total, p_encryption_key) AS DECIMAL(18,2)),
               AES_DECRYPT(V.type2, p_encryption_key), V.date, V.branch
        FROM voucher V
        INNER JOIN payment_type pt ON pt.id = V.from
        WHERE V.type = 2

        UNION ALL

        SELECT V.date, 'Transfer', V.`from`, pt.name, IFNULL(pt.nature, ''),
               AES_DECRYPT(V.voucher_id, p_encryption_key),
               -CAST(AES_DECRYPT(V.total, p_encryption_key) AS DECIMAL(18,2)),
               AES_DECRYPT(V.type2, p_encryption_key), V.date, V.branch
        FROM voucher V
        INNER JOIN payment_type pt ON pt.id = V.from
        WHERE V.type = 3

        UNION ALL

        SELECT V.date, 'Transfer', Vd.to, pt.name, IFNULL(pt.nature, ''),
               AES_DECRYPT(V.voucher_id, p_encryption_key),
               CAST(AES_DECRYPT(Vd.amount, p_encryption_key) AS DECIMAL(18,2)),
               AES_DECRYPT(V.type2, p_encryption_key), V.date, V.branch
        FROM voucher_details Vd
        INNER JOIN voucher V ON Vd.pid = V.id
        INNER JOIN payment_type pt ON pt.id = Vd.to
        WHERE V.type = 3

        UNION ALL

        SELECT s.date, 'Sale', s.payment_type, pt.name, IFNULL(pt.nature, ''),
               AES_DECRYPT(s.sale_id, p_encryption_key),
               CAST(AES_DECRYPT(s.grandTotal, p_encryption_key) AS DECIMAL(18,2)),
               AES_DECRYPT(s.type2, p_encryption_key), s.createddate, s.branch
        FROM sale s
        INNER JOIN payment_type pt ON pt.id = s.payment_type

        UNION ALL

        SELECT sv.date, 'Sale of Service', sv.payment_type, pt.name, IFNULL(pt.nature, ''),
               AES_DECRYPT(sv.service_id, p_encryption_key),
               CAST(AES_DECRYPT(sv.grandTotal, p_encryption_key) AS DECIMAL(18,2)),
               AES_DECRYPT(sv.type2, p_encryption_key), sv.createddate, sv.branch
        FROM service sv
        INNER JOIN payment_type pt ON pt.id = sv.payment_type

        UNION ALL

        SELECT p.date, 'Purchase', p.payment_type, pt.name, IFNULL(pt.nature, ''),
               AES_DECRYPT(p.chalan_no, p_encryption_key),
               -CAST(AES_DECRYPT(p.grandTotal, p_encryption_key) AS DECIMAL(18,2)),
               AES_DECRYPT(p.type2, p_encryption_key), p.createddate, p.branch
        FROM purchase p
        INNER JOIN payment_type pt ON pt.id = p.payment_type

        UNION ALL

        SELECT pr.rdate, 'Purchase Return', pr.payment_type, pt.name, IFNULL(pt.nature, ''),
               AES_DECRYPT(p.chalan_no, p_encryption_key),
               CAST(AES_DECRYPT(pr.grandTotal, p_encryption_key) AS DECIMAL(18,2)),
               AES_DECRYPT(pr.type2, p_encryption_key), pr.createddate, pr.branch
        FROM purchase_return pr
        INNER JOIN payment_type pt ON pt.id = pr.payment_type
        INNER JOIN purchase p ON p.id = pr.purchase_id

        UNION ALL

        SELECT sr.rdate, 'Sales Return', sr.payment_type, pt.name, IFNULL(pt.nature, ''),
               AES_DECRYPT(s.sale_id, p_encryption_key),
               -CAST(AES_DECRYPT(sr.grandTotal, p_encryption_key) AS DECIMAL(18,2)),
               AES_DECRYPT(sr.type2, p_encryption_key), sr.createddate, sr.branch
        FROM sales_return sr
        INNER JOIN payment_type pt ON pt.id = sr.payment_type
        INNER JOIN sale s ON s.id = sr.sales_id

        UNION ALL

        SELECT cr.date, 'Customer Receive', cr.payment_type, pt.name, IFNULL(pt.nature, ''),
               AES_DECRYPT(cr.voucher_no, p_encryption_key),
               CAST(AES_DECRYPT(cr.total_amount, p_encryption_key) AS DECIMAL(18,2)),
               AES_DECRYPT(cr.type2, p_encryption_key), cr.created_at, cr.branch
        FROM customer_receive cr
        INNER JOIN payment_type pt ON pt.id = cr.payment_type

        UNION ALL

        SELECT sp.date, 'Supplier Payment', sp.payment_type, pt.name, IFNULL(pt.nature, ''),
               AES_DECRYPT(sp.voucher_no, p_encryption_key),
               -CAST(AES_DECRYPT(sp.total_amount, p_encryption_key) AS DECIMAL(18,2)),
               AES_DECRYPT(sp.type2, p_encryption_key), sp.created_at, sp.branch
        FROM supplier_payment sp
        INNER JOIN payment_type pt ON pt.id = sp.payment_type

    ) AS cashbook
    WHERE date BETWEEN p_from_date AND p_to_date
      AND payment_method <> 'Credit'
      AND (p_type2 = 'All' OR type2 = p_type2)
      AND (
            (p_payment <> 0 AND payment_type = p_payment)
         OR (p_payment = 0 AND p_payment_nature <> '' AND payment_method_nature = p_payment_nature)
         OR (p_payment = 0 AND p_payment_nature = '')
      )
      AND (
            (p_branch <> 0 AND branch = p_branch)
         OR (p_branch = 0 AND p_branch_list = '')
         OR (p_branch = 0 AND p_branch_list <> '' AND FIND_IN_SET(branch, p_branch_list))
      )
    ORDER BY createddate DESC;
END $$

DELIMITER ;
