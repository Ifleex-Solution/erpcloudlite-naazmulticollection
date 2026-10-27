-- Unit Cost Logic Specification v1.1 (Phase 6, UCL-75/UCL-76) — Gross Profit reports.
--
-- Both Gross Profit stored procedures previously computed Cost of Goods Sold as a PERIODIC
-- inventory calculation: Opening Stock + Purchases - Closing Stock, with Opening/Closing Stock
-- valued at a snapshot of the product's current Fixed Purchase Price (`product_information.
-- cost_price`) times quantity as of a date — not even the price that was actually in effect at
-- that historical date. UCL-75 replaces this with the cost actually consumed by each sale,
-- read directly off `sale_details.line_cost_value` / `sales_return_details.returned_cost_value`
-- (the moving-average cost at the time of that sale, Phase 4). UCL-76 keeps the old Fixed
-- Purchase Price x Qty fallback, but only for Stock=Disable products — these are never tracked
-- in `stock_details` and so never get a `unit_cost`/`line_cost_value` populated at all (Phase 2-4
-- guard every costing call on `isstock == 1`, which a Stock=Disable product's lines never are).
--
-- This drops "Purchase of Product" / "Opening Stock" / "Closing Stock" as Cost-of-Sale line
-- items entirely — they measured what was bought and held, not what was actually sold, which is
-- what Cost of Goods Sold within a Gross Profit statement is supposed to measure. The
-- corresponding PHP (`Report::gross_profit_report()`, `generate_gross_profit_report()`,
-- `generate_grossprofitreportcategorywise()`) was rewritten alongside this migration to match.

DELIMITER $$

DROP PROCEDURE IF EXISTS GetGrossProfitReport$$

CREATE PROCEDURE GetGrossProfitReport(IN p_from_date DATE, IN p_to_date DATE, IN p_key VARCHAR(100), IN p_type2 VARCHAR(10))
BEGIN

SELECT
    'sale' AS type,
    SUM(AES_DECRYPT(grandtotal, p_key) + 0) AS grandtotal
FROM sale
WHERE (p_type2 IS NULL OR type2 = AES_ENCRYPT(p_type2, p_key))
AND date BETWEEN p_from_date AND p_to_date

UNION ALL

SELECT
    'sales_return' AS type,
    SUM(AES_DECRYPT(grandtotal, p_key) + 0)
FROM sales_return
WHERE (p_type2 IS NULL OR type2 = AES_ENCRYPT(p_type2, p_key))
AND date BETWEEN p_from_date AND p_to_date

UNION ALL

SELECT
    'service' AS type,
    SUM(AES_DECRYPT(grandtotal, p_key) + 0)
FROM service
WHERE (p_type2 IS NULL OR type2 = AES_ENCRYPT(p_type2, p_key))
AND date BETWEEN p_from_date AND p_to_date

UNION ALL

-- UCL-75: Cost of Goods Sold for Stock-tracked products = the cost each sale line was actually
-- valued at when it was sold (moving-average at that moment), summed over the period.
SELECT
    'cogs_tracked' AS type,
    SUM(AES_DECRYPT(sd.line_cost_value, p_key) + 0)
FROM sale s
INNER JOIN sale_details sd ON sd.pid = s.id
INNER JOIN product_information pi ON pi.id = sd.product
WHERE pi.stock = 1
AND (p_type2 IS NULL OR s.type2 = AES_ENCRYPT(p_type2, p_key))
AND s.date BETWEEN p_from_date AND p_to_date

UNION ALL

SELECT
    'cogs_tracked_return' AS type,
    SUM(AES_DECRYPT(sd.returned_cost_value, p_key) + 0)
FROM sales_return s
INNER JOIN sales_return_details sd ON sd.pid = s.id
INNER JOIN product_information pi ON pi.id = sd.product
WHERE pi.stock = 1
AND (p_type2 IS NULL OR s.type2 = AES_ENCRYPT(p_type2, p_key))
AND s.date BETWEEN p_from_date AND p_to_date

UNION ALL

-- UCL-76: Stock=Disable products have no unit_cost history at all (never tracked in
-- stock_details), so their Cost of Goods Sold still falls back to Fixed Purchase Price x Qty.
SELECT
    'cogs_disabled' AS type,
    SUM((AES_DECRYPT(sd.quantity, p_key) + 0) * (AES_DECRYPT(pi.cost_price, p_key) + 0))
FROM sale s
INNER JOIN sale_details sd ON sd.pid = s.id
INNER JOIN product_information pi ON pi.id = sd.product
WHERE pi.stock = 0
AND (p_type2 IS NULL OR s.type2 = AES_ENCRYPT(p_type2, p_key))
AND s.date BETWEEN p_from_date AND p_to_date

UNION ALL

SELECT
    'cogs_disabled_return' AS type,
    SUM((AES_DECRYPT(sd.rqty, p_key) + 0) * (AES_DECRYPT(pi.cost_price, p_key) + 0))
FROM sales_return s
INNER JOIN sales_return_details sd ON sd.pid = s.id
INNER JOIN product_information pi ON pi.id = sd.product
WHERE pi.stock = 0
AND (p_type2 IS NULL OR s.type2 = AES_ENCRYPT(p_type2, p_key))
AND s.date BETWEEN p_from_date AND p_to_date;

END$$

DROP PROCEDURE IF EXISTS GrossProfitReportCategorywise$$

CREATE PROCEDURE GrossProfitReportCategorywise(IN p_from_date DATE, IN p_to_date DATE, IN p_type2 VARCHAR(10), IN p_key VARCHAR(100), IN p_product_id INT, IN p_category_id INT)
BEGIN

    SELECT *,
           (total_sale - cogs) AS gross_profit
    FROM (
        SELECT
            pc.category_id,
            pc.category_name,
            p.id,
            p.product_name,

            (
                IFNULL((
                    SELECT SUM(AES_DECRYPT(sd.total_price, p_key) + 0)
                    FROM sale s
                    INNER JOIN sale_details sd ON sd.pid = s.id
                    WHERE sd.product = p.id
                    AND (p_type2 IS NULL OR s.type2 = AES_ENCRYPT(p_type2, p_key))
                    AND s.date BETWEEN p_from_date AND p_to_date
                ), 0)
                -
                IFNULL((
                    SELECT SUM(AES_DECRYPT(sd.total_price, p_key) + 0)
                    FROM sales_return s
                    INNER JOIN sales_return_details sd ON sd.pid = s.id
                    WHERE sd.product = p.id
                    AND (p_type2 IS NULL OR s.type2 = AES_ENCRYPT(p_type2, p_key))
                    AND s.date BETWEEN p_from_date AND p_to_date
                ), 0)
            ) AS total_sale,

            -- UCL-75/UCL-76: Cost of Goods Sold per product — the cost each sale line was
            -- actually valued at when sold (Stock-tracked), or Fixed Purchase Price x Qty
            -- (Stock=Disable, which never gets a unit_cost at all).
            CASE WHEN p.stock = 0 THEN
                (
                    IFNULL((
                        SELECT SUM(AES_DECRYPT(sd.quantity, p_key) + 0)
                        FROM sale s
                        INNER JOIN sale_details sd ON sd.pid = s.id
                        WHERE sd.product = p.id
                        AND (p_type2 IS NULL OR s.type2 = AES_ENCRYPT(p_type2, p_key))
                        AND s.date BETWEEN p_from_date AND p_to_date
                    ), 0)
                    -
                    IFNULL((
                        SELECT SUM(AES_DECRYPT(sd.rqty, p_key) + 0)
                        FROM sales_return s
                        INNER JOIN sales_return_details sd ON sd.pid = s.id
                        WHERE sd.product = p.id
                        AND (p_type2 IS NULL OR s.type2 = AES_ENCRYPT(p_type2, p_key))
                        AND s.date BETWEEN p_from_date AND p_to_date
                    ), 0)
                ) * (AES_DECRYPT(p.cost_price, p_key) + 0)
            ELSE
                (
                    IFNULL((
                        SELECT SUM(AES_DECRYPT(sd.line_cost_value, p_key) + 0)
                        FROM sale s
                        INNER JOIN sale_details sd ON sd.pid = s.id
                        WHERE sd.product = p.id
                        AND (p_type2 IS NULL OR s.type2 = AES_ENCRYPT(p_type2, p_key))
                        AND s.date BETWEEN p_from_date AND p_to_date
                    ), 0)
                    -
                    IFNULL((
                        SELECT SUM(AES_DECRYPT(sd.returned_cost_value, p_key) + 0)
                        FROM sales_return s
                        INNER JOIN sales_return_details sd ON sd.pid = s.id
                        WHERE sd.product = p.id
                        AND (p_type2 IS NULL OR s.type2 = AES_ENCRYPT(p_type2, p_key))
                        AND s.date BETWEEN p_from_date AND p_to_date
                    ), 0)
                )
            END AS cogs

        FROM product_information p
        INNER JOIN product_category pc ON pc.category_id = p.category_id
        WHERE
          (p_product_id IS NULL OR p.id = p_product_id)
           AND (p_category_id IS NULL OR pc.category_id = p_category_id)
        ORDER BY pc.category_id DESC
    ) t;

END$$

DELIMITER ;
