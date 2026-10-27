 DELIMITER $$

CREATE PROCEDURE GrossProfitReportCategorywise (
    IN p_from_date DATE,
    IN p_to_date DATE,
    IN p_type2 VARCHAR(10),  
    IN p_key VARCHAR(100),
    IN p_product_id INT,
    IN p_category_id INT
)
BEGIN

    SELECT *,
           (total_purchase + opening_stock - closing_stock) AS cogs,
           (total_sale-(total_purchase + opening_stock - closing_stock)) as gross_profit
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

            (
                IFNULL((
                    SELECT SUM(AES_DECRYPT(sd.total_price, p_key) + 0)
                    FROM purchase s
                    INNER JOIN purchase_details sd ON sd.pid = s.id
                    WHERE sd.product = p.id
                    AND (p_type2 IS NULL OR s.type2 = AES_ENCRYPT(p_type2, p_key))
                    AND s.date BETWEEN p_from_date AND p_to_date
                ), 0)
                -
                IFNULL((
                    SELECT SUM(AES_DECRYPT(sd.total_price, p_key) + 0)
                    FROM purchase_return s
                    INNER JOIN purchase_return_details sd ON sd.pid = s.id
                    WHERE sd.product = p.id
                    AND (p_type2 IS NULL OR s.type2 = AES_ENCRYPT(p_type2, p_key))
                    AND s.date BETWEEN p_from_date AND p_to_date
                ), 0)
            ) AS total_purchase,

            (
                IFNULL((
                    SELECT 
                        SUM(AES_DECRYPT(s.stock, p_key) + 0) *
                        MAX(AES_DECRYPT(pi.cost_price, p_key) + 0)
                    FROM stock_details s
                    INNER JOIN product_information pi ON pi.id = s.product
                    WHERE s.product = p.id
                    AND s.date < p_from_date
                    GROUP BY s.product
                ), 0)
            ) AS opening_stock,

            (
                IFNULL((
                    SELECT 
                        SUM(AES_DECRYPT(s.stock, p_key) + 0) *
                        MAX(AES_DECRYPT(pi.cost_price, p_key) + 0)
                    FROM stock_details s
                    INNER JOIN product_information pi ON pi.id = s.product
                    WHERE s.product = p.id
                    AND s.date < p_to_date
                    GROUP BY s.product
                ), 0)
            ) AS closing_stock

        FROM product_information p
        INNER JOIN product_category pc ON pc.category_id = p.category_id
        WHERE 
          (p_product_id IS NULL OR p.id = p_product_id)
           AND (p_category_id IS NULL OR pc.category_id = p_category_id)
        order by pc.category_id desc
    ) t;

END$$

DELIMITER ;