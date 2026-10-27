-- Stock Audit Report: add Batch to audit_stock.
--
-- audit_stock never recorded which batch a movement belonged to. Every code path that writes an
-- audit row (32 sites across Stock.php / Purchase.php / Invoice.php) now writes `batch` too, from
-- the same batch value it writes to stock_details / phystock_details on the same line. This
-- migration adds the column and backfills rows written before that change. Safe to re-run.

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'audit_stock' AND COLUMN_NAME = 'batch');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE audit_stock ADD COLUMN batch INT(11) NOT NULL DEFAULT 0 AFTER product', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Backfill: find each existing audit row's ledger row — same voucher (pid), product and store,
-- and the ledger `type` its scenario writes — and copy that row's batch. The Actual ledger
-- (stock_details) is tried first, then the Physical ledger (phystock_details, the only one GRN /
-- GDN / physical-only adjustments write to). Within each, a row whose quantity also matches the
-- audit row's own quantity is preferred, which disambiguates the case of one voucher carrying
-- the same product in two different batches at the same store; without a quantity match the
-- first matching ledger row is used. Rows that still can't be resolved stay at 0 (shown as
-- "Default"/blank in the report) rather than guessing further.
SET @k = 'Th@#981420314';

UPDATE audit_stock a
SET a.batch = COALESCE(
    (SELECT sd.batch FROM stock_details sd
      WHERE sd.pid = a.pid AND sd.product = a.product AND sd.store = a.store
        AND sd.type = CASE a.scenario
            WHEN 'purchase' THEN 'purchase'
            WHEN 'sale' THEN 'sales'
            WHEN 'Purchase Return' THEN 'purchase_return'
            WHEN 'Sales Return' THEN 'sales_return'
            WHEN 'Inventory Transaction' THEN 'adj_stock'
            ELSE NULL END
        AND AES_DECRYPT(sd.stock, @k) + 0 = AES_DECRYPT(a.astock, @k) + 0
      ORDER BY sd.id LIMIT 1),
    (SELECT pd.batch FROM phystock_details pd
      WHERE pd.pid = a.pid AND pd.product = a.product AND pd.store = a.store
        AND pd.type = CASE a.scenario
            WHEN 'purchase' THEN 'purchase'
            WHEN 'sale' THEN 'sales'
            WHEN 'Purchase Return' THEN 'purchase_return'
            WHEN 'Sales Return' THEN 'sales_return'
            WHEN 'Inventory Transaction' THEN 'adj_stock'
            WHEN 'GRN' THEN 'grn_stock'
            WHEN 'GDN' THEN 'gdn_stock'
            ELSE NULL END
        AND AES_DECRYPT(pd.stock, @k) + 0 = AES_DECRYPT(a.pstock, @k) + 0
      ORDER BY pd.id LIMIT 1),
    (SELECT sd.batch FROM stock_details sd
      WHERE sd.pid = a.pid AND sd.product = a.product AND sd.store = a.store
        AND sd.type = CASE a.scenario
            WHEN 'purchase' THEN 'purchase'
            WHEN 'sale' THEN 'sales'
            WHEN 'Purchase Return' THEN 'purchase_return'
            WHEN 'Sales Return' THEN 'sales_return'
            WHEN 'Inventory Transaction' THEN 'adj_stock'
            ELSE NULL END
      ORDER BY sd.id LIMIT 1),
    (SELECT pd.batch FROM phystock_details pd
      WHERE pd.pid = a.pid AND pd.product = a.product AND pd.store = a.store
        AND pd.type = CASE a.scenario
            WHEN 'purchase' THEN 'purchase'
            WHEN 'sale' THEN 'sales'
            WHEN 'Purchase Return' THEN 'purchase_return'
            WHEN 'Sales Return' THEN 'sales_return'
            WHEN 'Inventory Transaction' THEN 'adj_stock'
            WHEN 'GRN' THEN 'grn_stock'
            WHEN 'GDN' THEN 'gdn_stock'
            ELSE NULL END
      ORDER BY pd.id LIMIT 1),
    0)
WHERE a.batch = 0;
