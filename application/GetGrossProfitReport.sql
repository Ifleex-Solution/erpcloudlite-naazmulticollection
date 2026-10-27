DELIMITER $$

CREATE PROCEDURE GetGrossProfitReport (
    IN p_from_date DATE,
    IN p_to_date DATE,
    IN p_key VARCHAR(100),
    IN p_type2 VARCHAR(10)
)
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

SELECT 
    'purchase' AS type,
    SUM(AES_DECRYPT(grandtotal, p_key) + 0)
FROM purchase
WHERE (p_type2 IS NULL OR type2 = AES_ENCRYPT(p_type2, p_key))
AND date BETWEEN p_from_date AND p_to_date

UNION ALL

SELECT 
    'purchase_return' AS type,
    SUM(AES_DECRYPT(grandtotal, p_key) + 0)
FROM purchase_return
WHERE (p_type2 IS NULL OR type2 = AES_ENCRYPT(p_type2, p_key))
AND date BETWEEN p_from_date AND p_to_date

UNION ALL

SELECT 
    'opening_stock' AS type,
    SUM(total) AS grandtotal
FROM (
    SELECT  
        SUM(AES_DECRYPT(s.stock, p_key) + 0) 
        * MAX(AES_DECRYPT(pi.cost_price, p_key) + 0) AS total
    FROM stock_details s
    INNER JOIN product_information pi ON pi.id = s.product
    WHERE s.date < p_from_date
    GROUP BY s.product
) t

UNION ALL

SELECT 
    'closing_stock' AS type,
    SUM(total) AS grandtotal
FROM (
    SELECT  
        SUM(AES_DECRYPT(s.stock, p_key) + 0) 
        * MAX(AES_DECRYPT(pi.cost_price, p_key) + 0) AS total
    FROM stock_details s
    INNER JOIN product_information pi ON pi.id = s.product
    WHERE s.date <= p_to_date
    GROUP BY s.product
) t;

END $$

DELIMITER ;