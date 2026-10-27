-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 02, 2026 at 07:33 PM
-- Server version: 10.11.19-MariaDB
-- PHP Version: 8.4.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fextenso_erplite`
--

DELIMITER $$
--
-- Procedures
--
CREATE  PROCEDURE `GetGrossProfitReport` (IN `p_from_date` DATE, IN `p_to_date` DATE, IN `p_key` VARCHAR(100), IN `p_type2` VARCHAR(10))   BEGIN

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

END$$

CREATE  PROCEDURE `GrossProfitReportCategorywise` (IN `p_from_date` DATE, IN `p_to_date` DATE, IN `p_type2` VARCHAR(10), IN `p_key` VARCHAR(100), IN `p_product_id` INT, IN `p_category_id` INT)   BEGIN

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

-- --------------------------------------------------------

--
-- Table structure for table `acc_coa`
--

CREATE TABLE `acc_coa` (
  `id` int(11) NOT NULL,
  `HeadCode` int(11) NOT NULL,
  `HeadName` varchar(100) NOT NULL,
  `PHeadName` varchar(200) NOT NULL,
  `PHeadCode` varchar(50) DEFAULT NULL,
  `HeadLevel` int(11) NOT NULL,
  `IsActive` tinyint(1) NOT NULL,
  `IsTransaction` tinyint(1) NOT NULL,
  `IsGL` tinyint(1) NOT NULL,
  `isCashNature` int(11) NOT NULL DEFAULT 0,
  `isBankNature` int(11) NOT NULL DEFAULT 0,
  `HeadType` char(1) NOT NULL,
  `IsBudget` tinyint(1) NOT NULL,
  `IsDepreciation` tinyint(1) NOT NULL,
  `customer_id` varchar(50) DEFAULT NULL,
  `supplier_id` varchar(50) DEFAULT NULL,
  `bank_id` varchar(100) DEFAULT NULL,
  `service_id` varchar(50) DEFAULT NULL,
  `DepreciationRate` decimal(18,2) NOT NULL,
  `CreateBy` varchar(50) NOT NULL,
  `CreateDate` datetime NOT NULL,
  `UpdateBy` varchar(50) NOT NULL,
  `UpdateDate` datetime NOT NULL,
  `isSubType` int(11) NOT NULL DEFAULT 0,
  `subType` int(11) NOT NULL DEFAULT 1,
  `isStock` int(11) NOT NULL DEFAULT 0,
  `isFixedAssetSch` int(11) NOT NULL DEFAULT 0,
  `noteNo` varchar(20) DEFAULT NULL,
  `assetCode` varchar(20) DEFAULT NULL,
  `depCode` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `acc_coa`
--

INSERT INTO `acc_coa` (`id`, `HeadCode`, `HeadName`, `PHeadName`, `PHeadCode`, `HeadLevel`, `IsActive`, `IsTransaction`, `IsGL`, `isCashNature`, `isBankNature`, `HeadType`, `IsBudget`, `IsDepreciation`, `customer_id`, `supplier_id`, `bank_id`, `service_id`, `DepreciationRate`, `CreateBy`, `CreateDate`, `UpdateBy`, `UpdateDate`, `isSubType`, `subType`, `isStock`, `isFixedAssetSch`, `noteNo`, `assetCode`, `depCode`) VALUES
(8, 50202, 'Accounts Payable', 'Current Liabilities', '502', 3, 1, 0, 1, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-05-17 12:52:17', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(10, 1, 'Assets', 'COA', '0', 1, 1, 0, 0, 0, 0, 'A', 0, 0, NULL, NULL, NULL, NULL, 0.00, '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, '', '', ''),
(13, 10201, 'Cash', 'Current Asset', '102', 3, 1, 0, 0, 0, 0, 'A', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-10 12:07:11', '0', '2015-10-15 15:57:55', 0, 1, 0, 0, NULL, NULL, NULL),
(15, 1020101, 'Cash In Hand', 'Cash', '10201', 4, 1, 1, 1, 1, 0, 'A', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-12 08:16:03', '0', '2016-05-23 12:05:43', 0, 1, 0, 0, NULL, NULL, NULL),
(16, 102, 'Current Asset', 'Assets', '1', 2, 1, 0, 0, 0, 0, 'A', 0, 0, NULL, NULL, NULL, NULL, 0.00, '0', '0000-00-00 00:00:00', '0', '2018-07-07 11:23:00', 0, 1, 0, 0, '', '', ''),
(17, 502, 'Current Liabilities', 'Liabilities', '5', 2, 1, 0, 0, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '0', '2014-08-30 13:18:20', '0', '2015-10-15 19:49:21', 0, 1, 0, 0, '', '', ''),
(23, 401, 'Cost of Goods Solds', 'Expenses', '4', 2, 1, 1, 1, 0, 0, 'E', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-02 10:28:34', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, '', '', ''),
(24, 2, 'Shareholder\'s Equity', 'COA', '0', 1, 1, 0, 0, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, '', '', ''),
(25, 4, 'Expenses', 'COA', '0', 1, 1, 0, 0, 0, 0, 'E', 0, 0, NULL, NULL, NULL, NULL, 0.00, '2', '2019-11-24 05:45:24', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, '', '', ''),
(26, 101, 'Fixed Assets', 'Assets', '1', 2, 1, 0, 0, 0, 0, 'A', 0, 0, NULL, NULL, NULL, NULL, 0.00, '0', '0000-00-00 00:00:00', '0', '2015-10-15 15:29:11', 0, 1, 0, 0, '', '', ''),
(27, 402, 'Over Head Cost', 'Expenses', '4', 2, 1, 1, 1, 0, 0, 'E', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-04 10:01:58', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, '', '', ''),
(29, 3, 'Income', 'COA', '0', 1, 1, 0, 0, 0, 0, 'I', 0, 0, NULL, NULL, NULL, NULL, 0.00, '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, '', '', ''),
(30, 5, 'Liabilities', 'COA', '0', 1, 1, 0, 0, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '0', '2013-07-04 12:32:07', '0', '2015-10-15 19:46:54', 0, 1, 0, 0, '', '', ''),
(31, 501, 'Long Term Liabilities', 'Liabilities', '5', 2, 1, 0, 0, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '0', '2014-08-30 13:18:20', '0', '2015-10-15 19:49:21', 0, 1, 0, 0, '', '', ''),
(33, 301, 'Direct Income', 'Income', '3', 2, 1, 1, 1, 0, 0, 'I', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-02 10:31:45', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, '', '', ''),
(35, 302, 'Indirect Income', 'Income', '3', 2, 1, 1, 1, 0, 0, 'I', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-02 10:55:45', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, '', '', ''),
(36, 5020201, 'Supplier Payable', 'Accounts Payable', '50202', 4, 1, 0, 1, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-05-17 12:52:44', '0', '0000-00-00 00:00:00', 1, 4, 0, 0, NULL, NULL, NULL),
(41, 10203, 'Prepaid Expenses', 'Current Asset', '102', 3, 1, 0, 0, 0, 0, 'A', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-10 06:45:19', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(43, 10204, 'Inventory', 'Current Asset', '102', 3, 1, 0, 0, 0, 0, 'A', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-10 06:48:32', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(44, 50203, 'Accrued Expenses', 'Current Liabilities', '502', 3, 1, 0, 0, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-10 06:50:20', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(46, 50101, 'Long-Term Debt', 'Long Term Liabilities', '501', 3, 1, 0, 0, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-10 06:51:45', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(47, 50102, 'Other Long-Term  Liabilities', 'Long Term Liabilities', '501', 3, 1, 0, 0, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-10 06:53:04', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(48, 201, 'Equity', 'Shareholder\'s Equity', '2', 2, 1, 0, 0, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '0', '2022-04-10 06:56:38', '0', '2022-04-10 06:56:38', 0, 1, 0, 0, NULL, NULL, NULL),
(49, 20101, 'Equity Capital', 'Equity', '201', 3, 1, 0, 0, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-05-17 12:31:33', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(50, 20102, 'Retained Earnings', 'Equity', '201', 3, 1, 0, 0, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-10 07:01:45', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(51, 10101, 'Property & Equipment', 'Fixed Assets', '101', 3, 1, 0, 0, 0, 0, 'A', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-10 11:35:53', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(52, 10102, 'Goodwills', 'Fixed Assets', '101', 3, 1, 0, 0, 0, 0, 'A', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-23 06:47:21', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(54, 30102, 'Reimbursement Income', 'Direct Income', '301', 3, 1, 0, 0, 0, 0, 'I', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-10 08:09:02', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(55, 40101, 'Cost of Goods Sold', 'Cost of Goods Solds', '401', 3, 1, 0, 0, 0, 0, 'E', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-10 08:13:52', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(56, 40102, 'Job Expenses', 'Cost of Goods Solds', '401', 3, 1, 0, 0, 0, 0, 'E', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-10 08:14:18', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(57, 40201, 'Automobile', 'Over Head Cost', '402', 3, 1, 0, 0, 0, 0, 'E', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-10 08:14:37', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(58, 40202, 'Bank Service Charges', 'Over Head Cost', '402', 3, 1, 0, 0, 0, 0, 'E', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-10 08:15:32', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(59, 40203, 'Insurance', 'Over Head Cost', '402', 3, 1, 0, 0, 0, 0, 'E', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-10 08:15:58', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(60, 40204, 'Interest Expenses', 'Over Head Cost', '402', 3, 1, 0, 0, 0, 0, 'E', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-10 08:16:36', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(61, 40205, 'Payroll Expenses', 'Over Head Cost', '402', 3, 1, 0, 0, 0, 0, 'E', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-10 08:17:08', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(62, 40206, 'Postage', 'Over Head Cost', '402', 3, 1, 0, 0, 0, 0, 'E', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-10 08:17:26', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(63, 40207, 'Professional Fees', 'Over Head Cost', '402', 3, 1, 0, 0, 0, 0, 'E', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-10 08:17:55', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(64, 40208, 'Repairs', 'Over Head Cost', '402', 3, 1, 0, 0, 0, 0, 'E', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-10 08:18:38', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(65, 40209, 'Tools and Macchnery', 'Over Head Cost', '402', 3, 1, 0, 0, 0, 0, 'E', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-10 08:28:02', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(66, 40210, 'Utilities', 'Over Head Cost', '402', 3, 1, 0, 0, 0, 0, 'E', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-10 08:28:42', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(67, 4021001, 'Electic Bill', 'Utilities', '40210', 4, 1, 0, 0, 0, 0, 'E', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-10 09:05:45', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(68, 4021002, 'House Rent', 'Utilities', '40210', 4, 1, 0, 0, 0, 0, 'E', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-10 09:06:05', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(69, 10205, 'Cash at Bank', 'Current Asset', '102', 3, 1, 0, 0, 0, 0, 'A', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-05-17 12:44:19', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(75, 1010201, 'Goodwill', 'Goodwills', '10102', 4, 1, 0, 0, 0, 0, 'A', 0, 0, NULL, NULL, NULL, NULL, 15.00, '1', '2022-04-23 06:45:48', '0', '0000-00-00 00:00:00', 0, 1, 0, 1, NULL, 'GD001', NULL),
(77, 5020401, 'property sales', 'Unearned Revenue', '50204', 4, 1, 0, 0, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-10 11:40:48', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(78, 5010101, 'Debts', 'Long-Term Debt', '50101', 4, 1, 0, 0, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-10 11:41:49', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(79, 5010201, 'Other Long-Term  Liabilities', 'Other Long-Term  Liabilities', '50102', 4, 1, 0, 0, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-10 11:42:03', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(80, 2010101, 'Capital Fund', 'Equity Capital', '20101', 4, 1, 0, 0, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-10 11:42:36', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(81, 2010201, 'Current year Profit & Loss', 'Retained Earnings', '20102', 4, 1, 0, 0, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-10 11:42:53', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(82, 2010202, 'Last year Profit & Loss', 'Retained Earnings', '20102', 4, 1, 0, 0, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-10 11:43:03', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(84, 50201, 'Provisions', 'Current Liabilities', '502', 3, 1, 0, 0, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-10 11:46:00', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(85, 5020104, 'Depreciation of Goodwill', 'Depreciations', '50205', 4, 1, 0, 0, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-23 06:47:07', '0', '0000-00-00 00:00:00', 0, 1, 0, 1, NULL, NULL, 'GD001'),
(86, 50204, 'Unearned Revenue', 'Current Liabilities', '502', 3, 1, 0, 0, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-05-17 12:53:09', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(88, 10206, 'Advance', 'Current Asset', '102', 3, 1, 0, 0, 0, 0, 'A', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-11 11:56:56', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(89, 1020601, 'Advance against Employee', 'Advance', '10206', 4, 1, 0, 0, 0, 0, 'A', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-11 11:57:18', '0', '0000-00-00 00:00:00', 1, 2, 0, 0, NULL, NULL, NULL),
(90, 1020602, 'Advance Against Customer', 'Advance', '10206', 4, 1, 0, 0, 0, 0, 'A', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-11 11:57:35', '0', '0000-00-00 00:00:00', 1, 3, 0, 0, NULL, NULL, NULL),
(91, 1020102, 'Petty Cash', 'Cash', '10201', 4, 1, 0, 0, 1, 0, 'A', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-12 08:16:19', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(94, 40301, 'Purchase Account', 'Purchase Accounts', '402', 3, 1, 0, 0, 0, 0, 'E', 0, 0, NULL, NULL, NULL, NULL, 0.00, '0', '2022-04-16 10:31:43', '0', '2022-04-16 10:31:43', 0, 1, 0, 0, NULL, NULL, NULL),
(95, 4030102, 'Purchase', 'Purchase Account', '40201', 4, 1, 0, 0, 0, 0, 'E', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-16 10:33:59', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(96, 30103, 'Sales Accounts', 'Direct Income', '301', 3, 1, 0, 0, 0, 0, 'I', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-16 10:34:37', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(97, 3010301, 'Sales Account', 'Sales Accounts', '30103', 4, 1, 0, 0, 0, 0, 'I', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-16 10:34:57', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(99, 4020501, 'Salary Expense', 'Payroll Expenses', '40205', 4, 1, 0, 0, 0, 0, 'E', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-20 06:24:08', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(104, 5020102, 'Provision for npf contribution', 'Provisions', '50201', 4, 1, 0, 0, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-05-10 06:19:45', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(106, 5020101, 'Provision for State Income Tax', 'Provisions', '50201', 4, 1, 0, 0, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-26 06:44:29', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(107, 40211, 'State Income Tax', 'Over Head Cost', '402', 3, 1, 0, 0, 0, 0, 'E', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-26 06:44:46', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(108, 4021101, 'State Income Tax', 'State Income Tax', '40211', 4, 1, 0, 0, 0, 0, 'E', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-04-26 06:45:00', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(110, 40212, 'Employeer ICF Expense', 'Over Head Cost', '402', 3, 1, 0, 0, 0, 0, 'E', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-05-10 06:35:37', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(111, 4021201, 'Employeer 1% ICF Expense', 'Employeer ICF Expense', '40212', 4, 1, 0, 0, 0, 0, 'E', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-05-17 11:34:02', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(112, 50205, 'Depreciations', 'Current Liabilities', '502', 3, 1, 0, 0, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '0', '2022-05-10 09:35:15', '0', '2022-05-10 09:35:15', 0, 1, 0, 0, NULL, NULL, NULL),
(191, 4020502, 'Employee 5 % NPF Expenses', 'Payroll Expenses', '40205', 4, 1, 0, 0, 0, 0, 'E', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-05-17 11:32:14', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(192, 4020503, 'Employee 10 % NPF Expenses', 'Payroll Expenses', '40205', 4, 1, 0, 0, 0, 0, 'E', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-05-17 11:32:36', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(221, 50206, 'Aoounts pay by name supplier', 'Current Liabilities', '502', 3, 1, 0, 0, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-06-13 11:51:32', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(229, 10208, 'Accounts Receivable', 'Current Asset', '102', 3, 1, 0, 0, 0, 0, 'A', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-06-16 07:05:42', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(230, 1020801, 'Customer Receivable', 'Accounts Receivable', '10208', 4, 1, 0, 0, 0, 0, 'A', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-06-16 07:07:17', '0', '0000-00-00 00:00:00', 1, 3, 0, 0, NULL, NULL, NULL),
(231, 1020802, 'Employee Receivable', 'Accounts Receivable', '10208', 4, 1, 0, 0, 0, 0, 'A', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-06-16 07:06:58', '0', '0000-00-00 00:00:00', 1, 2, 0, 0, NULL, NULL, NULL),
(232, 1020401, 'Inventory account', 'Inventory', '10204', 4, 1, 0, 0, 0, 0, 'A', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-06-15 07:53:16', '', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(233, 4010101, 'Cost of Goods Sold Account', 'Cost of Goods Sold', '40101', 4, 1, 0, 0, 0, 0, 'E', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-06-15 08:58:18', '', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(234, 30104, 'Service Accounts', 'Direct Income', '301', 3, 1, 0, 0, 0, 0, 'I', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-06-15 16:23:37', '', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(235, 3010401, 'Service Account', 'Service Accounts', '30104', 4, 1, 0, 0, 0, 0, 'I', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2022-06-15 16:24:11', '', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(236, 1020501, 'Test_Cash', 'Cash at Bank', '10205', 4, 1, 0, 0, 0, 1, 'A', 0, 0, '0', '0', '0', '0', 0.00, '1', '2024-03-04 20:43:07', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, '0', '0', '0'),
(237, 1020502, 'Test_Card', 'Cash at Bank', '10205', 4, 1, 0, 0, 0, 1, 'A', 0, 0, '0', '0', '0', '0', 0.00, '1', '2024-03-04 20:44:39', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, '0', '0', '0'),
(238, 30201, 'Other Income', 'Indirect Income', '302', 3, 1, 0, 0, 0, 0, 'I', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2024-06-17 18:03:21', '', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(239, 3020101, 'House Rent Income', 'Other Income', '30201', 4, 1, 0, 0, 0, 0, 'I', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2024-06-17 18:03:34', '', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(240, 1020103, '3rd party cheque', 'Cash', '10201', 4, 1, 0, 0, 1, 0, 'A', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2024-06-18 14:31:19', '', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(241, 1020503, 'Test_Credit', 'Cash at Bank', '10205', 4, 1, 0, 0, 0, 1, 'A', 0, 0, '0', '0', '0', '0', 0.00, '1', '2024-06-30 06:36:27', '0', '0000-00-00 00:00:00', 0, 1, 0, 0, '0', '0', '0'),
(242, 1020803, 'Debtors', 'Accounts Receivable', '10208', 4, 1, 0, 0, 0, 0, 'A', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2024-07-05 16:58:44', '', '0000-00-00 00:00:00', 1, 7, 0, 0, NULL, NULL, NULL),
(243, 5020202, 'Creditors', 'Accounts Payable', '50202', 4, 1, 0, 0, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2024-07-05 16:59:16', '', '0000-00-00 00:00:00', 1, 8, 0, 0, NULL, NULL, NULL),
(244, 2116000001, '1-', 'Employee Ledger', NULL, 4, 1, 1, 0, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2025-02-27 16:42:24', '1', '2025-02-27 16:43:19', 0, 1, 0, 0, NULL, NULL, NULL),
(245, 2116000002, '2-', 'Employee Ledger', NULL, 4, 1, 1, 0, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2025-02-27 16:42:45', '1', '2025-02-27 16:43:12', 0, 1, 0, 0, NULL, NULL, NULL),
(246, 2116000003, '3-', 'Employee Ledger', NULL, 4, 1, 1, 0, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2025-03-10 08:48:49', '1', '2025-03-13 18:07:15', 0, 1, 0, 0, NULL, NULL, NULL),
(247, 2116000004, '4-', 'Employee Ledger', NULL, 4, 1, 1, 0, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2025-03-10 08:49:45', '1', '2025-03-13 18:07:48', 0, 1, 0, 0, NULL, NULL, NULL),
(248, 2116000005, '5-', 'Employee Ledger', NULL, 4, 1, 1, 0, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2025-03-10 08:51:52', '1', '2025-03-13 18:08:00', 0, 1, 0, 0, NULL, NULL, NULL),
(249, 2116000006, '6-', 'Employee Ledger', NULL, 4, 1, 1, 0, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2025-03-10 08:52:42', '1', '2025-03-13 18:08:12', 0, 1, 0, 0, NULL, NULL, NULL),
(250, 2116000007, '7-', 'Employee Ledger', NULL, 4, 1, 1, 0, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2025-08-30 19:53:31', '1', '2025-08-31 14:35:06', 0, 1, 0, 0, NULL, NULL, NULL),
(251, 2116000008, '8-001Test Employee', 'Employee Ledger', NULL, 4, 1, 1, 0, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2025-08-31 17:17:23', '', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL),
(252, 2116000009, '1-testpro', 'Employee Ledger', NULL, 4, 1, 1, 0, 0, 0, 'L', 0, 0, NULL, NULL, NULL, NULL, 0.00, '1', '2025-12-17 19:33:38', '', '0000-00-00 00:00:00', 0, 1, 0, 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `acc_monthly_balance`
--

CREATE TABLE `acc_monthly_balance` (
  `id` int(11) NOT NULL,
  `fyear` int(11) NOT NULL,
  `COAID` int(11) NOT NULL,
  `balance1` decimal(18,2) NOT NULL DEFAULT 0.00,
  `balance2` decimal(18,2) NOT NULL DEFAULT 0.00,
  `balance3` decimal(18,2) NOT NULL DEFAULT 0.00,
  `balance4` decimal(18,2) NOT NULL DEFAULT 0.00,
  `balance5` decimal(18,2) NOT NULL DEFAULT 0.00,
  `balance6` decimal(18,2) NOT NULL DEFAULT 0.00,
  `balance7` decimal(18,2) NOT NULL DEFAULT 0.00,
  `balance8` decimal(18,2) NOT NULL DEFAULT 0.00,
  `balance9` decimal(18,2) NOT NULL DEFAULT 0.00,
  `balance10` decimal(18,2) NOT NULL DEFAULT 0.00,
  `balance11` decimal(18,2) NOT NULL DEFAULT 0.00,
  `balance12` decimal(18,2) NOT NULL DEFAULT 0.00,
  `totalBalance` decimal(18,2) NOT NULL DEFAULT 0.00,
  `updatedDate` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `acc_monthly_balance`
--

INSERT INTO `acc_monthly_balance` (`id`, `fyear`, `COAID`, `balance1`, `balance2`, `balance3`, `balance4`, `balance5`, `balance6`, `balance7`, `balance8`, `balance9`, `balance10`, `balance11`, `balance12`, `totalBalance`, `updatedDate`) VALUES
(1, 1, 2010202, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-06-17 05:27:20'),
(2, 1, 1020501, 50000.00, 0.00, 0.00, 0.00, 0.00, 47800.00, 0.00, 200.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-08-05 02:22:21'),
(3, 1, 1020101, 50000.00, 0.00, 0.00, 0.00, 0.00, 47940.00, -4500.00, 729600.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-08-13 10:32:46'),
(4, 1, 1020801, 50000.00, 0.00, 0.00, 0.00, 0.00, 51840.00, 0.00, 500.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-08-11 05:37:45'),
(5, 1, 1020401, 2250.00, 0.00, 0.00, 0.00, 0.00, 7560.00, 4050.00, 270675.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-08-13 10:32:46'),
(6, 1, 5020201, 10000.00, 0.00, 0.00, 0.00, 0.00, 10950.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-06-24 08:20:05'),
(7, 1, 2010101, 142250.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-06-17 05:27:20'),
(8, 1, 3010301, 0.00, 0.00, 0.00, 0.00, 0.00, 22040.00, 800.00, 1155600.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-08-13 10:32:46'),
(9, 1, 4010101, 0.00, 0.00, 0.00, 0.00, 0.00, 14400.00, 450.00, 153825.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-08-13 10:32:46'),
(10, 1, 4021101, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-08-13 10:32:46'),
(11, 1, 5020101, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-08-13 10:32:46'),
(12, 1, 4021001, 0.00, 0.00, 0.00, 0.00, 0.00, 3000.00, 3700.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-07-05 06:29:32'),
(13, 1, 3020101, 0.00, 0.00, 0.00, 0.00, 0.00, 5000.00, 5700.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-07-05 06:29:32'),
(14, 1, 1020103, 0.00, 0.00, 0.00, 0.00, 0.00, 6800.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-08-05 02:22:21'),
(15, 1, 1020502, 0.00, 0.00, 0.00, 0.00, 0.00, 900.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-06-27 04:04:09'),
(16, 1, 5020202, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, -800.00, 202.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-08-05 02:16:24'),
(17, 1, 1020803, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, -300.00, 1002.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-08-05 02:20:09');

-- --------------------------------------------------------

--
-- Table structure for table `acc_opening_balance`
--

CREATE TABLE `acc_opening_balance` (
  `id` int(11) NOT NULL,
  `fyear` int(11) NOT NULL,
  `COAID` int(11) NOT NULL,
  `subType` int(11) NOT NULL DEFAULT 1,
  `subCode` int(11) DEFAULT NULL,
  `Debit` decimal(10,0) NOT NULL,
  `Credit` decimal(10,0) NOT NULL,
  `openDate` date NOT NULL,
  `CreateBy` int(11) NOT NULL,
  `CreateDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `acc_predefine_account`
--

CREATE TABLE `acc_predefine_account` (
  `id` int(11) NOT NULL,
  `cashCode` int(11) NOT NULL,
  `bankCode` int(11) NOT NULL,
  `advance` int(11) NOT NULL,
  `fixedAsset` int(11) NOT NULL,
  `purchaseCode` int(11) NOT NULL,
  `salesCode` int(11) NOT NULL,
  `serviceCode` int(11) NOT NULL,
  `customerCode` int(11) NOT NULL,
  `supplierCode` int(11) NOT NULL,
  `costs_of_good_solds` int(11) NOT NULL,
  `vat` int(11) NOT NULL,
  `tax` int(11) NOT NULL,
  `inventoryCode` int(11) NOT NULL,
  `CPLCode` int(11) NOT NULL,
  `LPLCode` int(11) NOT NULL,
  `salary_code` int(11) DEFAULT NULL,
  `emp_npf_contribution` int(11) DEFAULT NULL,
  `empr_npf_contribution` int(11) DEFAULT NULL,
  `emp_icf_contribution` int(11) DEFAULT NULL,
  `empr_icf_contribution` int(11) DEFAULT NULL,
  `prov_state_tax` int(11) NOT NULL,
  `state_tax` int(11) NOT NULL,
  `prov_npfcode` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `acc_predefine_account`
--

INSERT INTO `acc_predefine_account` (`id`, `cashCode`, `bankCode`, `advance`, `fixedAsset`, `purchaseCode`, `salesCode`, `serviceCode`, `customerCode`, `supplierCode`, `costs_of_good_solds`, `vat`, `tax`, `inventoryCode`, `CPLCode`, `LPLCode`, `salary_code`, `emp_npf_contribution`, `empr_npf_contribution`, `emp_icf_contribution`, `empr_icf_contribution`, `prov_state_tax`, `state_tax`, `prov_npfcode`) VALUES
(2, 10201, 10205, 10206, 101, 1020401, 3010301, 3010401, 1020801, 5020201, 4010101, 0, 4021101, 1020401, 2010201, 2010202, 4020501, 4020502, 4020503, 4021201, 0, 5020101, 4021101, 5020102);

-- --------------------------------------------------------

--
-- Table structure for table `acc_subcode`
--

CREATE TABLE `acc_subcode` (
  `id` int(11) NOT NULL,
  `subTypeId` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `referenceNo` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_by` int(11) NOT NULL,
  `created_date` date NOT NULL,
  `updated_by` int(11) NOT NULL,
  `updated_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `acc_subtype`
--

CREATE TABLE `acc_subtype` (
  `id` int(11) NOT NULL,
  `subtypeName` varchar(200) NOT NULL,
  `staus` int(11) NOT NULL DEFAULT 1,
  `created_by` int(11) NOT NULL,
  `created_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `acc_subtype`
--

INSERT INTO `acc_subtype` (`id`, `subtypeName`, `staus`, `created_by`, `created_date`) VALUES
(1, 'None', 1, 1, '2022-04-05 10:19:27'),
(2, 'Employee', 1, 1, '2022-04-06 08:14:48'),
(3, 'Customer', 1, 1, '2022-04-10 08:49:22'),
(4, 'Supplier', 1, 1, '2022-04-10 08:49:22'),
(6, 'Agent', 1, 1, '2022-04-10 08:50:12'),
(7, 'Debtors', 1, 1, '2024-07-05 21:54:30'),
(8, 'Creditors', 1, 1, '2024-07-05 21:54:30');

-- --------------------------------------------------------

--
-- Table structure for table `acc_transaction`
--

CREATE TABLE `acc_transaction` (
  `ID` int(11) NOT NULL,
  `vid` int(11) NOT NULL,
  `fyear` int(11) NOT NULL,
  `VNo` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `Vtype` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `referenceNo` varchar(20) DEFAULT NULL,
  `VDate` date DEFAULT NULL,
  `COAID` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `Narration` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `chequeNo` varchar(30) DEFAULT NULL,
  `chequeDate` date DEFAULT NULL,
  `isHonour` int(11) DEFAULT NULL,
  `ledgerComment` varchar(100) NOT NULL DEFAULT '0',
  `Debit` decimal(18,2) DEFAULT NULL,
  `Credit` decimal(18,2) DEFAULT NULL,
  `StoreID` int(11) NOT NULL DEFAULT 0,
  `IsPosted` char(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `is_opening` int(11) NOT NULL DEFAULT 0,
  `CreateBy` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  `UpdateBy` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `UpdateDate` datetime DEFAULT NULL,
  `IsAppove` char(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `RevCodde` bigint(20) NOT NULL,
  `subType` int(11) NOT NULL DEFAULT 1,
  `subCode` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `acc_vaucher`
--

CREATE TABLE `acc_vaucher` (
  `id` int(11) NOT NULL,
  `fyear` int(11) NOT NULL,
  `VNo` varchar(50) NOT NULL,
  `Vtype` varchar(50) NOT NULL,
  `referenceNo` varchar(50) DEFAULT NULL,
  `VDate` date NOT NULL,
  `COAID` int(11) NOT NULL,
  `Narration` varchar(255) DEFAULT NULL,
  `chequeno` varchar(30) DEFAULT NULL,
  `chequeDate` date DEFAULT NULL,
  `isHonour` int(11) NOT NULL DEFAULT 0,
  `ledgerComment` varchar(255) DEFAULT NULL,
  `Debit` decimal(18,2) NOT NULL DEFAULT 0.00,
  `Credit` decimal(18,2) NOT NULL DEFAULT 0.00,
  `RevCodde` int(11) NOT NULL,
  `subType` int(11) NOT NULL DEFAULT 1,
  `subCode` int(11) DEFAULT NULL,
  `isApproved` int(11) NOT NULL DEFAULT 0,
  `CreateBy` int(11) NOT NULL,
  `CreateDate` datetime NOT NULL,
  `UpdateBy` int(11) DEFAULT NULL,
  `UpdateDate` datetime DEFAULT NULL,
  `approvedBy` int(11) DEFAULT NULL,
  `approvedDate` datetime DEFAULT NULL,
  `isyearClosed` tinyint(1) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '0 = non yet transfer to transation,  1 = Tranfer to transition',
  `type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `acc_vaucher`
--

INSERT INTO `acc_vaucher` (`id`, `fyear`, `VNo`, `Vtype`, `referenceNo`, `VDate`, `COAID`, `Narration`, `chequeno`, `chequeDate`, `isHonour`, `ledgerComment`, `Debit`, `Credit`, `RevCodde`, `subType`, `subCode`, `isApproved`, `CreateBy`, `CreateDate`, `UpdateBy`, `UpdateDate`, `approvedBy`, `approvedDate`, `isyearClosed`, `status`, `type`) VALUES
(1, 0, 'CT-1', 'CT', NULL, '2026-05-10', 2, '', '', NULL, 0, '', 100.00, 0.00, 1, 1, NULL, 0, 1, '2026-05-10 19:20:48', NULL, NULL, NULL, NULL, 0, 0, ''),
(2, 0, 'DV-1', 'DV', NULL, '2026-05-11', 0, '', '', NULL, 0, '', 50.00, 0.00, 1, 1, NULL, 0, 1, '2026-05-10 19:44:17', NULL, NULL, NULL, NULL, 0, 0, ''),
(3, 0, 'DV-1', 'DV', NULL, '2026-05-11', 0, '', '', NULL, 0, '', 60.00, 0.00, 1, 0, NULL, 0, 1, '2026-05-10 19:44:17', NULL, NULL, NULL, NULL, 0, 0, ''),
(4, 0, 'CV-1', 'CV', NULL, '2026-05-11', 0, '', '0', NULL, 0, '', 0.00, 40.00, 1, 0, NULL, 0, 1, '2026-05-10 19:44:43', NULL, NULL, NULL, NULL, 0, 0, ''),
(5, 0, 'CV-1', 'CV', NULL, '2026-05-11', 0, '', '0', NULL, 0, '', 0.00, 30.00, 1, 0, NULL, 0, 1, '2026-05-10 19:44:43', NULL, NULL, NULL, NULL, 0, 0, ''),
(6, 0, 'DV-2', 'DV', NULL, '2026-05-12', 0, '', '', NULL, 0, '', 50.00, 0.00, 1, 1, NULL, 0, 1, '2026-05-12 07:12:36', NULL, NULL, NULL, NULL, 0, 0, ''),
(7, 0, 'CT-2', 'CT', NULL, '2026-05-12', 2, '', '', NULL, 0, '', 100000.00, 0.00, 1, 1, NULL, 0, 1, '2026-05-12 07:18:28', NULL, NULL, NULL, NULL, 0, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `activity_id` int(11) NOT NULL,
  `user_id` varchar(11) NOT NULL COMMENT 'for employee , it will save employee_id',
  `type` varchar(30) NOT NULL COMMENT 'ticket, employee, attendnace etc.',
  `action` varchar(15) NOT NULL COMMENT 'create, update, delete',
  `action_id` varchar(15) NOT NULL,
  `table_name` varchar(30) DEFAULT NULL,
  `slug` varchar(150) NOT NULL,
  `form_data` text DEFAULT NULL,
  `create_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=created, 2=updated, 3=deleted	'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`activity_id`, `user_id`, `type`, `action`, `action_id`, `table_name`, `slug`, `form_data`, `create_date`, `status`) VALUES
(0, '1', 'financial_year', 'create', '1', 'financial_year', 'financial_year', '{\"yearName\":\"2024\",\"startDate\":\"2024-01-01\",\"endDate\":\"2024-12-31\",\"status\":\"1\",\"created_by\":\"1\",\"created_date\":\"2024-03-04 08:01:24\"}', '2024-03-05 00:31:24', 1),
(0, '1', 'credit_vaucher', 'create', 'CV-1', 'acc_vaucher', 'account/accounts/store_credit_voucher', '{\"fyear\":\"1\",\"VNo\":\"CV-1\",\"Vtype\":\"CV\",\"referenceNo\":null,\"VDate\":\"2024-01-01\",\"COAID\":\"2010202\",\"Narration\":\"Opening Balance\",\"chequeNo\":\"\",\"chequeDate\":\"2024-06-17\",\"isHonour\":0,\"ledgerComment\":\"Opening Balance\",\"Debit\":0,\"Credit\":\"50000\",\"RevCodde\":\"1020501\",\"subType\":\"1\",\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:06:22\",\"status\":0}', '2024-06-17 22:36:22', 1),
(0, '1', 'approved_vaucher_transation', 'create', '1', 'acc_transaction', 'account/accounts/isactive/CV-1/active', '{\"vid\":\"1\",\"fyear\":\"1\",\"VNo\":\"CV-1\",\"Vtype\":\"CV\",\"referenceNo\":null,\"VDate\":\"2024-01-01\",\"COAID\":\"2010202\",\"Narration\":\"Opening Balance\",\"chequeNo\":\"\",\"chequeDate\":\"2024-06-17\",\"isHonour\":\"0\",\"ledgerComment\":\"Opening Balance\",\"Debit\":\"0.00\",\"Credit\":\"50000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020501\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:07:20\"}', '2024-06-17 22:37:20', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '2', 'acc_transaction', 'account/accounts/isactive/CV-1/active', '{\"vid\":\"1\",\"fyear\":\"1\",\"VNo\":\"CV-1\",\"Vtype\":\"CV\",\"referenceNo\":null,\"VDate\":\"2024-01-01\",\"COAID\":\"1020501\",\"Narration\":\"Opening Balance\",\"chequeNo\":\"\",\"chequeDate\":\"2024-06-17\",\"isHonour\":\"0\",\"ledgerComment\":\"Opening Balance\",\"Debit\":\"50000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"2010202\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:07:20\"}', '2024-06-17 22:37:20', 1),
(0, '1', 'credit_vaucher', 'create', 'CV-2', 'acc_vaucher', 'account/accounts/store_credit_voucher', '{\"fyear\":\"1\",\"VNo\":\"CV-2\",\"Vtype\":\"CV\",\"referenceNo\":null,\"VDate\":\"2024-06-17\",\"COAID\":\"2010202\",\"Narration\":\"Opening Balance\",\"chequeNo\":\"\",\"chequeDate\":\"\",\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":0,\"Credit\":\"50000\",\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:09:23\",\"status\":0}', '2024-06-17 22:39:23', 1),
(0, '1', 'credit_vaucher', 'update', 'CV-2', 'acc_vaucher', 'account/accounts/update_credit_voucher', '{\"txtVNo\":\"CV-2\",\"isApproved\":\"0\",\"fyear\":\"1\",\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:09:23\",\"finyear\":\"1\",\"cmbDebit\":\"1020101\",\"dtpDate\":\"2024-06-17\",\"txtRemarks\":\"Opening Balance\",\"cmbCode\":[\"2010202\"],\"subtype\":[\"\"],\"isSubtype\":[\"1\"],\"txtComment\":[\"Opening Balance\"],\"txtAmount\":[\"50000.00\"],\"grand_total\":\"50000\",\"save\":\"Update\"}', '2024-06-17 22:39:30', 2),
(0, '1', 'credit_vaucher', 'update', 'CV-2', 'acc_vaucher', 'account/accounts/update_credit_voucher', '{\"txtVNo\":\"CV-2\",\"isApproved\":\"0\",\"fyear\":\"1\",\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:09:23\",\"finyear\":\"1\",\"cmbDebit\":\"1020101\",\"dtpDate\":\"2024-01-01\",\"txtRemarks\":\"Opening Balance\",\"cmbCode\":[\"2010202\"],\"subtype\":[\"\"],\"isSubtype\":[\"1\"],\"txtComment\":[\"Opening Balance\"],\"txtAmount\":[\"50000.00\"],\"grand_total\":\"50000\",\"save\":\"Update\"}', '2024-06-17 22:39:40', 2),
(0, '1', 'approved_vaucher_transation', 'create', '3', 'acc_transaction', 'account/accounts/isactive/CV-2/active', '{\"vid\":\"4\",\"fyear\":\"1\",\"VNo\":\"CV-2\",\"Vtype\":\"CV\",\"referenceNo\":null,\"VDate\":\"2024-01-01\",\"COAID\":\"2010202\",\"Narration\":\"Opening Balance\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Opening Balance\",\"Debit\":\"0.00\",\"Credit\":\"50000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:09:52\"}', '2024-06-17 22:39:52', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '4', 'acc_transaction', 'account/accounts/isactive/CV-2/active', '{\"vid\":\"4\",\"fyear\":\"1\",\"VNo\":\"CV-2\",\"Vtype\":\"CV\",\"referenceNo\":null,\"VDate\":\"2024-01-01\",\"COAID\":\"1020101\",\"Narration\":\"Opening Balance\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Opening Balance\",\"Debit\":\"50000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"2010202\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:09:52\"}', '2024-06-17 22:39:52', 1),
(0, '1', 'journal_vaucher', 'create', 'JV-1', 'acc_vaucher', 'account/accounts/store_journal_voucher', '{\"fyear\":\"1\",\"VNo\":\"JV-1\",\"Vtype\":\"JV\",\"referenceNo\":\"3\",\"VDate\":\"2024-01-01\",\"COAID\":\"1020801\",\"Narration\":\"Opening Balance\",\"chequeNo\":null,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"Opening Balance\",\"Debit\":\"50000\",\"Credit\":\"\",\"RevCodde\":\"2010202\",\"subType\":\"3\",\"subCode\":\"4\",\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:26:43\",\"status\":0}', '2024-06-17 22:56:43', 1),
(0, '1', 'journal_vaucher', 'create', 'JV-1', 'acc_vaucher', 'account/accounts/store_journal_voucher', '{\"fyear\":\"1\",\"VNo\":\"JV-1\",\"Vtype\":\"JV\",\"referenceNo\":null,\"VDate\":\"2024-01-01\",\"COAID\":\"1020401\",\"Narration\":\"Opening Balance\",\"chequeNo\":null,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"Opening Balance\",\"Debit\":\"2250\",\"Credit\":\"\",\"RevCodde\":\"2010202\",\"subType\":\"1\",\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:26:43\",\"status\":0}', '2024-06-17 22:56:43', 1),
(0, '1', 'journal_vaucher', 'create', 'JV-1', 'acc_vaucher', 'account/accounts/store_journal_voucher', '{\"fyear\":\"1\",\"VNo\":\"JV-1\",\"Vtype\":\"JV\",\"referenceNo\":null,\"VDate\":\"2024-01-01\",\"COAID\":\"5020201\",\"Narration\":\"Opening Balance\",\"chequeNo\":null,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"Opening Balance\",\"Debit\":\"\",\"Credit\":\"10000\",\"RevCodde\":\"2010202\",\"subType\":\"4\",\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:26:43\",\"status\":0}', '2024-06-17 22:56:43', 1),
(0, '1', 'journal_vaucher', 'create', 'JV-1', 'acc_vaucher', 'account/accounts/store_journal_voucher', '{\"fyear\":\"1\",\"VNo\":\"JV-1\",\"Vtype\":\"JV\",\"referenceNo\":null,\"VDate\":\"2024-01-01\",\"COAID\":\"2010101\",\"Narration\":\"Opening Balance\",\"chequeNo\":null,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"Opening Balance\",\"Debit\":\"\",\"Credit\":\"142250\",\"RevCodde\":\"2010202\",\"subType\":\"1\",\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:26:43\",\"status\":0}', '2024-06-17 22:56:43', 1),
(0, '1', 'journal_vaucher', 'update', 'JV-1', 'acc_vaucher', 'account/accounts/update_journal_voucher', '{\"txtVNo\":\"JV-1\",\"isApproved\":\"0\",\"fyear\":\"1\",\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:26:43\",\"finyear\":\"1\",\"dtpDate\":\"2024-01-01\",\"txtRemarks\":\"Opening Balance\",\"cmbCode\":[\"1020801\",\"1020401\",\"5020201\",\"2010101\"],\"subtype\":[\"4\",\"\",\"5\",\"\"],\"isSubtype\":[\"3\",\"1\",\"4\",\"1\"],\"txtComment\":[\"Opening Balance\",\"Opening Balance\",\"Opening Balance\",\"Opening Balance\"],\"txtAmount\":[\"50000.00\",\"2250.00\",\"0.00\",\"0.00\"],\"txtAmountcr\":[\"0.00\",\"0.00\",\"10000.00\",\"142250.00\"],\"cmbDebit\":[\"2010202\",\"2010202\",\"2010202\",\"2010202\"],\"grand_total\":\"52250\",\"grand_total1\":\"152250\",\"save\":\"Update\"}', '2024-06-17 22:57:06', 2),
(0, '1', 'journal_vaucher', 'update', 'JV-1', 'acc_vaucher', 'account/accounts/update_journal_voucher', '{\"txtVNo\":\"JV-1\",\"isApproved\":\"0\",\"fyear\":\"1\",\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:26:43\",\"finyear\":\"1\",\"dtpDate\":\"2024-01-01\",\"txtRemarks\":\"Opening Balance\",\"cmbCode\":[\"1020801\",\"1020401\",\"5020201\",\"2010101\"],\"subtype\":[\"4\",\"\",\"5\",\"\"],\"isSubtype\":[\"3\",\"1\",\"4\",\"1\"],\"txtComment\":[\"Opening Balance\",\"Opening Balance\",\"Opening Balance\",\"Opening Balance\"],\"txtAmount\":[\"50000.00\",\"2250.00\",\"0.00\",\"0.00\"],\"txtAmountcr\":[\"0.00\",\"0.00\",\"10000.00\",\"142250.00\"],\"cmbDebit\":[\"2010202\",\"2010202\",\"2010202\",\"2010202\"],\"grand_total\":\"52250\",\"grand_total1\":\"152250\",\"save\":\"Update\"}', '2024-06-17 22:57:06', 2),
(0, '1', 'journal_vaucher', 'update', 'JV-1', 'acc_vaucher', 'account/accounts/update_journal_voucher', '{\"txtVNo\":\"JV-1\",\"isApproved\":\"0\",\"fyear\":\"1\",\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:26:43\",\"finyear\":\"1\",\"dtpDate\":\"2024-01-01\",\"txtRemarks\":\"Opening Balance\",\"cmbCode\":[\"1020801\",\"1020401\",\"5020201\",\"2010101\"],\"subtype\":[\"4\",\"\",\"5\",\"\"],\"isSubtype\":[\"3\",\"1\",\"4\",\"1\"],\"txtComment\":[\"Opening Balance\",\"Opening Balance\",\"Opening Balance\",\"Opening Balance\"],\"txtAmount\":[\"50000.00\",\"2250.00\",\"0.00\",\"0.00\"],\"txtAmountcr\":[\"0.00\",\"0.00\",\"10000.00\",\"142250.00\"],\"cmbDebit\":[\"2010202\",\"2010202\",\"2010202\",\"2010202\"],\"grand_total\":\"52250\",\"grand_total1\":\"152250\",\"save\":\"Update\"}', '2024-06-17 22:57:06', 2),
(0, '1', 'journal_vaucher', 'update', 'JV-1', 'acc_vaucher', 'account/accounts/update_journal_voucher', '{\"txtVNo\":\"JV-1\",\"isApproved\":\"0\",\"fyear\":\"1\",\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:26:43\",\"finyear\":\"1\",\"dtpDate\":\"2024-01-01\",\"txtRemarks\":\"Opening Balance\",\"cmbCode\":[\"1020801\",\"1020401\",\"5020201\",\"2010101\"],\"subtype\":[\"4\",\"\",\"5\",\"\"],\"isSubtype\":[\"3\",\"1\",\"4\",\"1\"],\"txtComment\":[\"Opening Balance\",\"Opening Balance\",\"Opening Balance\",\"Opening Balance\"],\"txtAmount\":[\"50000.00\",\"2250.00\",\"0.00\",\"0.00\"],\"txtAmountcr\":[\"0.00\",\"0.00\",\"10000.00\",\"142250.00\"],\"cmbDebit\":[\"2010202\",\"2010202\",\"2010202\",\"2010202\"],\"grand_total\":\"52250\",\"grand_total1\":\"152250\",\"save\":\"Update\"}', '2024-06-17 22:57:06', 2),
(0, '1', 'approved_vaucher_transation', 'create', '5', 'acc_transaction', 'account/accounts/isactive/JV-1/active', '{\"vid\":\"9\",\"fyear\":\"1\",\"VNo\":\"JV-1\",\"Vtype\":\"JV\",\"referenceNo\":\"3\",\"VDate\":\"2024-01-01\",\"COAID\":\"1020801\",\"Narration\":\"Opening Balance\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Opening Balance\",\"Debit\":\"50000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"2010202\",\"subType\":\"3\",\"subCode\":\"4\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:27:20\"}', '2024-06-17 22:57:20', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '6', 'acc_transaction', 'account/accounts/isactive/JV-1/active', '{\"vid\":\"9\",\"fyear\":\"1\",\"VNo\":\"JV-1\",\"Vtype\":\"JV\",\"referenceNo\":\"3\",\"VDate\":\"2024-01-01\",\"COAID\":\"2010202\",\"Narration\":\"Opening Balance\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Opening Balance\",\"Debit\":\"0.00\",\"Credit\":\"50000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020801\",\"subType\":\"3\",\"subCode\":\"4\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:27:20\"}', '2024-06-17 22:57:20', 1),
(0, '1', 'approved_vaucher_transation', 'create', '7', 'acc_transaction', 'account/accounts/isactive/JV-1/active', '{\"vid\":\"10\",\"fyear\":\"1\",\"VNo\":\"JV-1\",\"Vtype\":\"JV\",\"referenceNo\":null,\"VDate\":\"2024-01-01\",\"COAID\":\"1020401\",\"Narration\":\"Opening Balance\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Opening Balance\",\"Debit\":\"2250.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"2010202\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:27:20\"}', '2024-06-17 22:57:20', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '8', 'acc_transaction', 'account/accounts/isactive/JV-1/active', '{\"vid\":\"10\",\"fyear\":\"1\",\"VNo\":\"JV-1\",\"Vtype\":\"JV\",\"referenceNo\":null,\"VDate\":\"2024-01-01\",\"COAID\":\"2010202\",\"Narration\":\"Opening Balance\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Opening Balance\",\"Debit\":\"0.00\",\"Credit\":\"2250.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:27:20\"}', '2024-06-17 22:57:20', 1),
(0, '1', 'approved_vaucher_transation', 'create', '9', 'acc_transaction', 'account/accounts/isactive/JV-1/active', '{\"vid\":\"11\",\"fyear\":\"1\",\"VNo\":\"JV-1\",\"Vtype\":\"JV\",\"referenceNo\":\"3\",\"VDate\":\"2024-01-01\",\"COAID\":\"5020201\",\"Narration\":\"Opening Balance\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Opening Balance\",\"Debit\":\"0.00\",\"Credit\":\"10000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"2010202\",\"subType\":\"4\",\"subCode\":\"5\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:27:20\"}', '2024-06-17 22:57:20', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '10', 'acc_transaction', 'account/accounts/isactive/JV-1/active', '{\"vid\":\"11\",\"fyear\":\"1\",\"VNo\":\"JV-1\",\"Vtype\":\"JV\",\"referenceNo\":\"3\",\"VDate\":\"2024-01-01\",\"COAID\":\"2010202\",\"Narration\":\"Opening Balance\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Opening Balance\",\"Debit\":\"10000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020201\",\"subType\":\"4\",\"subCode\":\"5\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:27:20\"}', '2024-06-17 22:57:20', 1),
(0, '1', 'approved_vaucher_transation', 'create', '11', 'acc_transaction', 'account/accounts/isactive/JV-1/active', '{\"vid\":\"12\",\"fyear\":\"1\",\"VNo\":\"JV-1\",\"Vtype\":\"JV\",\"referenceNo\":null,\"VDate\":\"2024-01-01\",\"COAID\":\"2010101\",\"Narration\":\"Opening Balance\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Opening Balance\",\"Debit\":\"0.00\",\"Credit\":\"142250.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"2010202\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:27:20\"}', '2024-06-17 22:57:20', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '12', 'acc_transaction', 'account/accounts/isactive/JV-1/active', '{\"vid\":\"12\",\"fyear\":\"1\",\"VNo\":\"JV-1\",\"Vtype\":\"JV\",\"referenceNo\":null,\"VDate\":\"2024-01-01\",\"COAID\":\"2010202\",\"Narration\":\"Opening Balance\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Opening Balance\",\"Debit\":\"142250.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"2010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:27:20\"}', '2024-06-17 22:57:20', 1),
(0, '1', 'approved_vaucher_transation', 'create', '13', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"13\",\"fyear\":\"1\",\"VNo\":\"DV-1\",\"Vtype\":\"DV\",\"referenceNo\":\"1\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"4500.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:04:04\"}', '2024-06-17 23:04:04', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '14', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"13\",\"fyear\":\"1\",\"VNo\":\"DV-1\",\"Vtype\":\"DV\",\"referenceNo\":\"1\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020101\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"4500.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:04:04\"}', '2024-06-17 23:04:04', 1),
(0, '1', 'approved_vaucher_transation', 'create', '15', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"14\",\"fyear\":\"1\",\"VNo\":\"DV-2\",\"Vtype\":\"DV\",\"referenceNo\":\"2\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"4500.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020501\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:05:52\"}', '2024-06-17 23:05:52', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '16', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"14\",\"fyear\":\"1\",\"VNo\":\"DV-2\",\"Vtype\":\"DV\",\"referenceNo\":\"2\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020501\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"4500.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:05:52\"}', '2024-06-17 23:05:52', 1),
(0, '1', 'approved_vaucher_transation', 'create', '17', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"15\",\"fyear\":\"1\",\"VNo\":\"JV-2\",\"Vtype\":\"JV\",\"referenceNo\":\"3\",\"VDate\":\"2024-06-17\",\"COAID\":\"5020201\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"2250.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"4\",\"subCode\":\"5\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:06:45\"}', '2024-06-17 23:06:45', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '18', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"15\",\"fyear\":\"1\",\"VNo\":\"JV-2\",\"Vtype\":\"JV\",\"referenceNo\":\"3\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"2250.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020201\",\"subType\":\"4\",\"subCode\":\"5\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:06:45\"}', '2024-06-17 23:06:45', 1),
(0, '1', 'approved_vaucher_transation', 'create', '19', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"16\",\"fyear\":\"1\",\"VNo\":\"DV-3\",\"Vtype\":\"DV\",\"referenceNo\":\"4\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"810.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:09:44\"}', '2024-06-17 23:09:44', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '20', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"16\",\"fyear\":\"1\",\"VNo\":\"DV-3\",\"Vtype\":\"DV\",\"referenceNo\":\"4\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020101\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"810.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:09:44\"}', '2024-06-17 23:09:44', 1),
(0, '1', 'approved_vaucher_transation', 'create', '21', 'acc_transaction', 'return/returns/return_suppliers', '{\"vid\":\"17\",\"fyear\":\"1\",\"VNo\":\"JV-3\",\"Vtype\":\"JV\",\"referenceNo\":\"3231159768\",\"VDate\":\"2024-06-17\",\"COAID\":\"5020201\",\"Narration\":\"Purchase Return Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Return Voucher for supplier\",\"Debit\":\"900.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"4\",\"subCode\":\"1\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:11:59\"}', '2024-06-17 23:11:59', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '22', 'acc_transaction', 'return/returns/return_suppliers', '{\"vid\":\"17\",\"fyear\":\"1\",\"VNo\":\"JV-3\",\"Vtype\":\"JV\",\"referenceNo\":\"3231159768\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Return Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Return Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"900.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020201\",\"subType\":\"4\",\"subCode\":\"1\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:11:59\"}', '2024-06-17 23:11:59', 1),
(0, '1', 'approved_vaucher_transation', 'create', '23', 'acc_transaction', 'return/returns/return_suppliers', '{\"vid\":\"18\",\"fyear\":\"1\",\"VNo\":\"DV-4\",\"Vtype\":\"DV\",\"referenceNo\":\"3231326182\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Return Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Return Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"2250.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:13:26\"}', '2024-06-17 23:13:26', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '24', 'acc_transaction', 'return/returns/return_suppliers', '{\"vid\":\"18\",\"fyear\":\"1\",\"VNo\":\"DV-4\",\"Vtype\":\"DV\",\"referenceNo\":\"3231326182\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020101\",\"Narration\":\"Purchase Return Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Return Voucher for supplier\",\"Debit\":\"2250.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:13:26\"}', '2024-06-17 23:13:26', 1),
(0, '1', 'approved_vaucher_transation', 'create', '25', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"19\",\"fyear\":\"1\",\"VNo\":\"CV-3\",\"Vtype\":\"CV\",\"referenceNo\":\"1000\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020101\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"3000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:16:19\"}', '2024-06-17 23:16:19', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '26', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"19\",\"fyear\":\"1\",\"VNo\":\"CV-3\",\"Vtype\":\"CV\",\"referenceNo\":\"1000\",\"VDate\":\"2024-06-17\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"3000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:16:19\"}', '2024-06-17 23:16:19', 1),
(0, '1', 'approved_vaucher_transation', 'create', '27', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"20\",\"fyear\":\"1\",\"VNo\":\"JV-4\",\"Vtype\":\"JV\",\"referenceNo\":\"1000\",\"VDate\":\"2024-06-17\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"2250.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:16:19\"}', '2024-06-17 23:16:19', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '28', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"20\",\"fyear\":\"1\",\"VNo\":\"JV-4\",\"Vtype\":\"JV\",\"referenceNo\":\"1000\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"2250.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:16:19\"}', '2024-06-17 23:16:19', 1),
(0, '1', 'approved_vaucher_transation', 'create', '29', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"21\",\"fyear\":\"1\",\"VNo\":\"JV-5\",\"Vtype\":\"JV\",\"referenceNo\":\"1000\",\"VDate\":\"2024-06-17\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:16:19\"}', '2024-06-17 23:16:19', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '30', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"21\",\"fyear\":\"1\",\"VNo\":\"JV-5\",\"Vtype\":\"JV\",\"referenceNo\":\"1000\",\"VDate\":\"2024-06-17\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:16:19\"}', '2024-06-17 23:16:19', 1),
(0, '1', 'approved_vaucher_transation', 'create', '31', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"22\",\"fyear\":\"1\",\"VNo\":\"CV-4\",\"Vtype\":\"CV\",\"referenceNo\":\"1001\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020501\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"3000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:17:50\"}', '2024-06-17 23:17:50', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '32', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"22\",\"fyear\":\"1\",\"VNo\":\"CV-4\",\"Vtype\":\"CV\",\"referenceNo\":\"1001\",\"VDate\":\"2024-06-17\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"3000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020501\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:17:50\"}', '2024-06-17 23:17:50', 1),
(0, '1', 'approved_vaucher_transation', 'create', '33', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"23\",\"fyear\":\"1\",\"VNo\":\"JV-6\",\"Vtype\":\"JV\",\"referenceNo\":\"1001\",\"VDate\":\"2024-06-17\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"2250.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:17:50\"}', '2024-06-17 23:17:50', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '34', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"23\",\"fyear\":\"1\",\"VNo\":\"JV-6\",\"Vtype\":\"JV\",\"referenceNo\":\"1001\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"2250.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:17:50\"}', '2024-06-17 23:17:50', 1),
(0, '1', 'approved_vaucher_transation', 'create', '35', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"24\",\"fyear\":\"1\",\"VNo\":\"JV-7\",\"Vtype\":\"JV\",\"referenceNo\":\"1001\",\"VDate\":\"2024-06-17\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:17:50\"}', '2024-06-17 23:17:50', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '36', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"24\",\"fyear\":\"1\",\"VNo\":\"JV-7\",\"Vtype\":\"JV\",\"referenceNo\":\"1001\",\"VDate\":\"2024-06-17\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:17:50\"}', '2024-06-17 23:17:50', 1),
(0, '1', 'approved_vaucher_transation', 'create', '37', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"25\",\"fyear\":\"1\",\"VNo\":\"JV-8\",\"Vtype\":\"JV\",\"referenceNo\":\"1002\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020801\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"3000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"3\",\"subCode\":\"4\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:19:06\"}', '2024-06-17 23:19:06', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '38', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"25\",\"fyear\":\"1\",\"VNo\":\"JV-8\",\"Vtype\":\"JV\",\"referenceNo\":\"1002\",\"VDate\":\"2024-06-17\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"3000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020801\",\"subType\":\"3\",\"subCode\":\"4\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:19:06\"}', '2024-06-17 23:19:06', 1),
(0, '1', 'approved_vaucher_transation', 'create', '39', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"26\",\"fyear\":\"1\",\"VNo\":\"JV-9\",\"Vtype\":\"JV\",\"referenceNo\":\"1002\",\"VDate\":\"2024-06-17\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"2250.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:19:06\"}', '2024-06-17 23:19:06', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '40', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"26\",\"fyear\":\"1\",\"VNo\":\"JV-9\",\"Vtype\":\"JV\",\"referenceNo\":\"1002\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"2250.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:19:06\"}', '2024-06-17 23:19:06', 1),
(0, '1', 'approved_vaucher_transation', 'create', '41', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"27\",\"fyear\":\"1\",\"VNo\":\"JV-10\",\"Vtype\":\"JV\",\"referenceNo\":\"1002\",\"VDate\":\"2024-06-17\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:19:06\"}', '2024-06-17 23:19:06', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '42', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"27\",\"fyear\":\"1\",\"VNo\":\"JV-10\",\"Vtype\":\"JV\",\"referenceNo\":\"1002\",\"VDate\":\"2024-06-17\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:19:06\"}', '2024-06-17 23:19:06', 1),
(0, '1', 'approved_vaucher_transation', 'create', '43', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"28\",\"fyear\":\"1\",\"VNo\":\"CV-5\",\"Vtype\":\"CV\",\"referenceNo\":\"1003\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020101\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"540.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:20:48\"}', '2024-06-17 23:20:48', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '44', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"28\",\"fyear\":\"1\",\"VNo\":\"CV-5\",\"Vtype\":\"CV\",\"referenceNo\":\"1003\",\"VDate\":\"2024-06-17\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"540.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:20:48\"}', '2024-06-17 23:20:48', 1),
(0, '1', 'approved_vaucher_transation', 'create', '45', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"29\",\"fyear\":\"1\",\"VNo\":\"JV-11\",\"Vtype\":\"JV\",\"referenceNo\":\"1003\",\"VDate\":\"2024-06-17\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:20:48\"}', '2024-06-17 23:20:48', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '46', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"29\",\"fyear\":\"1\",\"VNo\":\"JV-11\",\"Vtype\":\"JV\",\"referenceNo\":\"1003\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:20:48\"}', '2024-06-17 23:20:48', 1),
(0, '1', 'approved_vaucher_transation', 'create', '47', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"30\",\"fyear\":\"1\",\"VNo\":\"JV-12\",\"Vtype\":\"JV\",\"referenceNo\":\"1003\",\"VDate\":\"2024-06-17\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:20:48\"}', '2024-06-17 23:20:48', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '48', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"30\",\"fyear\":\"1\",\"VNo\":\"JV-12\",\"Vtype\":\"JV\",\"referenceNo\":\"1003\",\"VDate\":\"2024-06-17\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:20:48\"}', '2024-06-17 23:20:48', 1),
(0, '1', 'approved_vaucher_transation', 'create', '49', 'acc_transaction', 'return/returns/return_invoice', '{\"vid\":\"31\",\"fyear\":\"1\",\"VNo\":\"JV-13\",\"Vtype\":\"JV\",\"referenceNo\":\"3232231770\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020801\",\"Narration\":\"Sales Return Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Return Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"1800.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"3\",\"subCode\":\"4\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:22:31\"}', '2024-06-17 23:22:31', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '50', 'acc_transaction', 'return/returns/return_invoice', '{\"vid\":\"31\",\"fyear\":\"1\",\"VNo\":\"JV-13\",\"Vtype\":\"JV\",\"referenceNo\":\"3232231770\",\"VDate\":\"2024-06-17\",\"COAID\":\"3010301\",\"Narration\":\"Sales Return Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Return Voucher for customer\",\"Debit\":\"1800.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020801\",\"subType\":\"3\",\"subCode\":\"4\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:22:31\"}', '2024-06-17 23:22:31', 1),
(0, '1', 'approved_vaucher_transation', 'create', '51', 'acc_transaction', 'return/returns/return_invoice', '{\"vid\":\"32\",\"fyear\":\"1\",\"VNo\":\"JV-14\",\"Vtype\":\"JV\",\"referenceNo\":\"3232231770\",\"VDate\":\"2024-06-17\",\"COAID\":\"4010101\",\"Narration\":\"Sales Return Voucher for Cost of Goods\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Return cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"1350.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:22:31\"}', '2024-06-17 23:22:31', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '52', 'acc_transaction', 'return/returns/return_invoice', '{\"vid\":\"32\",\"fyear\":\"1\",\"VNo\":\"JV-14\",\"Vtype\":\"JV\",\"referenceNo\":\"3232231770\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020401\",\"Narration\":\"Sales Return Voucher for Cost of Goods\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Return cost of goods Voucher for customer\",\"Debit\":\"1350.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:22:31\"}', '2024-06-17 23:22:31', 1),
(0, '1', 'approved_vaucher_transation', 'create', '53', 'acc_transaction', 'account/accounts/create_customer_receive', '{\"vid\":\"33\",\"fyear\":\"1\",\"VNo\":\"CV-6\",\"Vtype\":\"CV\",\"referenceNo\":\"1002\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020801\",\"Narration\":\"Sales Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Due Voucher for Customer\",\"Debit\":\"0.00\",\"Credit\":\"1200.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"3\",\"subCode\":\"4\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:28:01\"}', '2024-06-17 23:28:01', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '54', 'acc_transaction', 'account/accounts/create_customer_receive', '{\"vid\":\"33\",\"fyear\":\"1\",\"VNo\":\"CV-6\",\"Vtype\":\"CV\",\"referenceNo\":\"1002\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020101\",\"Narration\":\"Sales Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Due Voucher for Customer\",\"Debit\":\"1200.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020801\",\"subType\":\"3\",\"subCode\":\"4\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:28:01\"}', '2024-06-17 23:28:01', 1),
(0, '1', 'coa_account', 'create', '238', 'acc_coa', 'account/accounts/insert_coa', '{\"HeadCode\":\"30201\",\"Pheadcode\":\"302\",\"HeadName\":\"House Rent\",\"PHeadName\":\"Indirect Income\",\"HeadLevel\":\"3\",\"IsActive\":\"1\",\"isStock\":0,\"isSubType\":0,\"DepreciationRate\":0,\"HeadType\":\"I\",\"IsBudget\":0,\"isCashNature\":0,\"isBankNature\":0,\"isFixedAssetSch\":0,\"assetCode\":null,\"depCode\":null,\"subType\":1,\"noteNo\":null,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 18:02:11\"}', '2024-06-17 23:32:11', 1),
(0, '1', 'coa_account', 'update', '30201', 'acc_coa', 'account/accounts/insert_coa', '{\"HeadCode\":\"30201\",\"Pheadcode\":\"302\",\"HeadName\":\"House Rent Income\",\"PHeadName\":\"Indirect Income\",\"HeadLevel\":\"3\",\"IsActive\":\"1\",\"isStock\":0,\"isSubType\":0,\"DepreciationRate\":0,\"HeadType\":\"I\",\"IsBudget\":0,\"isCashNature\":0,\"isBankNature\":0,\"isFixedAssetSch\":0,\"assetCode\":null,\"depCode\":null,\"subType\":1,\"noteNo\":null,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 18:02:30\"}', '2024-06-17 23:32:30', 2),
(0, '1', 'coa_account', 'update', '30201', 'acc_coa', 'account/accounts/insert_coa', '{\"HeadCode\":\"30201\",\"Pheadcode\":\"302\",\"HeadName\":\"Other Income\",\"PHeadName\":\"Indirect Income\",\"HeadLevel\":\"3\",\"IsActive\":\"1\",\"isStock\":0,\"isSubType\":0,\"DepreciationRate\":0,\"HeadType\":\"I\",\"IsBudget\":0,\"isCashNature\":0,\"isBankNature\":0,\"isFixedAssetSch\":0,\"assetCode\":null,\"depCode\":null,\"subType\":1,\"noteNo\":null,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 18:03:21\"}', '2024-06-17 23:33:21', 2),
(0, '1', 'coa_account', 'create', '239', 'acc_coa', 'account/accounts/insert_coa', '{\"HeadCode\":\"3020101\",\"Pheadcode\":\"30201\",\"HeadName\":\"House Rent Income\",\"PHeadName\":\"Other Income\",\"HeadLevel\":\"4\",\"IsActive\":\"1\",\"isStock\":0,\"isSubType\":0,\"DepreciationRate\":0,\"HeadType\":\"I\",\"IsBudget\":0,\"isCashNature\":0,\"isBankNature\":0,\"isFixedAssetSch\":0,\"assetCode\":null,\"depCode\":null,\"subType\":1,\"noteNo\":null,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 18:03:34\"}', '2024-06-17 23:33:34', 1),
(0, '1', 'credit_vaucher', 'create', 'CV-7', 'acc_vaucher', 'account/accounts/store_credit_voucher', '{\"fyear\":\"1\",\"VNo\":\"CV-7\",\"Vtype\":\"CV\",\"referenceNo\":null,\"VDate\":\"2024-06-17\",\"COAID\":\"3020101\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":\"\",\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":0,\"Credit\":\"5000\",\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 18:04:12\",\"status\":0}', '2024-06-17 23:34:12', 1),
(0, '1', 'debit_vaucher', 'create', 'DV-5', 'acc_vaucher', 'account/accounts/store_debit_voucher', '{\"fyear\":\"1\",\"VNo\":\"DV-5\",\"Vtype\":\"DV\",\"referenceNo\":null,\"VDate\":\"2024-06-17\",\"COAID\":\"4021001\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":\"\",\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":\"3000\",\"Credit\":0,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 18:05:14\",\"status\":0}', '2024-06-17 23:35:14', 1),
(0, '1', 'contra_vaucher', 'create', 'CT-1', 'acc_vaucher', 'account/accounts/store_contra_voucher', '{\"fyear\":\"1\",\"VNo\":\"CT-1\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-06-17\",\"COAID\":\"1020501\",\"Narration\":\"\",\"chequeNo\":null,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":\"2500\",\"Credit\":\"\",\"RevCodde\":\"1020101\",\"subType\":1,\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 18:05:53\",\"status\":0}', '2024-06-17 23:35:53', 1),
(0, '1', 'approved_vaucher_transation', 'create', '55', 'acc_transaction', 'account/accounts/bulk_voucher_approve', '{\"vid\":\"35\",\"fyear\":\"1\",\"VNo\":\"DV-5\",\"Vtype\":\"DV\",\"referenceNo\":null,\"VDate\":\"2024-06-17\",\"COAID\":\"4021001\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":\"0000-00-00\",\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"3000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 18:06:17\"}', '2024-06-17 23:36:17', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '56', 'acc_transaction', 'account/accounts/bulk_voucher_approve', '{\"vid\":\"35\",\"fyear\":\"1\",\"VNo\":\"DV-5\",\"Vtype\":\"DV\",\"referenceNo\":null,\"VDate\":\"2024-06-17\",\"COAID\":\"1020101\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":\"0000-00-00\",\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"0.00\",\"Credit\":\"3000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021001\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 18:06:17\"}', '2024-06-17 23:36:17', 1),
(0, '1', 'approved_vaucher_transation', 'create', '57', 'acc_transaction', 'account/accounts/bulk_voucher_approve', '{\"vid\":\"34\",\"fyear\":\"1\",\"VNo\":\"CV-7\",\"Vtype\":\"CV\",\"referenceNo\":null,\"VDate\":\"2024-06-17\",\"COAID\":\"3020101\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":\"0000-00-00\",\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"0.00\",\"Credit\":\"5000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 18:06:17\"}', '2024-06-17 23:36:17', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '58', 'acc_transaction', 'account/accounts/bulk_voucher_approve', '{\"vid\":\"34\",\"fyear\":\"1\",\"VNo\":\"CV-7\",\"Vtype\":\"CV\",\"referenceNo\":null,\"VDate\":\"2024-06-17\",\"COAID\":\"1020101\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":\"0000-00-00\",\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"5000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 18:06:17\"}', '2024-06-17 23:36:17', 1),
(0, '1', 'approved_vaucher_transation', 'create', '59', 'acc_transaction', 'account/accounts/bulk_voucher_approve', '{\"vid\":\"36\",\"fyear\":\"1\",\"VNo\":\"CT-1\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-06-17\",\"COAID\":\"1020501\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"2500.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 18:06:17\"}', '2024-06-17 23:36:17', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '60', 'acc_transaction', 'account/accounts/bulk_voucher_approve', '{\"vid\":\"36\",\"fyear\":\"1\",\"VNo\":\"CT-1\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-06-17\",\"COAID\":\"1020101\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"0.00\",\"Credit\":\"2500.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020501\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 18:06:17\"}', '2024-06-17 23:36:17', 1);
INSERT INTO `activity_logs` (`activity_id`, `user_id`, `type`, `action`, `action_id`, `table_name`, `slug`, `form_data`, `create_date`, `status`) VALUES
(0, '1', 'coa_account', 'create', '240', 'acc_coa', 'account/accounts/insert_coa', '{\"HeadCode\":\"1020103\",\"Pheadcode\":\"10201\",\"HeadName\":\"3rd Party Cheque\",\"PHeadName\":\"Cash\",\"HeadLevel\":\"4\",\"IsActive\":\"1\",\"isStock\":0,\"isSubType\":0,\"DepreciationRate\":0,\"HeadType\":\"A\",\"IsBudget\":0,\"isCashNature\":\"1\",\"isBankNature\":0,\"isFixedAssetSch\":0,\"assetCode\":null,\"depCode\":null,\"subType\":1,\"noteNo\":null,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-18 14:31:19\"}', '2024-06-18 20:01:19', 1),
(0, '1', 'approved_vaucher_transation', 'create', '61', 'acc_transaction', 'account/accounts/create_supplier_payment', '{\"vid\":\"37\",\"fyear\":\"1\",\"VNo\":\"DV-6\",\"Vtype\":\"DV\",\"referenceNo\":\"3\",\"VDate\":\"2024-06-20\",\"COAID\":\"5020201\",\"Narration\":\"Purchase Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Due Voucher for supplier\",\"Debit\":\"1350.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"4\",\"subCode\":\"5\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-20 18:15:47\"}', '2024-06-20 18:15:47', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '62', 'acc_transaction', 'account/accounts/create_supplier_payment', '{\"vid\":\"37\",\"fyear\":\"1\",\"VNo\":\"DV-6\",\"Vtype\":\"DV\",\"referenceNo\":\"3\",\"VDate\":\"2024-06-20\",\"COAID\":\"1020101\",\"Narration\":\"Purchase Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Due Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"1350.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020201\",\"subType\":\"4\",\"subCode\":\"5\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-20 18:15:47\"}', '2024-06-20 18:15:47', 1),
(0, '1', 'approved_vaucher_transation', 'create', '63', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"38\",\"fyear\":\"1\",\"VNo\":\"CV-8\",\"Vtype\":\"CV\",\"referenceNo\":\"1004\",\"VDate\":\"2024-06-21\",\"COAID\":\"1020103\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"600.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:25:00\"}', '2024-06-21 23:25:00', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '64', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"38\",\"fyear\":\"1\",\"VNo\":\"CV-8\",\"Vtype\":\"CV\",\"referenceNo\":\"1004\",\"VDate\":\"2024-06-21\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"600.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:25:00\"}', '2024-06-21 23:25:00', 1),
(0, '1', 'approved_vaucher_transation', 'create', '65', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"39\",\"fyear\":\"1\",\"VNo\":\"JV-15\",\"Vtype\":\"JV\",\"referenceNo\":\"1004\",\"VDate\":\"2024-06-21\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:25:00\"}', '2024-06-21 23:25:00', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '66', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"39\",\"fyear\":\"1\",\"VNo\":\"JV-15\",\"Vtype\":\"JV\",\"referenceNo\":\"1004\",\"VDate\":\"2024-06-21\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:25:00\"}', '2024-06-21 23:25:00', 1),
(0, '1', 'approved_vaucher_transation', 'create', '67', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"40\",\"fyear\":\"1\",\"VNo\":\"JV-16\",\"Vtype\":\"JV\",\"referenceNo\":\"1004\",\"VDate\":\"2024-06-21\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:25:00\"}', '2024-06-21 23:25:00', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '68', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"40\",\"fyear\":\"1\",\"VNo\":\"JV-16\",\"Vtype\":\"JV\",\"referenceNo\":\"1004\",\"VDate\":\"2024-06-21\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:25:00\"}', '2024-06-21 23:25:00', 1),
(0, '1', 'approved_vaucher_transation', 'create', '69', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"41\",\"fyear\":\"1\",\"VNo\":\"CV-9\",\"Vtype\":\"CV\",\"referenceNo\":\"1005\",\"VDate\":\"2024-06-21\",\"COAID\":\"1020103\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"600.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:29:08\"}', '2024-06-21 23:29:08', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '70', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"41\",\"fyear\":\"1\",\"VNo\":\"CV-9\",\"Vtype\":\"CV\",\"referenceNo\":\"1005\",\"VDate\":\"2024-06-21\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"600.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:29:08\"}', '2024-06-21 23:29:08', 1),
(0, '1', 'approved_vaucher_transation', 'create', '71', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"42\",\"fyear\":\"1\",\"VNo\":\"JV-17\",\"Vtype\":\"JV\",\"referenceNo\":\"1005\",\"VDate\":\"2024-06-21\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:29:08\"}', '2024-06-21 23:29:08', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '72', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"42\",\"fyear\":\"1\",\"VNo\":\"JV-17\",\"Vtype\":\"JV\",\"referenceNo\":\"1005\",\"VDate\":\"2024-06-21\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:29:08\"}', '2024-06-21 23:29:08', 1),
(0, '1', 'approved_vaucher_transation', 'create', '73', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"43\",\"fyear\":\"1\",\"VNo\":\"JV-18\",\"Vtype\":\"JV\",\"referenceNo\":\"1005\",\"VDate\":\"2024-06-21\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:29:08\"}', '2024-06-21 23:29:08', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '74', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"43\",\"fyear\":\"1\",\"VNo\":\"JV-18\",\"Vtype\":\"JV\",\"referenceNo\":\"1005\",\"VDate\":\"2024-06-21\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:29:08\"}', '2024-06-21 23:29:08', 1),
(0, '1', 'approved_vaucher_transation', 'create', '75', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"44\",\"fyear\":\"1\",\"VNo\":\"CV-10\",\"Vtype\":\"CV\",\"referenceNo\":\"1006\",\"VDate\":\"2024-06-21\",\"COAID\":\"1020103\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"600.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:31:16\"}', '2024-06-21 23:31:16', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '76', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"44\",\"fyear\":\"1\",\"VNo\":\"CV-10\",\"Vtype\":\"CV\",\"referenceNo\":\"1006\",\"VDate\":\"2024-06-21\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"600.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:31:16\"}', '2024-06-21 23:31:16', 1),
(0, '1', 'approved_vaucher_transation', 'create', '77', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"45\",\"fyear\":\"1\",\"VNo\":\"JV-19\",\"Vtype\":\"JV\",\"referenceNo\":\"1006\",\"VDate\":\"2024-06-21\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:31:16\"}', '2024-06-21 23:31:16', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '78', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"45\",\"fyear\":\"1\",\"VNo\":\"JV-19\",\"Vtype\":\"JV\",\"referenceNo\":\"1006\",\"VDate\":\"2024-06-21\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:31:16\"}', '2024-06-21 23:31:16', 1),
(0, '1', 'approved_vaucher_transation', 'create', '79', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"46\",\"fyear\":\"1\",\"VNo\":\"JV-20\",\"Vtype\":\"JV\",\"referenceNo\":\"1006\",\"VDate\":\"2024-06-21\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:31:17\"}', '2024-06-21 23:31:17', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '80', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"46\",\"fyear\":\"1\",\"VNo\":\"JV-20\",\"Vtype\":\"JV\",\"referenceNo\":\"1006\",\"VDate\":\"2024-06-21\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:31:17\"}', '2024-06-21 23:31:17', 1),
(0, '1', 'contra_vaucher', 'create', 'CT-2', 'acc_vaucher', 'account/accounts/store_contra_voucher', '{\"fyear\":\"1\",\"VNo\":\"CT-2\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-06-22\",\"COAID\":\"1020501\",\"Narration\":\"\",\"chequeNo\":null,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":\"1000\",\"Credit\":\"\",\"RevCodde\":\"1020101\",\"subType\":1,\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-22 16:20:16\",\"status\":0}', '2024-06-22 21:50:16', 1),
(0, '1', 'contra_vaucher', 'create', 'CT-3', 'acc_vaucher', 'account/accounts/store_contra_voucher', '{\"fyear\":\"1\",\"VNo\":\"CT-3\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-06-23\",\"COAID\":\"1020502\",\"Narration\":\"\",\"chequeNo\":null,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":\"600\",\"Credit\":0,\"RevCodde\":\"1020103\",\"subType\":1,\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 07:09:50\",\"status\":0}', '2024-06-23 12:39:50', 1),
(0, '1', 'approved_vaucher_transation', 'create', '81', 'acc_transaction', 'account/accounts/isactive/CT-3/active', '{\"vid\":\"48\",\"fyear\":\"1\",\"VNo\":\"CT-3\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-06-23\",\"COAID\":\"1020502\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"600.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 07:10:05\"}', '2024-06-23 12:40:05', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '82', 'acc_transaction', 'account/accounts/isactive/CT-3/active', '{\"vid\":\"48\",\"fyear\":\"1\",\"VNo\":\"CT-3\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-06-23\",\"COAID\":\"1020103\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"0.00\",\"Credit\":\"600.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020502\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 07:10:05\"}', '2024-06-23 12:40:05', 1),
(0, '1', 'approved_vaucher_transation', 'create', '83', 'acc_transaction', 'account/accounts/isactive/CT-2/active', '{\"vid\":\"47\",\"fyear\":\"1\",\"VNo\":\"CT-2\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-06-22\",\"COAID\":\"1020501\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"1000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 07:10:08\"}', '2024-06-23 12:40:08', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '84', 'acc_transaction', 'account/accounts/isactive/CT-2/active', '{\"vid\":\"47\",\"fyear\":\"1\",\"VNo\":\"CT-2\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-06-22\",\"COAID\":\"1020101\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"0.00\",\"Credit\":\"1000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020501\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 07:10:08\"}', '2024-06-23 12:40:08', 1),
(0, '1', 'approved_vaucher_transation', 'create', '85', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"49\",\"fyear\":\"1\",\"VNo\":\"CV-11\",\"Vtype\":\"CV\",\"referenceNo\":\"1007\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020101\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"100.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:27:40\"}', '2024-06-23 22:27:40', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '86', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"49\",\"fyear\":\"1\",\"VNo\":\"CV-11\",\"Vtype\":\"CV\",\"referenceNo\":\"1007\",\"VDate\":\"2024-06-23\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"100.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:27:40\"}', '2024-06-23 22:27:40', 1),
(0, '1', 'approved_vaucher_transation', 'create', '87', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"50\",\"fyear\":\"1\",\"VNo\":\"CV-12\",\"Vtype\":\"CV\",\"referenceNo\":\"1007\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020103\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"500.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:27:40\"}', '2024-06-23 22:27:40', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '88', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"50\",\"fyear\":\"1\",\"VNo\":\"CV-12\",\"Vtype\":\"CV\",\"referenceNo\":\"1007\",\"VDate\":\"2024-06-23\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"500.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:27:40\"}', '2024-06-23 22:27:40', 1),
(0, '1', 'approved_vaucher_transation', 'create', '89', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"51\",\"fyear\":\"1\",\"VNo\":\"JV-21\",\"Vtype\":\"JV\",\"referenceNo\":\"1007\",\"VDate\":\"2024-06-23\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:27:40\"}', '2024-06-23 22:27:40', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '90', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"51\",\"fyear\":\"1\",\"VNo\":\"JV-21\",\"Vtype\":\"JV\",\"referenceNo\":\"1007\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:27:40\"}', '2024-06-23 22:27:40', 1),
(0, '1', 'approved_vaucher_transation', 'create', '91', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"52\",\"fyear\":\"1\",\"VNo\":\"JV-22\",\"Vtype\":\"JV\",\"referenceNo\":\"1007\",\"VDate\":\"2024-06-23\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:27:40\"}', '2024-06-23 22:27:40', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '92', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"52\",\"fyear\":\"1\",\"VNo\":\"JV-22\",\"Vtype\":\"JV\",\"referenceNo\":\"1007\",\"VDate\":\"2024-06-23\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:27:40\"}', '2024-06-23 22:27:40', 1),
(0, '1', 'approved_vaucher_transation', 'create', '93', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"53\",\"fyear\":\"1\",\"VNo\":\"DV-7\",\"Vtype\":\"DV\",\"referenceNo\":\"5\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"500.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:34:04\"}', '2024-06-23 22:34:04', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '94', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"53\",\"fyear\":\"1\",\"VNo\":\"DV-7\",\"Vtype\":\"DV\",\"referenceNo\":\"5\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020103\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"500.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:34:04\"}', '2024-06-23 22:34:04', 1),
(0, '1', 'approved_vaucher_transation', 'create', '95', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"54\",\"fyear\":\"1\",\"VNo\":\"DV-8\",\"Vtype\":\"DV\",\"referenceNo\":\"5\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"4000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020501\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:34:04\"}', '2024-06-23 22:34:04', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '96', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"54\",\"fyear\":\"1\",\"VNo\":\"DV-8\",\"Vtype\":\"DV\",\"referenceNo\":\"5\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020501\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"4000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:34:04\"}', '2024-06-23 22:34:04', 1),
(0, '1', 'approved_vaucher_transation', 'create', '97', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"55\",\"fyear\":\"1\",\"VNo\":\"CV-13\",\"Vtype\":\"CV\",\"referenceNo\":\"1008\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020103\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"700.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:42:46\"}', '2024-06-23 22:42:46', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '98', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"55\",\"fyear\":\"1\",\"VNo\":\"CV-13\",\"Vtype\":\"CV\",\"referenceNo\":\"1008\",\"VDate\":\"2024-06-23\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"700.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:42:46\"}', '2024-06-23 22:42:46', 1),
(0, '1', 'approved_vaucher_transation', 'create', '99', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"56\",\"fyear\":\"1\",\"VNo\":\"JV-23\",\"Vtype\":\"JV\",\"referenceNo\":\"1008\",\"VDate\":\"2024-06-23\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:42:46\"}', '2024-06-23 22:42:46', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '100', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"56\",\"fyear\":\"1\",\"VNo\":\"JV-23\",\"Vtype\":\"JV\",\"referenceNo\":\"1008\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:42:46\"}', '2024-06-23 22:42:46', 1),
(0, '1', 'approved_vaucher_transation', 'create', '101', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"57\",\"fyear\":\"1\",\"VNo\":\"JV-24\",\"Vtype\":\"JV\",\"referenceNo\":\"1008\",\"VDate\":\"2024-06-23\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:42:46\"}', '2024-06-23 22:42:46', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '102', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"57\",\"fyear\":\"1\",\"VNo\":\"JV-24\",\"Vtype\":\"JV\",\"referenceNo\":\"1008\",\"VDate\":\"2024-06-23\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:42:46\"}', '2024-06-23 22:42:46', 1),
(0, '1', 'approved_vaucher_transation', 'create', '103', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"58\",\"fyear\":\"1\",\"VNo\":\"CV-14\",\"Vtype\":\"CV\",\"referenceNo\":\"1009\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020103\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"800.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:46:12\"}', '2024-06-23 22:46:12', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '104', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"58\",\"fyear\":\"1\",\"VNo\":\"CV-14\",\"Vtype\":\"CV\",\"referenceNo\":\"1009\",\"VDate\":\"2024-06-23\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"800.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:46:12\"}', '2024-06-23 22:46:12', 1),
(0, '1', 'approved_vaucher_transation', 'create', '105', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"59\",\"fyear\":\"1\",\"VNo\":\"JV-25\",\"Vtype\":\"JV\",\"referenceNo\":\"1009\",\"VDate\":\"2024-06-23\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:46:12\"}', '2024-06-23 22:46:12', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '106', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"59\",\"fyear\":\"1\",\"VNo\":\"JV-25\",\"Vtype\":\"JV\",\"referenceNo\":\"1009\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:46:12\"}', '2024-06-23 22:46:12', 1),
(0, '1', 'approved_vaucher_transation', 'create', '107', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"60\",\"fyear\":\"1\",\"VNo\":\"JV-26\",\"Vtype\":\"JV\",\"referenceNo\":\"1009\",\"VDate\":\"2024-06-23\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:46:12\"}', '2024-06-23 22:46:12', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '108', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"60\",\"fyear\":\"1\",\"VNo\":\"JV-26\",\"Vtype\":\"JV\",\"referenceNo\":\"1009\",\"VDate\":\"2024-06-23\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:46:12\"}', '2024-06-23 22:46:12', 1),
(0, '1', 'approved_vaucher_transation', 'create', '109', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"61\",\"fyear\":\"1\",\"VNo\":\"CV-15\",\"Vtype\":\"CV\",\"referenceNo\":\"1010\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020103\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"800.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:49:02\"}', '2024-06-23 22:49:02', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '110', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"61\",\"fyear\":\"1\",\"VNo\":\"CV-15\",\"Vtype\":\"CV\",\"referenceNo\":\"1010\",\"VDate\":\"2024-06-23\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"800.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:49:02\"}', '2024-06-23 22:49:02', 1),
(0, '1', 'approved_vaucher_transation', 'create', '111', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"62\",\"fyear\":\"1\",\"VNo\":\"JV-27\",\"Vtype\":\"JV\",\"referenceNo\":\"1010\",\"VDate\":\"2024-06-23\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:49:02\"}', '2024-06-23 22:49:02', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '112', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"62\",\"fyear\":\"1\",\"VNo\":\"JV-27\",\"Vtype\":\"JV\",\"referenceNo\":\"1010\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:49:02\"}', '2024-06-23 22:49:02', 1),
(0, '1', 'approved_vaucher_transation', 'create', '113', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"63\",\"fyear\":\"1\",\"VNo\":\"JV-28\",\"Vtype\":\"JV\",\"referenceNo\":\"1010\",\"VDate\":\"2024-06-23\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:49:02\"}', '2024-06-23 22:49:02', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '114', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"63\",\"fyear\":\"1\",\"VNo\":\"JV-28\",\"Vtype\":\"JV\",\"referenceNo\":\"1010\",\"VDate\":\"2024-06-23\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:49:02\"}', '2024-06-23 22:49:02', 1),
(0, '1', 'approved_vaucher_transation', 'create', '115', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"64\",\"fyear\":\"1\",\"VNo\":\"CV-16\",\"Vtype\":\"CV\",\"referenceNo\":\"1011\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020101\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"160.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:34:16\"}', '2024-06-23 23:34:16', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '116', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"64\",\"fyear\":\"1\",\"VNo\":\"CV-16\",\"Vtype\":\"CV\",\"referenceNo\":\"1011\",\"VDate\":\"2024-06-23\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"160.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:34:16\"}', '2024-06-23 23:34:16', 1),
(0, '1', 'approved_vaucher_transation', 'create', '117', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"65\",\"fyear\":\"1\",\"VNo\":\"JV-29\",\"Vtype\":\"JV\",\"referenceNo\":\"1011\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020801\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"1440.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"3\",\"subCode\":\"7\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:34:16\"}', '2024-06-23 23:34:16', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '118', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"65\",\"fyear\":\"1\",\"VNo\":\"JV-29\",\"Vtype\":\"JV\",\"referenceNo\":\"1011\",\"VDate\":\"2024-06-23\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"1440.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020801\",\"subType\":\"3\",\"subCode\":\"7\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:34:16\"}', '2024-06-23 23:34:16', 1),
(0, '1', 'approved_vaucher_transation', 'create', '119', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"66\",\"fyear\":\"1\",\"VNo\":\"JV-30\",\"Vtype\":\"JV\",\"referenceNo\":\"1011\",\"VDate\":\"2024-06-23\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"900.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:34:16\"}', '2024-06-23 23:34:16', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '120', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"66\",\"fyear\":\"1\",\"VNo\":\"JV-30\",\"Vtype\":\"JV\",\"referenceNo\":\"1011\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"900.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:34:16\"}', '2024-06-23 23:34:16', 1),
(0, '1', 'approved_vaucher_transation', 'create', '121', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"67\",\"fyear\":\"1\",\"VNo\":\"JV-31\",\"Vtype\":\"JV\",\"referenceNo\":\"1011\",\"VDate\":\"2024-06-23\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:34:16\"}', '2024-06-23 23:34:16', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '122', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"67\",\"fyear\":\"1\",\"VNo\":\"JV-31\",\"Vtype\":\"JV\",\"referenceNo\":\"1011\",\"VDate\":\"2024-06-23\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:34:16\"}', '2024-06-23 23:34:16', 1),
(0, '1', 'approved_vaucher_transation', 'create', '123', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"68\",\"fyear\":\"1\",\"VNo\":\"CV-17\",\"Vtype\":\"CV\",\"referenceNo\":\"1012\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020103\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"400.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:36:15\"}', '2024-06-23 23:36:15', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '124', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"68\",\"fyear\":\"1\",\"VNo\":\"CV-17\",\"Vtype\":\"CV\",\"referenceNo\":\"1012\",\"VDate\":\"2024-06-23\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"400.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:36:15\"}', '2024-06-23 23:36:15', 1),
(0, '1', 'approved_vaucher_transation', 'create', '125', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"69\",\"fyear\":\"1\",\"VNo\":\"CV-18\",\"Vtype\":\"CV\",\"referenceNo\":\"1012\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020101\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"100.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:36:15\"}', '2024-06-23 23:36:15', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '126', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"69\",\"fyear\":\"1\",\"VNo\":\"CV-18\",\"Vtype\":\"CV\",\"referenceNo\":\"1012\",\"VDate\":\"2024-06-23\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"100.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:36:15\"}', '2024-06-23 23:36:15', 1),
(0, '1', 'approved_vaucher_transation', 'create', '127', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"70\",\"fyear\":\"1\",\"VNo\":\"JV-32\",\"Vtype\":\"JV\",\"referenceNo\":\"1012\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020801\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"1100.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"3\",\"subCode\":\"7\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:36:15\"}', '2024-06-23 23:36:15', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '128', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"70\",\"fyear\":\"1\",\"VNo\":\"JV-32\",\"Vtype\":\"JV\",\"referenceNo\":\"1012\",\"VDate\":\"2024-06-23\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"1100.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020801\",\"subType\":\"3\",\"subCode\":\"7\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:36:15\"}', '2024-06-23 23:36:15', 1),
(0, '1', 'approved_vaucher_transation', 'create', '129', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"71\",\"fyear\":\"1\",\"VNo\":\"JV-33\",\"Vtype\":\"JV\",\"referenceNo\":\"1012\",\"VDate\":\"2024-06-23\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"900.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:36:15\"}', '2024-06-23 23:36:15', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '130', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"71\",\"fyear\":\"1\",\"VNo\":\"JV-33\",\"Vtype\":\"JV\",\"referenceNo\":\"1012\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"900.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:36:15\"}', '2024-06-23 23:36:15', 1),
(0, '1', 'approved_vaucher_transation', 'create', '131', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"72\",\"fyear\":\"1\",\"VNo\":\"JV-34\",\"Vtype\":\"JV\",\"referenceNo\":\"1012\",\"VDate\":\"2024-06-23\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:36:15\"}', '2024-06-23 23:36:15', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '132', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"72\",\"fyear\":\"1\",\"VNo\":\"JV-34\",\"Vtype\":\"JV\",\"referenceNo\":\"1012\",\"VDate\":\"2024-06-23\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:36:15\"}', '2024-06-23 23:36:15', 1),
(0, '1', 'approved_vaucher_transation', 'create', '133', 'acc_transaction', 'account/accounts/create_customer_receive', '{\"vid\":\"73\",\"fyear\":\"1\",\"VNo\":\"CV-19\",\"Vtype\":\"CV\",\"referenceNo\":\"1012\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020801\",\"Narration\":\"Sales Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Due Voucher for Customer\",\"Debit\":\"0.00\",\"Credit\":\"100.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"3\",\"subCode\":\"7\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:45:08\"}', '2024-06-23 23:45:08', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '134', 'acc_transaction', 'account/accounts/create_customer_receive', '{\"vid\":\"73\",\"fyear\":\"1\",\"VNo\":\"CV-19\",\"Vtype\":\"CV\",\"referenceNo\":\"1012\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020103\",\"Narration\":\"Sales Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Due Voucher for Customer\",\"Debit\":\"100.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020801\",\"subType\":\"3\",\"subCode\":\"7\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:45:08\"}', '2024-06-23 23:45:08', 1),
(0, '1', 'approved_vaucher_transation', 'create', '135', 'acc_transaction', 'account/accounts/create_customer_receive', '{\"vid\":\"74\",\"fyear\":\"1\",\"VNo\":\"CV-20\",\"Vtype\":\"CV\",\"referenceNo\":\"1012\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020801\",\"Narration\":\"Sales Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Due Voucher for Customer\",\"Debit\":\"0.00\",\"Credit\":\"300.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"3\",\"subCode\":\"7\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:47:31\"}', '2024-06-23 23:47:31', 1);
INSERT INTO `activity_logs` (`activity_id`, `user_id`, `type`, `action`, `action_id`, `table_name`, `slug`, `form_data`, `create_date`, `status`) VALUES
(0, '1', 'approved_vaucher_reversetransa', 'create', '136', 'acc_transaction', 'account/accounts/create_customer_receive', '{\"vid\":\"74\",\"fyear\":\"1\",\"VNo\":\"CV-20\",\"Vtype\":\"CV\",\"referenceNo\":\"1012\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020103\",\"Narration\":\"Sales Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Due Voucher for Customer\",\"Debit\":\"300.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020801\",\"subType\":\"3\",\"subCode\":\"7\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:47:31\"}', '2024-06-23 23:47:31', 1),
(0, '1', 'approved_vaucher_transation', 'create', '137', 'acc_transaction', 'account/accounts/create_customer_receive', '{\"vid\":\"75\",\"fyear\":\"1\",\"VNo\":\"CV-21\",\"Vtype\":\"CV\",\"referenceNo\":\"1012\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020801\",\"Narration\":\"Sales Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Due Voucher for Customer\",\"Debit\":\"0.00\",\"Credit\":\"700.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"3\",\"subCode\":\"7\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:47:31\"}', '2024-06-23 23:47:31', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '138', 'acc_transaction', 'account/accounts/create_customer_receive', '{\"vid\":\"75\",\"fyear\":\"1\",\"VNo\":\"CV-21\",\"Vtype\":\"CV\",\"referenceNo\":\"1012\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020103\",\"Narration\":\"Sales Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Due Voucher for Customer\",\"Debit\":\"700.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020801\",\"subType\":\"3\",\"subCode\":\"7\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:47:31\"}', '2024-06-23 23:47:31', 1),
(0, '1', 'approved_vaucher_transation', 'create', '139', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"76\",\"fyear\":\"1\",\"VNo\":\"DV-9\",\"Vtype\":\"DV\",\"referenceNo\":\"6\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"100.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 00:08:06\"}', '2024-06-24 00:08:06', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '140', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"76\",\"fyear\":\"1\",\"VNo\":\"DV-9\",\"Vtype\":\"DV\",\"referenceNo\":\"6\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020103\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"100.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 00:08:06\"}', '2024-06-24 00:08:06', 1),
(0, '1', 'approved_vaucher_transation', 'create', '141', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"77\",\"fyear\":\"1\",\"VNo\":\"DV-10\",\"Vtype\":\"DV\",\"referenceNo\":\"6\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"250.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 00:08:06\"}', '2024-06-24 00:08:06', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '142', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"77\",\"fyear\":\"1\",\"VNo\":\"DV-10\",\"Vtype\":\"DV\",\"referenceNo\":\"6\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020101\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"250.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 00:08:06\"}', '2024-06-24 00:08:06', 1),
(0, '1', 'approved_vaucher_transation', 'create', '143', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"78\",\"fyear\":\"1\",\"VNo\":\"JV-35\",\"Vtype\":\"JV\",\"referenceNo\":\"6\",\"VDate\":\"2024-06-24\",\"COAID\":\"5020201\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"1000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"4\",\"subCode\":\"1\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 00:08:06\"}', '2024-06-24 00:08:06', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '144', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"78\",\"fyear\":\"1\",\"VNo\":\"JV-35\",\"Vtype\":\"JV\",\"referenceNo\":\"6\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"1000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020201\",\"subType\":\"4\",\"subCode\":\"1\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 00:08:06\"}', '2024-06-24 00:08:06', 1),
(0, '1', 'approved_vaucher_transation', 'create', '145', 'acc_transaction', 'account/accounts/create_supplier_payment', '{\"vid\":\"79\",\"fyear\":\"1\",\"VNo\":\"DV-11\",\"Vtype\":\"DV\",\"referenceNo\":\"6\",\"VDate\":\"2024-06-24\",\"COAID\":\"5020201\",\"Narration\":\"Purchase Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Due Voucher for supplier\",\"Debit\":\"800.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"4\",\"subCode\":\"8\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 00:16:48\"}', '2024-06-24 00:16:48', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '146', 'acc_transaction', 'account/accounts/create_supplier_payment', '{\"vid\":\"79\",\"fyear\":\"1\",\"VNo\":\"DV-11\",\"Vtype\":\"DV\",\"referenceNo\":\"6\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020103\",\"Narration\":\"Purchase Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Due Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"800.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020201\",\"subType\":\"4\",\"subCode\":\"8\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 00:16:48\"}', '2024-06-24 00:16:48', 1),
(0, '1', 'approved_vaucher_transation', 'create', '147', 'acc_transaction', 'account/accounts/create_supplier_payment', '{\"vid\":\"80\",\"fyear\":\"1\",\"VNo\":\"DV-12\",\"Vtype\":\"DV\",\"referenceNo\":\"6\",\"VDate\":\"2024-06-24\",\"COAID\":\"5020201\",\"Narration\":\"Purchase Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Due Voucher for supplier\",\"Debit\":\"200.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020501\",\"subType\":\"4\",\"subCode\":\"8\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 00:16:48\"}', '2024-06-24 00:16:48', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '148', 'acc_transaction', 'account/accounts/create_supplier_payment', '{\"vid\":\"80\",\"fyear\":\"1\",\"VNo\":\"DV-12\",\"Vtype\":\"DV\",\"referenceNo\":\"6\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020501\",\"Narration\":\"Purchase Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Due Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"200.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020201\",\"subType\":\"4\",\"subCode\":\"8\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 00:16:48\"}', '2024-06-24 00:16:48', 1),
(0, '1', 'contra_vaucher', 'create', 'CT-4', 'acc_vaucher', 'account/accounts/store_contra_voucher', '{\"fyear\":\"1\",\"VNo\":\"CT-4\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-06-18\",\"COAID\":\"1020501\",\"Narration\":\"\",\"chequeNo\":null,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":\"400\",\"Credit\":0,\"RevCodde\":\"1020103\",\"subType\":1,\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 18:53:46\",\"status\":0}', '2024-06-24 00:23:46', 1),
(0, '1', 'approved_vaucher_transation', 'create', '149', 'acc_transaction', 'account/accounts/isactive/CT-4/active', '{\"vid\":\"81\",\"fyear\":\"1\",\"VNo\":\"CT-4\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-06-18\",\"COAID\":\"1020501\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"400.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 18:54:17\"}', '2024-06-24 00:24:17', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '150', 'acc_transaction', 'account/accounts/isactive/CT-4/active', '{\"vid\":\"81\",\"fyear\":\"1\",\"VNo\":\"CT-4\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-06-18\",\"COAID\":\"1020103\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"0.00\",\"Credit\":\"400.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020501\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 18:54:17\"}', '2024-06-24 00:24:17', 1),
(0, '1', 'approved_vaucher_transation', 'create', '151', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"82\",\"fyear\":\"1\",\"VNo\":\"DV-13\",\"Vtype\":\"DV\",\"referenceNo\":\"7\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"600.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:24:56\"}', '2024-06-24 15:24:56', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '152', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"82\",\"fyear\":\"1\",\"VNo\":\"DV-13\",\"Vtype\":\"DV\",\"referenceNo\":\"7\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020103\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"600.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:24:56\"}', '2024-06-24 15:24:56', 1),
(0, '1', 'approved_vaucher_transation', 'create', '153', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"83\",\"fyear\":\"1\",\"VNo\":\"DV-14\",\"Vtype\":\"DV\",\"referenceNo\":\"7\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"3000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:24:56\"}', '2024-06-24 15:24:56', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '154', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"83\",\"fyear\":\"1\",\"VNo\":\"DV-14\",\"Vtype\":\"DV\",\"referenceNo\":\"7\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020101\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"3000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:24:56\"}', '2024-06-24 15:24:56', 1),
(0, '1', 'approved_vaucher_transation', 'create', '155', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"84\",\"fyear\":\"1\",\"VNo\":\"DV-15\",\"Vtype\":\"DV\",\"referenceNo\":\"7\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"400.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020501\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:24:56\"}', '2024-06-24 15:24:56', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '156', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"84\",\"fyear\":\"1\",\"VNo\":\"DV-15\",\"Vtype\":\"DV\",\"referenceNo\":\"7\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020501\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"400.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:24:56\"}', '2024-06-24 15:24:56', 1),
(0, '1', 'approved_vaucher_transation', 'create', '157', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"85\",\"fyear\":\"1\",\"VNo\":\"JV-36\",\"Vtype\":\"JV\",\"referenceNo\":\"7\",\"VDate\":\"2024-06-24\",\"COAID\":\"5020201\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"500.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"4\",\"subCode\":\"1\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:24:56\"}', '2024-06-24 15:24:56', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '158', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"85\",\"fyear\":\"1\",\"VNo\":\"JV-36\",\"Vtype\":\"JV\",\"referenceNo\":\"7\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"500.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020201\",\"subType\":\"4\",\"subCode\":\"1\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:24:56\"}', '2024-06-24 15:24:57', 1),
(0, '1', 'approved_vaucher_transation', 'create', '159', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"86\",\"fyear\":\"1\",\"VNo\":\"CV-22\",\"Vtype\":\"CV\",\"referenceNo\":\"1013\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020101\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"600.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:45:25\"}', '2024-06-24 15:45:25', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '160', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"86\",\"fyear\":\"1\",\"VNo\":\"CV-22\",\"Vtype\":\"CV\",\"referenceNo\":\"1013\",\"VDate\":\"2024-06-24\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"600.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:45:25\"}', '2024-06-24 15:45:25', 1),
(0, '1', 'approved_vaucher_transation', 'create', '161', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"87\",\"fyear\":\"1\",\"VNo\":\"JV-37\",\"Vtype\":\"JV\",\"referenceNo\":\"1013\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020801\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"200.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"3\",\"subCode\":\"4\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:45:25\"}', '2024-06-24 15:45:25', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '162', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"87\",\"fyear\":\"1\",\"VNo\":\"JV-37\",\"Vtype\":\"JV\",\"referenceNo\":\"1013\",\"VDate\":\"2024-06-24\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"200.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020801\",\"subType\":\"3\",\"subCode\":\"4\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:45:25\"}', '2024-06-24 15:45:25', 1),
(0, '1', 'approved_vaucher_transation', 'create', '163', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"88\",\"fyear\":\"1\",\"VNo\":\"JV-38\",\"Vtype\":\"JV\",\"referenceNo\":\"1013\",\"VDate\":\"2024-06-24\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:45:25\"}', '2024-06-24 15:45:25', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '164', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"88\",\"fyear\":\"1\",\"VNo\":\"JV-38\",\"Vtype\":\"JV\",\"referenceNo\":\"1013\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:45:25\"}', '2024-06-24 15:45:25', 1),
(0, '1', 'approved_vaucher_transation', 'create', '165', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"89\",\"fyear\":\"1\",\"VNo\":\"JV-39\",\"Vtype\":\"JV\",\"referenceNo\":\"1013\",\"VDate\":\"2024-06-24\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:45:25\"}', '2024-06-24 15:45:25', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '166', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"89\",\"fyear\":\"1\",\"VNo\":\"JV-39\",\"Vtype\":\"JV\",\"referenceNo\":\"1013\",\"VDate\":\"2024-06-24\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:45:25\"}', '2024-06-24 15:45:25', 1),
(0, '1', 'approved_vaucher_transation', 'create', '167', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"90\",\"fyear\":\"1\",\"VNo\":\"CV-23\",\"Vtype\":\"CV\",\"referenceNo\":\"1014\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020101\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"600.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:45:41\"}', '2024-06-24 15:45:41', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '168', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"90\",\"fyear\":\"1\",\"VNo\":\"CV-23\",\"Vtype\":\"CV\",\"referenceNo\":\"1014\",\"VDate\":\"2024-06-24\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"600.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:45:41\"}', '2024-06-24 15:45:41', 1),
(0, '1', 'approved_vaucher_transation', 'create', '169', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"91\",\"fyear\":\"1\",\"VNo\":\"JV-40\",\"Vtype\":\"JV\",\"referenceNo\":\"1014\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020801\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"200.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"3\",\"subCode\":\"4\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:45:41\"}', '2024-06-24 15:45:41', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '170', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"91\",\"fyear\":\"1\",\"VNo\":\"JV-40\",\"Vtype\":\"JV\",\"referenceNo\":\"1014\",\"VDate\":\"2024-06-24\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"200.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020801\",\"subType\":\"3\",\"subCode\":\"4\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:45:41\"}', '2024-06-24 15:45:41', 1),
(0, '1', 'approved_vaucher_transation', 'create', '171', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"92\",\"fyear\":\"1\",\"VNo\":\"JV-41\",\"Vtype\":\"JV\",\"referenceNo\":\"1014\",\"VDate\":\"2024-06-24\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:45:41\"}', '2024-06-24 15:45:41', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '172', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"92\",\"fyear\":\"1\",\"VNo\":\"JV-41\",\"Vtype\":\"JV\",\"referenceNo\":\"1014\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:45:41\"}', '2024-06-24 15:45:41', 1),
(0, '1', 'approved_vaucher_transation', 'create', '173', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"93\",\"fyear\":\"1\",\"VNo\":\"JV-42\",\"Vtype\":\"JV\",\"referenceNo\":\"1014\",\"VDate\":\"2024-06-24\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:45:41\"}', '2024-06-24 15:45:41', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '174', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"93\",\"fyear\":\"1\",\"VNo\":\"JV-42\",\"Vtype\":\"JV\",\"referenceNo\":\"1014\",\"VDate\":\"2024-06-24\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:45:41\"}', '2024-06-24 15:45:41', 1),
(0, '1', 'approved_vaucher_transation', 'create', '175', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"94\",\"fyear\":\"1\",\"VNo\":\"CV-24\",\"Vtype\":\"CV\",\"referenceNo\":\"1015\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020101\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"800.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:46:02\"}', '2024-06-24 15:46:02', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '176', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"94\",\"fyear\":\"1\",\"VNo\":\"CV-24\",\"Vtype\":\"CV\",\"referenceNo\":\"1015\",\"VDate\":\"2024-06-24\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"800.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:46:02\"}', '2024-06-24 15:46:02', 1),
(0, '1', 'approved_vaucher_transation', 'create', '177', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"95\",\"fyear\":\"1\",\"VNo\":\"JV-43\",\"Vtype\":\"JV\",\"referenceNo\":\"1015\",\"VDate\":\"2024-06-24\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:46:02\"}', '2024-06-24 15:46:02', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '178', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"95\",\"fyear\":\"1\",\"VNo\":\"JV-43\",\"Vtype\":\"JV\",\"referenceNo\":\"1015\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:46:02\"}', '2024-06-24 15:46:02', 1),
(0, '1', 'approved_vaucher_transation', 'create', '179', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"96\",\"fyear\":\"1\",\"VNo\":\"JV-44\",\"Vtype\":\"JV\",\"referenceNo\":\"1015\",\"VDate\":\"2024-06-24\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:46:02\"}', '2024-06-24 15:46:02', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '180', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"96\",\"fyear\":\"1\",\"VNo\":\"JV-44\",\"Vtype\":\"JV\",\"referenceNo\":\"1015\",\"VDate\":\"2024-06-24\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:46:02\"}', '2024-06-24 15:46:02', 1),
(0, '1', 'approved_vaucher_transation', 'create', '181', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"97\",\"fyear\":\"1\",\"VNo\":\"JV-45\",\"Vtype\":\"JV\",\"referenceNo\":\"8\",\"VDate\":\"2024-06-24\",\"COAID\":\"5020201\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"4\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 20:20:05\"}', '2024-06-24 20:20:05', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '182', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"97\",\"fyear\":\"1\",\"VNo\":\"JV-45\",\"Vtype\":\"JV\",\"referenceNo\":\"8\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020201\",\"subType\":\"4\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 20:20:05\"}', '2024-06-24 20:20:05', 1),
(0, '1', 'approved_vaucher_transation', 'create', '183', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"98\",\"fyear\":\"1\",\"VNo\":\"CV-25\",\"Vtype\":\"CV\",\"referenceNo\":\"1016\",\"VDate\":\"2024-06-27\",\"COAID\":\"1020103\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"800.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:01:52\"}', '2024-06-27 21:01:52', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '184', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"98\",\"fyear\":\"1\",\"VNo\":\"CV-25\",\"Vtype\":\"CV\",\"referenceNo\":\"1016\",\"VDate\":\"2024-06-27\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"800.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:01:52\"}', '2024-06-27 21:01:52', 1),
(0, '1', 'approved_vaucher_transation', 'create', '185', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"99\",\"fyear\":\"1\",\"VNo\":\"JV-46\",\"Vtype\":\"JV\",\"referenceNo\":\"1016\",\"VDate\":\"2024-06-27\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:01:52\"}', '2024-06-27 21:01:52', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '186', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"99\",\"fyear\":\"1\",\"VNo\":\"JV-46\",\"Vtype\":\"JV\",\"referenceNo\":\"1016\",\"VDate\":\"2024-06-27\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:01:52\"}', '2024-06-27 21:01:53', 1),
(0, '1', 'approved_vaucher_transation', 'create', '187', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"100\",\"fyear\":\"1\",\"VNo\":\"JV-47\",\"Vtype\":\"JV\",\"referenceNo\":\"1016\",\"VDate\":\"2024-06-27\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:01:53\"}', '2024-06-27 21:01:53', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '188', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"100\",\"fyear\":\"1\",\"VNo\":\"JV-47\",\"Vtype\":\"JV\",\"referenceNo\":\"1016\",\"VDate\":\"2024-06-27\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:01:53\"}', '2024-06-27 21:01:53', 1),
(0, '1', 'approved_vaucher_transation', 'create', '189', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"101\",\"fyear\":\"1\",\"VNo\":\"CV-26\",\"Vtype\":\"CV\",\"referenceNo\":\"1017\",\"VDate\":\"2024-06-27\",\"COAID\":\"1020103\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"800.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:06:03\"}', '2024-06-27 21:06:03', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '190', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"101\",\"fyear\":\"1\",\"VNo\":\"CV-26\",\"Vtype\":\"CV\",\"referenceNo\":\"1017\",\"VDate\":\"2024-06-27\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"800.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:06:03\"}', '2024-06-27 21:06:03', 1),
(0, '1', 'approved_vaucher_transation', 'create', '191', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"102\",\"fyear\":\"1\",\"VNo\":\"JV-48\",\"Vtype\":\"JV\",\"referenceNo\":\"1017\",\"VDate\":\"2024-06-27\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:06:03\"}', '2024-06-27 21:06:03', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '192', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"102\",\"fyear\":\"1\",\"VNo\":\"JV-48\",\"Vtype\":\"JV\",\"referenceNo\":\"1017\",\"VDate\":\"2024-06-27\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:06:03\"}', '2024-06-27 21:06:03', 1),
(0, '1', 'approved_vaucher_transation', 'create', '193', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"103\",\"fyear\":\"1\",\"VNo\":\"JV-49\",\"Vtype\":\"JV\",\"referenceNo\":\"1017\",\"VDate\":\"2024-06-27\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:06:03\"}', '2024-06-27 21:06:03', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '194', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"103\",\"fyear\":\"1\",\"VNo\":\"JV-49\",\"Vtype\":\"JV\",\"referenceNo\":\"1017\",\"VDate\":\"2024-06-27\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:06:03\"}', '2024-06-27 21:06:03', 1),
(0, '1', 'approved_vaucher_transation', 'create', '195', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"104\",\"fyear\":\"1\",\"VNo\":\"CV-27\",\"Vtype\":\"CV\",\"referenceNo\":\"1018\",\"VDate\":\"2024-06-27\",\"COAID\":\"1020103\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"800.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:25:55\"}', '2024-06-27 21:25:55', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '196', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"104\",\"fyear\":\"1\",\"VNo\":\"CV-27\",\"Vtype\":\"CV\",\"referenceNo\":\"1018\",\"VDate\":\"2024-06-27\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"800.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:25:55\"}', '2024-06-27 21:25:55', 1),
(0, '1', 'approved_vaucher_transation', 'create', '197', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"105\",\"fyear\":\"1\",\"VNo\":\"JV-50\",\"Vtype\":\"JV\",\"referenceNo\":\"1018\",\"VDate\":\"2024-06-27\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:25:55\"}', '2024-06-27 21:25:55', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '198', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"105\",\"fyear\":\"1\",\"VNo\":\"JV-50\",\"Vtype\":\"JV\",\"referenceNo\":\"1018\",\"VDate\":\"2024-06-27\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:25:55\"}', '2024-06-27 21:25:55', 1),
(0, '1', 'approved_vaucher_transation', 'create', '199', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"106\",\"fyear\":\"1\",\"VNo\":\"JV-51\",\"Vtype\":\"JV\",\"referenceNo\":\"1018\",\"VDate\":\"2024-06-27\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:25:55\"}', '2024-06-27 21:25:55', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '200', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"106\",\"fyear\":\"1\",\"VNo\":\"JV-51\",\"Vtype\":\"JV\",\"referenceNo\":\"1018\",\"VDate\":\"2024-06-27\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:25:55\"}', '2024-06-27 21:25:55', 1),
(0, '1', 'approved_vaucher_transation', 'create', '201', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"107\",\"fyear\":\"1\",\"VNo\":\"JV-52\",\"Vtype\":\"JV\",\"referenceNo\":\"1019\",\"VDate\":\"2024-06-27\",\"COAID\":\"1020801\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"1600.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"3\",\"subCode\":\"4\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:27:07\"}', '2024-06-27 21:27:07', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '202', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"107\",\"fyear\":\"1\",\"VNo\":\"JV-52\",\"Vtype\":\"JV\",\"referenceNo\":\"1019\",\"VDate\":\"2024-06-27\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"1600.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020801\",\"subType\":\"3\",\"subCode\":\"4\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:27:07\"}', '2024-06-27 21:27:07', 1),
(0, '1', 'approved_vaucher_transation', 'create', '203', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"108\",\"fyear\":\"1\",\"VNo\":\"JV-53\",\"Vtype\":\"JV\",\"referenceNo\":\"1019\",\"VDate\":\"2024-06-27\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"900.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:27:07\"}', '2024-06-27 21:27:07', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '204', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"108\",\"fyear\":\"1\",\"VNo\":\"JV-53\",\"Vtype\":\"JV\",\"referenceNo\":\"1019\",\"VDate\":\"2024-06-27\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"900.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:27:07\"}', '2024-06-27 21:27:07', 1),
(0, '1', 'approved_vaucher_transation', 'create', '205', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"109\",\"fyear\":\"1\",\"VNo\":\"JV-54\",\"Vtype\":\"JV\",\"referenceNo\":\"1019\",\"VDate\":\"2024-06-27\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:27:07\"}', '2024-06-27 21:27:07', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '206', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"109\",\"fyear\":\"1\",\"VNo\":\"JV-54\",\"Vtype\":\"JV\",\"referenceNo\":\"1019\",\"VDate\":\"2024-06-27\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:27:07\"}', '2024-06-27 21:27:07', 1),
(0, '1', 'approved_vaucher_transation', 'create', '207', 'acc_transaction', 'account/accounts/create_customer_receive', '{\"vid\":\"110\",\"fyear\":\"1\",\"VNo\":\"CV-28\",\"Vtype\":\"CV\",\"referenceNo\":\"1019\",\"VDate\":\"2024-06-27\",\"COAID\":\"1020801\",\"Narration\":\"Sales Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Due Voucher for Customer\",\"Debit\":\"0.00\",\"Credit\":\"1600.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"3\",\"subCode\":\"4\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:28:27\"}', '2024-06-27 21:28:27', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '208', 'acc_transaction', 'account/accounts/create_customer_receive', '{\"vid\":\"110\",\"fyear\":\"1\",\"VNo\":\"CV-28\",\"Vtype\":\"CV\",\"referenceNo\":\"1019\",\"VDate\":\"2024-06-27\",\"COAID\":\"1020103\",\"Narration\":\"Sales Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Due Voucher for Customer\",\"Debit\":\"1600.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020801\",\"subType\":\"3\",\"subCode\":\"4\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:28:27\"}', '2024-06-27 21:28:27', 1),
(0, '1', 'contra_vaucher', 'create', 'CT-5', 'acc_vaucher', 'account/accounts/store_contra_voucher', '{\"fyear\":\"1\",\"VNo\":\"CT-5\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-06-27\",\"COAID\":\"1020502\",\"Narration\":\"\",\"chequeNo\":null,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":\"300\",\"Credit\":0,\"RevCodde\":\"1020103\",\"subType\":1,\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 16:01:27\",\"status\":0}', '2024-06-27 21:31:27', 1),
(0, '1', 'approved_vaucher_transation', 'create', '209', 'acc_transaction', 'account/accounts/isactive/CT-5/active', '{\"vid\":\"111\",\"fyear\":\"1\",\"VNo\":\"CT-5\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-06-27\",\"COAID\":\"1020502\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"300.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 16:04:09\"}', '2024-06-27 21:34:09', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '210', 'acc_transaction', 'account/accounts/isactive/CT-5/active', '{\"vid\":\"111\",\"fyear\":\"1\",\"VNo\":\"CT-5\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-06-27\",\"COAID\":\"1020103\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"0.00\",\"Credit\":\"300.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020502\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 16:04:09\"}', '2024-06-27 21:34:09', 1),
(0, '1', 'sub_account', 'create', '9', 'acc_subcode', 'subaccount_list', '{\"subTypeId\":\"6\",\"name\":\"Agent 1\",\"referenceNo\":0,\"status\":1,\"created_by\":\"1\",\"created_date\":\"2024-07-03 06:38:34\"}', '2024-07-04 00:08:34', 1),
(0, '1', 'sub_account', 'create', '10', 'acc_subcode', 'subaccount_list', '{\"subTypeId\":\"6\",\"name\":\"Agent 2\",\"referenceNo\":0,\"status\":1,\"created_by\":\"1\",\"created_date\":\"2024-07-03 06:38:50\"}', '2024-07-04 00:08:50', 1);
INSERT INTO `activity_logs` (`activity_id`, `user_id`, `type`, `action`, `action_id`, `table_name`, `slug`, `form_data`, `create_date`, `status`) VALUES
(0, '1', 'coa_account', 'create', '242', 'acc_coa', 'account/accounts/insert_coa', '{\"HeadCode\":\"1020803\",\"Pheadcode\":\"10208\",\"HeadName\":\"Debtors\",\"PHeadName\":\"Accounts Receivable\",\"HeadLevel\":\"4\",\"IsActive\":\"1\",\"isStock\":0,\"isSubType\":\"1\",\"DepreciationRate\":0,\"HeadType\":\"A\",\"IsBudget\":0,\"isCashNature\":0,\"isBankNature\":0,\"isFixedAssetSch\":0,\"assetCode\":null,\"depCode\":null,\"subType\":\"6\",\"noteNo\":null,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-03 18:41:17\"}', '2024-07-04 00:11:17', 1),
(0, '1', 'coa_account', 'create', '243', 'acc_coa', 'account/accounts/insert_coa', '{\"HeadCode\":\"5020202\",\"Pheadcode\":\"50202\",\"HeadName\":\"Creditors\",\"PHeadName\":\"Accounts Payable\",\"HeadLevel\":\"4\",\"IsActive\":\"1\",\"isStock\":0,\"isSubType\":\"1\",\"DepreciationRate\":0,\"HeadType\":\"L\",\"IsBudget\":0,\"isCashNature\":0,\"isBankNature\":0,\"isFixedAssetSch\":0,\"assetCode\":null,\"depCode\":null,\"subType\":\"6\",\"noteNo\":null,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-03 18:44:53\"}', '2024-07-04 00:14:53', 1),
(0, '1', 'sub_account', 'create', '11', 'acc_subcode', 'subaccount_list', '{\"subTypeId\":\"6\",\"name\":\"Creditor 1\",\"referenceNo\":0,\"status\":1,\"created_by\":\"1\",\"created_date\":\"2024-07-03 06:45:44\"}', '2024-07-04 00:15:44', 1),
(0, '1', 'sub_account', 'create', '12', 'acc_subcode', 'subaccount_list', '{\"subTypeId\":\"6\",\"name\":\"Creditor 2\",\"referenceNo\":0,\"status\":1,\"created_by\":\"1\",\"created_date\":\"2024-07-03 06:45:56\"}', '2024-07-04 00:15:56', 1),
(0, '1', 'sub_account', 'create', '13', 'acc_subcode', 'subaccount_list', '{\"subTypeId\":\"1\",\"name\":\"Debtor 1\",\"referenceNo\":0,\"status\":1,\"created_by\":\"1\",\"created_date\":\"2024-07-03 07:30:10\"}', '2024-07-04 01:00:10', 1),
(0, '1', 'sub_account', 'create', '14', 'acc_subcode', 'subaccount_list', '{\"subTypeId\":\"1\",\"name\":\"Debtor 2\",\"referenceNo\":0,\"status\":1,\"created_by\":\"1\",\"created_date\":\"2024-07-03 07:30:26\"}', '2024-07-04 01:00:26', 1),
(0, '1', 'coa_account', 'update', '5020202', 'acc_coa', 'account/accounts/insert_coa', '{\"HeadCode\":\"5020202\",\"Pheadcode\":\"50202\",\"HeadName\":\"Creditors\",\"PHeadName\":\"Accounts Payable\",\"HeadLevel\":\"4\",\"IsActive\":\"1\",\"isStock\":0,\"isSubType\":\"1\",\"DepreciationRate\":0,\"HeadType\":\"L\",\"IsBudget\":0,\"isCashNature\":0,\"isBankNature\":0,\"isFixedAssetSch\":0,\"assetCode\":null,\"depCode\":null,\"subType\":\"1\",\"noteNo\":null,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-04 15:16:30\"}', '2024-07-04 20:46:30', 2),
(0, '1', 'coa_account', 'update', '5020202', 'acc_coa', 'account/accounts/insert_coa', '{\"HeadCode\":\"5020202\",\"Pheadcode\":\"50202\",\"HeadName\":\"Creditors\",\"PHeadName\":\"Accounts Payable\",\"HeadLevel\":\"4\",\"IsActive\":\"1\",\"isStock\":0,\"isSubType\":\"1\",\"DepreciationRate\":0,\"HeadType\":\"L\",\"IsBudget\":0,\"isCashNature\":0,\"isBankNature\":0,\"isFixedAssetSch\":0,\"assetCode\":null,\"depCode\":null,\"subType\":\"2\",\"noteNo\":null,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-04 15:17:35\"}', '2024-07-04 20:47:35', 2),
(0, '1', 'sub_account', 'delete', '13', 'acc_subcode', 'account/accounts/delete_sub_account/13', '{\"id\":\"13\",\"subTypeId\":\"1\",\"name\":\"Debtor 1\",\"deleteBy\":\"1\",\"deleteDate\":\"2024-07-04 03:17:59\"}', '2024-07-04 20:47:59', 3),
(0, '1', 'sub_account', 'delete', '14', 'acc_subcode', 'account/accounts/delete_sub_account/14', '{\"id\":\"14\",\"subTypeId\":\"1\",\"name\":\"Debtor 2\",\"deleteBy\":\"1\",\"deleteDate\":\"2024-07-04 03:18:07\"}', '2024-07-04 20:48:07', 3),
(0, '1', 'sub_account', 'create', '15', 'acc_subcode', 'subaccount_list', '{\"subTypeId\":\"2\",\"name\":\"Debtor X1\",\"referenceNo\":0,\"status\":1,\"created_by\":\"1\",\"created_date\":\"2024-07-04 03:18:28\"}', '2024-07-04 20:48:28', 1),
(0, '1', 'sub_account', 'create', '16', 'acc_subcode', 'subaccount_list', '{\"subTypeId\":\"2\",\"name\":\"Debtor X2\",\"referenceNo\":0,\"status\":1,\"created_by\":\"1\",\"created_date\":\"2024-07-04 03:18:37\"}', '2024-07-04 20:48:37', 1),
(0, '1', 'sub_account', 'update', '15', 'acc_subcode', 'account/accounts/update_sub_account', '{\"id\":\"15\",\"subTypeId\":\"2\",\"name\":\"Creditors X1\",\"updated_by\":\"1\",\"updated_date\":\"2024-07-04 03:19:25\"}', '2024-07-04 20:49:25', 2),
(0, '1', 'sub_account', 'delete', '11', 'acc_subcode', 'account/accounts/delete_sub_account/11', '{\"id\":\"11\",\"subTypeId\":\"6\",\"name\":\"Creditor 1\",\"deleteBy\":\"1\",\"deleteDate\":\"2024-07-05 04:57:31\"}', '2024-07-05 22:27:31', 3),
(0, '1', 'sub_account', 'delete', '12', 'acc_subcode', 'account/accounts/delete_sub_account/12', '{\"id\":\"12\",\"subTypeId\":\"6\",\"name\":\"Creditor 2\",\"deleteBy\":\"1\",\"deleteDate\":\"2024-07-05 04:57:52\"}', '2024-07-05 22:27:52', 3),
(0, '1', 'sub_account', 'delete', '16', 'acc_subcode', 'account/accounts/delete_sub_account/16', '{\"id\":\"16\",\"subTypeId\":\"2\",\"name\":\"Debtor X2\",\"deleteBy\":\"1\",\"deleteDate\":\"2024-07-05 04:57:58\"}', '2024-07-05 22:27:58', 3),
(0, '1', 'sub_account', 'delete', '15', 'acc_subcode', 'account/accounts/delete_sub_account/15', '{\"id\":\"15\",\"subTypeId\":\"2\",\"name\":\"Creditors X1\",\"deleteBy\":\"1\",\"deleteDate\":\"2024-07-05 04:58:02\"}', '2024-07-05 22:28:02', 3),
(0, '1', 'coa_account', 'update', '1020803', 'acc_coa', 'account/accounts/insert_coa', '{\"HeadCode\":\"1020803\",\"Pheadcode\":\"10208\",\"HeadName\":\"Debtors\",\"PHeadName\":\"Accounts Receivable\",\"HeadLevel\":\"4\",\"IsActive\":\"1\",\"isStock\":0,\"isSubType\":\"1\",\"DepreciationRate\":0,\"HeadType\":\"A\",\"IsBudget\":0,\"isCashNature\":0,\"isBankNature\":0,\"isFixedAssetSch\":0,\"assetCode\":null,\"depCode\":null,\"subType\":\"7\",\"noteNo\":null,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-05 16:58:44\"}', '2024-07-05 22:28:44', 2),
(0, '1', 'coa_account', 'update', '5020202', 'acc_coa', 'account/accounts/insert_coa', '{\"HeadCode\":\"5020202\",\"Pheadcode\":\"50202\",\"HeadName\":\"Creditors\",\"PHeadName\":\"Accounts Payable\",\"HeadLevel\":\"4\",\"IsActive\":\"1\",\"isStock\":0,\"isSubType\":\"1\",\"DepreciationRate\":0,\"HeadType\":\"L\",\"IsBudget\":0,\"isCashNature\":0,\"isBankNature\":0,\"isFixedAssetSch\":0,\"assetCode\":null,\"depCode\":null,\"subType\":\"8\",\"noteNo\":null,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-05 16:59:16\"}', '2024-07-05 22:29:16', 2),
(0, '1', 'sub_account', 'create', '17', 'acc_subcode', 'subaccount_list', '{\"subTypeId\":\"7\",\"name\":\"Debtor Test 01\",\"referenceNo\":0,\"status\":1,\"created_by\":\"1\",\"created_date\":\"2024-07-05 05:01:33\"}', '2024-07-05 22:31:33', 1),
(0, '1', 'sub_account', 'create', '18', 'acc_subcode', 'subaccount_list', '{\"subTypeId\":\"7\",\"name\":\"Debtor Test 02\",\"referenceNo\":0,\"status\":1,\"created_by\":\"1\",\"created_date\":\"2024-07-05 05:01:42\"}', '2024-07-05 22:31:42', 1),
(0, '1', 'sub_account', 'create', '19', 'acc_subcode', 'subaccount_list', '{\"subTypeId\":\"8\",\"name\":\"Creditor Test 01\",\"referenceNo\":0,\"status\":1,\"created_by\":\"1\",\"created_date\":\"2024-07-05 05:01:58\"}', '2024-07-05 22:31:58', 1),
(0, '1', 'sub_account', 'create', '20', 'acc_subcode', 'subaccount_list', '{\"subTypeId\":\"8\",\"name\":\"Creditor Test 02\",\"referenceNo\":0,\"status\":1,\"created_by\":\"1\",\"created_date\":\"2024-07-05 05:02:08\"}', '2024-07-05 22:32:08', 1),
(0, '1', 'credit_vaucher', 'create', 'CV-29', 'acc_vaucher', 'account/accounts/store_credit_voucher', '{\"fyear\":\"1\",\"VNo\":\"CV-29\",\"Vtype\":\"CV\",\"referenceNo\":null,\"VDate\":\"2024-07-05\",\"COAID\":\"3020101\",\"Narration\":\"\",\"chequeNo\":null,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":0,\"Credit\":\"250\",\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-05 18:01:33\",\"status\":0}', '2024-07-05 23:31:33', 1),
(0, '1', 'approved_vaucher_transation', 'create', '211', 'acc_transaction', 'account/accounts/isactive/CV-29/active', '{\"vid\":\"112\",\"fyear\":\"1\",\"VNo\":\"CV-29\",\"Vtype\":\"CV\",\"referenceNo\":null,\"VDate\":\"2024-07-05\",\"COAID\":\"3020101\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"0.00\",\"Credit\":\"250.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-05 18:15:48\"}', '2024-07-05 23:45:48', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '212', 'acc_transaction', 'account/accounts/isactive/CV-29/active', '{\"vid\":\"112\",\"fyear\":\"1\",\"VNo\":\"CV-29\",\"Vtype\":\"CV\",\"referenceNo\":null,\"VDate\":\"2024-07-05\",\"COAID\":\"1020103\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"250.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-05 18:15:48\"}', '2024-07-05 23:45:48', 1),
(0, '1', 'credit_vaucher', 'create', 'CV-30', 'acc_vaucher', 'account/accounts/store_credit_voucher', '{\"fyear\":\"1\",\"VNo\":\"CV-30\",\"Vtype\":\"CV\",\"referenceNo\":null,\"VDate\":\"2024-07-05\",\"COAID\":\"3020101\",\"Narration\":\"XX\",\"chequeNo\":null,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"XXX\",\"Debit\":0,\"Credit\":\"450\",\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-05 18:24:10\",\"status\":0}', '2024-07-05 23:54:10', 1),
(0, '1', 'debit_vaucher', 'create', 'DV-16', 'acc_vaucher', 'account/accounts/store_debit_voucher', '{\"fyear\":\"1\",\"VNo\":\"DV-16\",\"Vtype\":\"DV\",\"referenceNo\":null,\"VDate\":\"2024-07-05\",\"COAID\":\"4021001\",\"Narration\":\"YY\",\"chequeNo\":null,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"YYY\",\"Debit\":\"700\",\"Credit\":0,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-05 18:29:11\",\"status\":0}', '2024-07-05 23:59:11', 1),
(0, '1', 'approved_vaucher_transation', 'create', '213', 'acc_transaction', 'account/accounts/bulk_voucher_approve', '{\"vid\":\"114\",\"fyear\":\"1\",\"VNo\":\"DV-16\",\"Vtype\":\"DV\",\"referenceNo\":null,\"VDate\":\"2024-07-05\",\"COAID\":\"4021001\",\"Narration\":\"YY\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"YYY\",\"Debit\":\"700.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-05 18:29:32\"}', '2024-07-05 23:59:32', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '214', 'acc_transaction', 'account/accounts/bulk_voucher_approve', '{\"vid\":\"114\",\"fyear\":\"1\",\"VNo\":\"DV-16\",\"Vtype\":\"DV\",\"referenceNo\":null,\"VDate\":\"2024-07-05\",\"COAID\":\"1020103\",\"Narration\":\"YY\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"YYY\",\"Debit\":\"0.00\",\"Credit\":\"700.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021001\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-05 18:29:32\"}', '2024-07-05 23:59:32', 1),
(0, '1', 'approved_vaucher_transation', 'create', '215', 'acc_transaction', 'account/accounts/bulk_voucher_approve', '{\"vid\":\"113\",\"fyear\":\"1\",\"VNo\":\"CV-30\",\"Vtype\":\"CV\",\"referenceNo\":null,\"VDate\":\"2024-07-05\",\"COAID\":\"3020101\",\"Narration\":\"XX\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"XXX\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-05 18:29:32\"}', '2024-07-05 23:59:32', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '216', 'acc_transaction', 'account/accounts/bulk_voucher_approve', '{\"vid\":\"113\",\"fyear\":\"1\",\"VNo\":\"CV-30\",\"Vtype\":\"CV\",\"referenceNo\":null,\"VDate\":\"2024-07-05\",\"COAID\":\"1020103\",\"Narration\":\"XX\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"XXX\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-05 18:29:32\"}', '2024-07-05 23:59:32', 1),
(0, '1', 'credit_vaucher', 'create', 'CV-31', 'acc_vaucher', 'account/accounts/store_credit_voucher', '{\"fyear\":\"1\",\"VNo\":\"CV-31\",\"Vtype\":\"CV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-17\",\"COAID\":\"5020202\",\"Narration\":\"\",\"chequeNo\":null,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":0,\"Credit\":\"10000\",\"RevCodde\":\"1020103\",\"subType\":\"8\",\"subCode\":\"19\",\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-17 15:29:19\",\"status\":0}', '2024-07-17 20:59:19', 1),
(0, '1', 'approved_vaucher_transation', 'create', '217', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"116\",\"fyear\":\"1\",\"VNo\":\"CV-32\",\"Vtype\":\"CV\",\"referenceNo\":\"1020\",\"VDate\":\"2024-07-21\",\"COAID\":\"1020103\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"800.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 00:53:00\"}', '2024-07-21 00:53:00', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '218', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"116\",\"fyear\":\"1\",\"VNo\":\"CV-32\",\"Vtype\":\"CV\",\"referenceNo\":\"1020\",\"VDate\":\"2024-07-21\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"800.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 00:53:00\"}', '2024-07-21 00:53:00', 1),
(0, '1', 'approved_vaucher_transation', 'create', '219', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"117\",\"fyear\":\"1\",\"VNo\":\"JV-55\",\"Vtype\":\"JV\",\"referenceNo\":\"1020\",\"VDate\":\"2024-07-21\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 00:53:00\"}', '2024-07-21 00:53:00', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '220', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"117\",\"fyear\":\"1\",\"VNo\":\"JV-55\",\"Vtype\":\"JV\",\"referenceNo\":\"1020\",\"VDate\":\"2024-07-21\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 00:53:00\"}', '2024-07-21 00:53:00', 1),
(0, '1', 'approved_vaucher_transation', 'create', '221', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"118\",\"fyear\":\"1\",\"VNo\":\"JV-56\",\"Vtype\":\"JV\",\"referenceNo\":\"1020\",\"VDate\":\"2024-07-21\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 00:53:00\"}', '2024-07-21 00:53:00', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '222', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"118\",\"fyear\":\"1\",\"VNo\":\"JV-56\",\"Vtype\":\"JV\",\"referenceNo\":\"1020\",\"VDate\":\"2024-07-21\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 00:53:00\"}', '2024-07-21 00:53:00', 1),
(0, '1', 'debit_vaucher', 'create', 'DV-17', 'acc_vaucher', 'account/accounts/store_debit_voucher', '{\"fyear\":\"1\",\"VNo\":\"DV-17\",\"Vtype\":\"DV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-21\",\"COAID\":\"5020202\",\"Narration\":\"\",\"chequeNo\":\"30\",\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":\"300\",\"Credit\":0,\"RevCodde\":\"1020103\",\"subType\":\"8\",\"subCode\":\"19\",\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 06:09:24\",\"status\":0}', '2024-07-21 11:39:24', 1),
(0, '1', 'approved_vaucher_transation', 'create', '223', 'acc_transaction', 'account/accounts/isactive/DV-17/active', '{\"vid\":\"119\",\"fyear\":\"1\",\"VNo\":\"DV-17\",\"Vtype\":\"DV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-21\",\"COAID\":\"5020202\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"300.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"8\",\"subCode\":\"19\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 06:10:25\"}', '2024-07-21 11:40:25', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '224', 'acc_transaction', 'account/accounts/isactive/DV-17/active', '{\"vid\":\"119\",\"fyear\":\"1\",\"VNo\":\"DV-17\",\"Vtype\":\"DV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-21\",\"COAID\":\"1020103\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"0.00\",\"Credit\":\"300.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020202\",\"subType\":\"8\",\"subCode\":\"19\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 06:10:25\"}', '2024-07-21 11:40:25', 1),
(0, '1', 'credit_vaucher', 'create', 'CV-33', 'acc_vaucher', 'account/accounts/store_credit_voucher', '{\"fyear\":\"1\",\"VNo\":\"CV-33\",\"Vtype\":\"CV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-21\",\"COAID\":\"1020803\",\"Narration\":\"\",\"chequeNo\":33,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":0,\"Credit\":\"300\",\"RevCodde\":\"1020103\",\"subType\":\"7\",\"subCode\":\"17\",\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 06:11:54\",\"status\":0}', '2024-07-21 11:41:54', 1),
(0, '1', 'approved_vaucher_transation', 'create', '225', 'acc_transaction', 'account/accounts/isactive/CV-33/active', '{\"vid\":\"120\",\"fyear\":\"1\",\"VNo\":\"CV-33\",\"Vtype\":\"CV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-21\",\"COAID\":\"1020803\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"0.00\",\"Credit\":\"300.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"7\",\"subCode\":\"17\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 06:12:52\"}', '2024-07-21 11:42:52', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '226', 'acc_transaction', 'account/accounts/isactive/CV-33/active', '{\"vid\":\"120\",\"fyear\":\"1\",\"VNo\":\"CV-33\",\"Vtype\":\"CV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-21\",\"COAID\":\"1020103\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"300.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020803\",\"subType\":\"7\",\"subCode\":\"17\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 06:12:52\"}', '2024-07-21 11:42:52', 1),
(0, '1', 'approved_vaucher_transation', 'create', '227', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"121\",\"fyear\":\"1\",\"VNo\":\"CV-34\",\"Vtype\":\"CV\",\"referenceNo\":\"1021\",\"VDate\":\"2024-07-21\",\"COAID\":\"1020101\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"800.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 12:43:07\"}', '2024-07-21 12:43:07', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '228', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"121\",\"fyear\":\"1\",\"VNo\":\"CV-34\",\"Vtype\":\"CV\",\"referenceNo\":\"1021\",\"VDate\":\"2024-07-21\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"800.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 12:43:07\"}', '2024-07-21 12:43:07', 1),
(0, '1', 'approved_vaucher_transation', 'create', '229', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"122\",\"fyear\":\"1\",\"VNo\":\"JV-57\",\"Vtype\":\"JV\",\"referenceNo\":\"1021\",\"VDate\":\"2024-07-21\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 12:43:07\"}', '2024-07-21 12:43:07', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '230', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"122\",\"fyear\":\"1\",\"VNo\":\"JV-57\",\"Vtype\":\"JV\",\"referenceNo\":\"1021\",\"VDate\":\"2024-07-21\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 12:43:07\"}', '2024-07-21 12:43:07', 1),
(0, '1', 'approved_vaucher_transation', 'create', '231', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"123\",\"fyear\":\"1\",\"VNo\":\"JV-58\",\"Vtype\":\"JV\",\"referenceNo\":\"1021\",\"VDate\":\"2024-07-21\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 12:43:07\"}', '2024-07-21 12:43:07', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '232', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"123\",\"fyear\":\"1\",\"VNo\":\"JV-58\",\"Vtype\":\"JV\",\"referenceNo\":\"1021\",\"VDate\":\"2024-07-21\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 12:43:07\"}', '2024-07-21 12:43:07', 1),
(0, '1', 'approved_vaucher_transation', 'create', '233', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"124\",\"fyear\":\"1\",\"VNo\":\"CV-35\",\"Vtype\":\"CV\",\"referenceNo\":\"1022\",\"VDate\":\"2024-07-21\",\"COAID\":\"1020103\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"800.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 12:46:15\"}', '2024-07-21 12:46:15', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '234', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"124\",\"fyear\":\"1\",\"VNo\":\"CV-35\",\"Vtype\":\"CV\",\"referenceNo\":\"1022\",\"VDate\":\"2024-07-21\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"800.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 12:46:15\"}', '2024-07-21 12:46:15', 1),
(0, '1', 'approved_vaucher_transation', 'create', '235', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"125\",\"fyear\":\"1\",\"VNo\":\"JV-59\",\"Vtype\":\"JV\",\"referenceNo\":\"1022\",\"VDate\":\"2024-07-21\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 12:46:15\"}', '2024-07-21 12:46:15', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '236', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"125\",\"fyear\":\"1\",\"VNo\":\"JV-59\",\"Vtype\":\"JV\",\"referenceNo\":\"1022\",\"VDate\":\"2024-07-21\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 12:46:15\"}', '2024-07-21 12:46:15', 1),
(0, '1', 'approved_vaucher_transation', 'create', '237', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"126\",\"fyear\":\"1\",\"VNo\":\"JV-60\",\"Vtype\":\"JV\",\"referenceNo\":\"1022\",\"VDate\":\"2024-07-21\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 12:46:15\"}', '2024-07-21 12:46:15', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '238', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"126\",\"fyear\":\"1\",\"VNo\":\"JV-60\",\"Vtype\":\"JV\",\"referenceNo\":\"1022\",\"VDate\":\"2024-07-21\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 12:46:15\"}', '2024-07-21 12:46:15', 1),
(0, '1', 'credit_vaucher', 'create', 'CV-36', 'acc_vaucher', 'account/accounts/store_credit_voucher', '{\"fyear\":\"1\",\"VNo\":\"CV-36\",\"Vtype\":\"CV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-21\",\"COAID\":\"5020202\",\"Narration\":\"\",\"chequeNo\":35,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":0,\"Credit\":\"100\",\"RevCodde\":\"1020103\",\"subType\":\"8\",\"subCode\":\"19\",\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 07:19:32\",\"status\":0}', '2024-07-21 12:49:32', 1),
(0, '1', 'approved_vaucher_transation', 'create', '239', 'acc_transaction', 'account/accounts/isactive/CV-36/active', '{\"vid\":\"127\",\"fyear\":\"1\",\"VNo\":\"CV-36\",\"Vtype\":\"CV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-21\",\"COAID\":\"5020202\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"0.00\",\"Credit\":\"100.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"8\",\"subCode\":\"19\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 07:19:51\"}', '2024-07-21 12:49:51', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '240', 'acc_transaction', 'account/accounts/isactive/CV-36/active', '{\"vid\":\"127\",\"fyear\":\"1\",\"VNo\":\"CV-36\",\"Vtype\":\"CV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-21\",\"COAID\":\"1020103\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"100.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020202\",\"subType\":\"8\",\"subCode\":\"19\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 07:19:51\"}', '2024-07-21 12:49:51', 1),
(0, '1', 'debit_vaucher', 'create', 'DV-18', 'acc_vaucher', 'account/accounts/store_debit_voucher', '{\"fyear\":\"1\",\"VNo\":\"DV-18\",\"Vtype\":\"DV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-21\",\"COAID\":\"5020202\",\"Narration\":\"\",\"chequeNo\":\"36\",\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":\"100\",\"Credit\":0,\"RevCodde\":\"1020103\",\"subType\":\"8\",\"subCode\":\"19\",\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 07:21:01\",\"status\":0}', '2024-07-21 12:51:01', 1),
(0, '1', 'credit_vaucher', 'create', 'CV-37', 'acc_vaucher', 'account/accounts/store_credit_voucher', '{\"fyear\":\"1\",\"VNo\":\"CV-37\",\"Vtype\":\"CV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-21\",\"COAID\":\"5020202\",\"Narration\":\"\",\"chequeNo\":0,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":0,\"Credit\":\"120\",\"RevCodde\":\"1020101\",\"subType\":\"8\",\"subCode\":\"20\",\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 07:44:09\",\"status\":0}', '2024-07-21 13:14:09', 1),
(0, '1', 'approved_vaucher_transation', 'create', '241', 'acc_transaction', 'account/accounts/isactive/CV-37/active', '{\"vid\":\"129\",\"fyear\":\"1\",\"VNo\":\"CV-37\",\"Vtype\":\"CV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-21\",\"COAID\":\"5020202\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"0.00\",\"Credit\":\"120.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"8\",\"subCode\":\"20\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 07:45:03\"}', '2024-07-21 13:15:03', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '242', 'acc_transaction', 'account/accounts/isactive/CV-37/active', '{\"vid\":\"129\",\"fyear\":\"1\",\"VNo\":\"CV-37\",\"Vtype\":\"CV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-21\",\"COAID\":\"1020101\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"120.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020202\",\"subType\":\"8\",\"subCode\":\"20\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 07:45:03\"}', '2024-07-21 13:15:03', 1),
(0, '1', 'approved_vaucher_transation', 'create', '1', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"1\",\"fyear\":\"1\",\"VNo\":\"DV-1\",\"Vtype\":\"DV\",\"referenceNo\":\"1\",\"VDate\":\"2024-07-27\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"4500.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-27 14:18:56\"}', '2024-07-27 14:18:56', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '2', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"1\",\"fyear\":\"1\",\"VNo\":\"DV-1\",\"Vtype\":\"DV\",\"referenceNo\":\"1\",\"VDate\":\"2024-07-27\",\"COAID\":\"1020101\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"4500.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-27 14:18:56\"}', '2024-07-27 14:18:56', 1),
(0, '1', 'approved_vaucher_transation', 'create', '3', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"2\",\"fyear\":\"1\",\"VNo\":\"CV-1\",\"Vtype\":\"CV\",\"referenceNo\":\"1000\",\"VDate\":\"2024-07-27\",\"COAID\":\"1020103\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"800.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-27 14:20:02\"}', '2024-07-27 14:20:02', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '4', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"2\",\"fyear\":\"1\",\"VNo\":\"CV-1\",\"Vtype\":\"CV\",\"referenceNo\":\"1000\",\"VDate\":\"2024-07-27\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"800.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-27 14:20:02\"}', '2024-07-27 14:20:02', 1),
(0, '1', 'approved_vaucher_transation', 'create', '5', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"3\",\"fyear\":\"1\",\"VNo\":\"JV-1\",\"Vtype\":\"JV\",\"referenceNo\":\"1000\",\"VDate\":\"2024-07-27\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-27 14:20:02\"}', '2024-07-27 14:20:02', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '6', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"3\",\"fyear\":\"1\",\"VNo\":\"JV-1\",\"Vtype\":\"JV\",\"referenceNo\":\"1000\",\"VDate\":\"2024-07-27\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-27 14:20:02\"}', '2024-07-27 14:20:02', 1),
(0, '1', 'approved_vaucher_transation', 'create', '7', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"4\",\"fyear\":\"1\",\"VNo\":\"JV-2\",\"Vtype\":\"JV\",\"referenceNo\":\"1000\",\"VDate\":\"2024-07-27\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-27 14:20:02\"}', '2024-07-27 14:20:02', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '8', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"4\",\"fyear\":\"1\",\"VNo\":\"JV-2\",\"Vtype\":\"JV\",\"referenceNo\":\"1000\",\"VDate\":\"2024-07-27\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-27 14:20:02\"}', '2024-07-27 14:20:02', 1),
(0, '1', 'debit_vaucher', 'create', 'DV-2', 'acc_vaucher', 'account/accounts/store_debit_voucher', '{\"fyear\":\"1\",\"VNo\":\"DV-2\",\"Vtype\":\"DV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-27\",\"COAID\":\"5020202\",\"Narration\":\"\",\"chequeNo\":\"1\",\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"Testing\",\"Debit\":\"800\",\"Credit\":0,\"RevCodde\":\"1020103\",\"subType\":\"8\",\"subCode\":\"19\",\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-27 08:52:47\",\"status\":0}', '2024-07-27 14:22:47', 1),
(0, '1', 'approved_vaucher_transation', 'create', '9', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"6\",\"fyear\":\"1\",\"VNo\":\"CV-2\",\"Vtype\":\"CV\",\"referenceNo\":\"1001\",\"VDate\":\"2024-08-03\",\"COAID\":\"1020103\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"200.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-03 09:01:57\"}', '2024-08-03 09:01:57', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '10', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"6\",\"fyear\":\"1\",\"VNo\":\"CV-2\",\"Vtype\":\"CV\",\"referenceNo\":\"1001\",\"VDate\":\"2024-08-03\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"200.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-03 09:01:57\"}', '2024-08-03 09:01:57', 1),
(0, '1', 'approved_vaucher_transation', 'create', '11', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"7\",\"fyear\":\"1\",\"VNo\":\"CV-3\",\"Vtype\":\"CV\",\"referenceNo\":\"1001\",\"VDate\":\"2024-08-03\",\"COAID\":\"1020101\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"600.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-03 09:01:57\"}', '2024-08-03 09:01:57', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '12', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"7\",\"fyear\":\"1\",\"VNo\":\"CV-3\",\"Vtype\":\"CV\",\"referenceNo\":\"1001\",\"VDate\":\"2024-08-03\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"600.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-03 09:01:57\"}', '2024-08-03 09:01:57', 1),
(0, '1', 'approved_vaucher_transation', 'create', '13', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"8\",\"fyear\":\"1\",\"VNo\":\"JV-3\",\"Vtype\":\"JV\",\"referenceNo\":\"1001\",\"VDate\":\"2024-08-03\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-03 09:01:57\"}', '2024-08-03 09:01:57', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '14', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"8\",\"fyear\":\"1\",\"VNo\":\"JV-3\",\"Vtype\":\"JV\",\"referenceNo\":\"1001\",\"VDate\":\"2024-08-03\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-03 09:01:57\"}', '2024-08-03 09:01:57', 1),
(0, '1', 'approved_vaucher_transation', 'create', '15', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"9\",\"fyear\":\"1\",\"VNo\":\"JV-4\",\"Vtype\":\"JV\",\"referenceNo\":\"1001\",\"VDate\":\"2024-08-03\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-03 09:01:57\"}', '2024-08-03 09:01:57', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '16', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"9\",\"fyear\":\"1\",\"VNo\":\"JV-4\",\"Vtype\":\"JV\",\"referenceNo\":\"1001\",\"VDate\":\"2024-08-03\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-03 09:01:57\"}', '2024-08-03 09:01:57', 1),
(0, '1', 'approved_vaucher_transation', 'create', '17', 'acc_transaction', 'account/accounts/isactive/DV-2/active', '{\"vid\":\"5\",\"fyear\":\"1\",\"VNo\":\"DV-2\",\"Vtype\":\"DV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-27\",\"COAID\":\"5020202\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Testing\",\"Debit\":\"800.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"8\",\"subCode\":\"19\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-03 03:32:45\"}', '2024-08-03 09:02:45', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '18', 'acc_transaction', 'account/accounts/isactive/DV-2/active', '{\"vid\":\"5\",\"fyear\":\"1\",\"VNo\":\"DV-2\",\"Vtype\":\"DV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-27\",\"COAID\":\"1020103\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Testing\",\"Debit\":\"0.00\",\"Credit\":\"800.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020202\",\"subType\":\"8\",\"subCode\":\"19\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-03 03:32:45\"}', '2024-08-03 09:02:45', 1),
(0, '1', 'approved_vaucher_transation', 'create', '19', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"10\",\"fyear\":\"1\",\"VNo\":\"DV-3\",\"Vtype\":\"DV\",\"referenceNo\":\"2\",\"VDate\":\"2024-08-05\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"20000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 11:43:32\"}', '2024-08-05 11:43:32', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '20', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"10\",\"fyear\":\"1\",\"VNo\":\"DV-3\",\"Vtype\":\"DV\",\"referenceNo\":\"2\",\"VDate\":\"2024-08-05\",\"COAID\":\"1020101\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"20000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 11:43:32\"}', '2024-08-05 11:43:32', 1),
(0, '1', 'approved_vaucher_transation', 'create', '21', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"11\",\"fyear\":\"1\",\"VNo\":\"CV-4\",\"Vtype\":\"CV\",\"referenceNo\":\"1002\",\"VDate\":\"2024-08-05\",\"COAID\":\"1020101\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"800.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 18:34:20\"}', '2024-08-05 18:34:20', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '22', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"11\",\"fyear\":\"1\",\"VNo\":\"CV-4\",\"Vtype\":\"CV\",\"referenceNo\":\"1002\",\"VDate\":\"2024-08-05\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"800.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 18:34:20\"}', '2024-08-05 18:34:20', 1),
(0, '1', 'approved_vaucher_transation', 'create', '23', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"12\",\"fyear\":\"1\",\"VNo\":\"JV-5\",\"Vtype\":\"JV\",\"referenceNo\":\"1002\",\"VDate\":\"2024-08-05\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 18:34:20\"}', '2024-08-05 18:34:20', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '24', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"12\",\"fyear\":\"1\",\"VNo\":\"JV-5\",\"Vtype\":\"JV\",\"referenceNo\":\"1002\",\"VDate\":\"2024-08-05\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 18:34:20\"}', '2024-08-05 18:34:20', 1),
(0, '1', 'approved_vaucher_transation', 'create', '25', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"13\",\"fyear\":\"1\",\"VNo\":\"JV-6\",\"Vtype\":\"JV\",\"referenceNo\":\"1002\",\"VDate\":\"2024-08-05\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 18:34:20\"}', '2024-08-05 18:34:20', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '26', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"13\",\"fyear\":\"1\",\"VNo\":\"JV-6\",\"Vtype\":\"JV\",\"referenceNo\":\"1002\",\"VDate\":\"2024-08-05\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 18:34:20\"}', '2024-08-05 18:34:20', 1),
(0, '1', 'credit_vaucher', 'create', 'CV-5', 'acc_vaucher', 'account/accounts/store_credit_voucher', '{\"fyear\":\"1\",\"VNo\":\"CV-5\",\"Vtype\":\"CV\",\"referenceNo\":\"0\",\"VDate\":\"2024-08-05\",\"COAID\":\"5020202\",\"Narration\":\"\",\"chequeNo\":3,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":0,\"Credit\":\"1002\",\"RevCodde\":\"1020103\",\"subType\":\"8\",\"subCode\":\"19\",\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 14:16:01\",\"status\":0}', '2024-08-05 19:46:01', 1);
INSERT INTO `activity_logs` (`activity_id`, `user_id`, `type`, `action`, `action_id`, `table_name`, `slug`, `form_data`, `create_date`, `status`) VALUES
(0, '1', 'approved_vaucher_transation', 'create', '27', 'acc_transaction', 'account/accounts/isactive/CV-5/active', '{\"vid\":\"14\",\"fyear\":\"1\",\"VNo\":\"CV-5\",\"Vtype\":\"CV\",\"referenceNo\":\"0\",\"VDate\":\"2024-08-05\",\"COAID\":\"5020202\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"0.00\",\"Credit\":\"1002.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"8\",\"subCode\":\"19\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 14:16:24\"}', '2024-08-05 19:46:24', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '28', 'acc_transaction', 'account/accounts/isactive/CV-5/active', '{\"vid\":\"14\",\"fyear\":\"1\",\"VNo\":\"CV-5\",\"Vtype\":\"CV\",\"referenceNo\":\"0\",\"VDate\":\"2024-08-05\",\"COAID\":\"1020103\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"1002.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020202\",\"subType\":\"8\",\"subCode\":\"19\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 14:16:24\"}', '2024-08-05 19:46:24', 1),
(0, '1', 'debit_vaucher', 'create', 'DV-4', 'acc_vaucher', 'account/accounts/store_debit_voucher', '{\"fyear\":\"1\",\"VNo\":\"DV-4\",\"Vtype\":\"DV\",\"referenceNo\":\"0\",\"VDate\":\"2024-08-05\",\"COAID\":\"1020803\",\"Narration\":\"\",\"chequeNo\":\"3\",\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":\"1002\",\"Credit\":0,\"RevCodde\":\"1020103\",\"subType\":\"7\",\"subCode\":\"18\",\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 14:18:50\",\"status\":0}', '2024-08-05 19:48:50', 1),
(0, '1', 'approved_vaucher_transation', 'create', '29', 'acc_transaction', 'account/accounts/bulk_voucher_approve', '{\"vid\":\"15\",\"fyear\":\"1\",\"VNo\":\"DV-4\",\"Vtype\":\"DV\",\"referenceNo\":\"0\",\"VDate\":\"2024-08-05\",\"COAID\":\"1020803\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"1002.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"7\",\"subCode\":\"18\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 14:20:09\"}', '2024-08-05 19:50:09', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '30', 'acc_transaction', 'account/accounts/bulk_voucher_approve', '{\"vid\":\"15\",\"fyear\":\"1\",\"VNo\":\"DV-4\",\"Vtype\":\"DV\",\"referenceNo\":\"0\",\"VDate\":\"2024-08-05\",\"COAID\":\"1020103\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"0.00\",\"Credit\":\"1002.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020803\",\"subType\":\"7\",\"subCode\":\"18\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 14:20:09\"}', '2024-08-05 19:50:09', 1),
(0, '1', 'contra_vaucher', 'create', 'CT-1', 'acc_vaucher', 'account/accounts/store_contra_voucher', '{\"fyear\":\"1\",\"VNo\":\"CT-1\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-08-05\",\"COAID\":\"1020501\",\"Narration\":\"\",\"chequeNo\":\"2\",\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":\"200\",\"Credit\":0,\"RevCodde\":\"1020103\",\"subType\":1,\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 14:21:59\",\"status\":0}', '2024-08-05 19:51:59', 1),
(0, '1', 'approved_vaucher_transation', 'create', '31', 'acc_transaction', 'account/accounts/isactive/CT-1/active', '{\"vid\":\"16\",\"fyear\":\"1\",\"VNo\":\"CT-1\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-08-05\",\"COAID\":\"1020501\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"200.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 14:22:21\"}', '2024-08-05 19:52:21', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '32', 'acc_transaction', 'account/accounts/isactive/CT-1/active', '{\"vid\":\"16\",\"fyear\":\"1\",\"VNo\":\"CT-1\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-08-05\",\"COAID\":\"1020103\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"0.00\",\"Credit\":\"200.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020501\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 14:22:21\"}', '2024-08-05 19:52:21', 1),
(0, '1', 'approved_vaucher_transation', 'create', '33', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"17\",\"fyear\":\"1\",\"VNo\":\"CV-6\",\"Vtype\":\"CV\",\"referenceNo\":\"1003\",\"VDate\":\"2024-08-07\",\"COAID\":\"1020101\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"1000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-07 11:04:13\"}', '2024-08-07 11:04:13', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '34', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"17\",\"fyear\":\"1\",\"VNo\":\"CV-6\",\"Vtype\":\"CV\",\"referenceNo\":\"1003\",\"VDate\":\"2024-08-07\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"1000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-07 11:04:13\"}', '2024-08-07 11:04:13', 1),
(0, '1', 'approved_vaucher_transation', 'create', '35', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"18\",\"fyear\":\"1\",\"VNo\":\"JV-7\",\"Vtype\":\"JV\",\"referenceNo\":\"1003\",\"VDate\":\"2024-08-07\",\"COAID\":\"1020801\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"600.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"3\",\"subCode\":\"2\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-07 11:04:13\"}', '2024-08-07 11:04:13', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '36', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"18\",\"fyear\":\"1\",\"VNo\":\"JV-7\",\"Vtype\":\"JV\",\"referenceNo\":\"1003\",\"VDate\":\"2024-08-07\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"600.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020801\",\"subType\":\"3\",\"subCode\":\"2\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-07 11:04:13\"}', '2024-08-07 11:04:13', 1),
(0, '1', 'approved_vaucher_transation', 'create', '37', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"19\",\"fyear\":\"1\",\"VNo\":\"JV-8\",\"Vtype\":\"JV\",\"referenceNo\":\"1003\",\"VDate\":\"2024-08-07\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"900.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-07 11:04:13\"}', '2024-08-07 11:04:13', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '38', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"19\",\"fyear\":\"1\",\"VNo\":\"JV-8\",\"Vtype\":\"JV\",\"referenceNo\":\"1003\",\"VDate\":\"2024-08-07\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"900.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-07 11:04:13\"}', '2024-08-07 11:04:13', 1),
(0, '1', 'approved_vaucher_transation', 'create', '39', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"20\",\"fyear\":\"1\",\"VNo\":\"JV-9\",\"Vtype\":\"JV\",\"referenceNo\":\"1003\",\"VDate\":\"2024-08-07\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-07 11:04:13\"}', '2024-08-07 11:04:13', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '40', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"20\",\"fyear\":\"1\",\"VNo\":\"JV-9\",\"Vtype\":\"JV\",\"referenceNo\":\"1003\",\"VDate\":\"2024-08-07\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-07 11:04:13\"}', '2024-08-07 11:04:13', 1),
(0, '1', 'approved_vaucher_transation', 'create', '41', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"21\",\"fyear\":\"1\",\"VNo\":\"CV-7\",\"Vtype\":\"CV\",\"referenceNo\":\"1004\",\"VDate\":\"2024-08-07\",\"COAID\":\"1020101\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"1600.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-07 11:04:38\"}', '2024-08-07 11:04:38', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '42', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"21\",\"fyear\":\"1\",\"VNo\":\"CV-7\",\"Vtype\":\"CV\",\"referenceNo\":\"1004\",\"VDate\":\"2024-08-07\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"1600.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-07 11:04:38\"}', '2024-08-07 11:04:38', 1),
(0, '1', 'approved_vaucher_transation', 'create', '43', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"22\",\"fyear\":\"1\",\"VNo\":\"JV-10\",\"Vtype\":\"JV\",\"referenceNo\":\"1004\",\"VDate\":\"2024-08-07\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"900.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-07 11:04:38\"}', '2024-08-07 11:04:38', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '44', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"22\",\"fyear\":\"1\",\"VNo\":\"JV-10\",\"Vtype\":\"JV\",\"referenceNo\":\"1004\",\"VDate\":\"2024-08-07\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"900.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-07 11:04:38\"}', '2024-08-07 11:04:38', 1),
(0, '1', 'approved_vaucher_transation', 'create', '45', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"23\",\"fyear\":\"1\",\"VNo\":\"JV-11\",\"Vtype\":\"JV\",\"referenceNo\":\"1004\",\"VDate\":\"2024-08-07\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-07 11:04:38\"}', '2024-08-07 11:04:38', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '46', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"23\",\"fyear\":\"1\",\"VNo\":\"JV-11\",\"Vtype\":\"JV\",\"referenceNo\":\"1004\",\"VDate\":\"2024-08-07\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-07 11:04:38\"}', '2024-08-07 11:04:38', 1),
(0, '1', 'approved_vaucher_transation', 'create', '47', 'acc_transaction', 'account/accounts/create_customer_receive', '{\"vid\":\"24\",\"fyear\":\"1\",\"VNo\":\"CV-8\",\"Vtype\":\"CV\",\"referenceNo\":\"1003\",\"VDate\":\"2024-08-11\",\"COAID\":\"1020801\",\"Narration\":\"Sales Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Due Voucher for Customer\",\"Debit\":\"0.00\",\"Credit\":\"100.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"3\",\"subCode\":\"2\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-11 17:37:45\"}', '2024-08-11 17:37:45', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '48', 'acc_transaction', 'account/accounts/create_customer_receive', '{\"vid\":\"24\",\"fyear\":\"1\",\"VNo\":\"CV-8\",\"Vtype\":\"CV\",\"referenceNo\":\"1003\",\"VDate\":\"2024-08-11\",\"COAID\":\"1020101\",\"Narration\":\"Sales Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Due Voucher for Customer\",\"Debit\":\"100.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020801\",\"subType\":\"3\",\"subCode\":\"2\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-11 17:37:45\"}', '2024-08-11 17:37:45', 1),
(0, '1', 'approved_vaucher_transation', 'create', '49', 'acc_transaction', 'invoice/invoice/bdtask_manual_possales_insert', '{\"vid\":\"25\",\"fyear\":\"1\",\"VNo\":\"CV-9\",\"Vtype\":\"CV\",\"referenceNo\":\"1005\",\"VDate\":\"2024-08-11\",\"COAID\":\"1020101\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"550000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-11 21:34:31\"}', '2024-08-11 21:34:31', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '50', 'acc_transaction', 'invoice/invoice/bdtask_manual_possales_insert', '{\"vid\":\"25\",\"fyear\":\"1\",\"VNo\":\"CV-9\",\"Vtype\":\"CV\",\"referenceNo\":\"1005\",\"VDate\":\"2024-08-11\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"550000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-11 21:34:31\"}', '2024-08-11 21:34:31', 1),
(0, '1', 'approved_vaucher_transation', 'create', '51', 'acc_transaction', 'invoice/invoice/bdtask_manual_possales_insert', '{\"vid\":\"26\",\"fyear\":\"1\",\"VNo\":\"JV-12\",\"Vtype\":\"JV\",\"referenceNo\":\"1005\",\"VDate\":\"2024-08-11\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-11 21:34:31\"}', '2024-08-11 21:34:31', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '52', 'acc_transaction', 'invoice/invoice/bdtask_manual_possales_insert', '{\"vid\":\"26\",\"fyear\":\"1\",\"VNo\":\"JV-12\",\"Vtype\":\"JV\",\"referenceNo\":\"1005\",\"VDate\":\"2024-08-11\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-11 21:34:31\"}', '2024-08-11 21:34:31', 1),
(0, '1', 'approved_vaucher_transation', 'create', '53', 'acc_transaction', 'invoice/invoice/bdtask_manual_possales_insert', '{\"vid\":\"27\",\"fyear\":\"1\",\"VNo\":\"JV-13\",\"Vtype\":\"JV\",\"referenceNo\":\"1005\",\"VDate\":\"2024-08-11\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-11 21:34:31\"}', '2024-08-11 21:34:31', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '54', 'acc_transaction', 'invoice/invoice/bdtask_manual_possales_insert', '{\"vid\":\"27\",\"fyear\":\"1\",\"VNo\":\"JV-13\",\"Vtype\":\"JV\",\"referenceNo\":\"1005\",\"VDate\":\"2024-08-11\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-11 21:34:31\"}', '2024-08-11 21:34:31', 1),
(0, '1', 'approved_vaucher_transation', 'create', '55', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"28\",\"fyear\":\"1\",\"VNo\":\"DV-5\",\"Vtype\":\"DV\",\"referenceNo\":\"3\",\"VDate\":\"2024-08-13\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"400000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-13 22:31:11\"}', '2024-08-13 22:31:11', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '56', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"28\",\"fyear\":\"1\",\"VNo\":\"DV-5\",\"Vtype\":\"DV\",\"referenceNo\":\"3\",\"VDate\":\"2024-08-13\",\"COAID\":\"1020101\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"400000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-13 22:31:11\"}', '2024-08-13 22:31:11', 1),
(0, '1', 'approved_vaucher_transation', 'create', '57', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"29\",\"fyear\":\"1\",\"VNo\":\"CV-10\",\"Vtype\":\"CV\",\"referenceNo\":\"1006\",\"VDate\":\"2024-08-13\",\"COAID\":\"1020101\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"600000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-13 22:32:46\"}', '2024-08-13 22:32:46', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '58', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"29\",\"fyear\":\"1\",\"VNo\":\"CV-10\",\"Vtype\":\"CV\",\"referenceNo\":\"1006\",\"VDate\":\"2024-08-13\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"600000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-13 22:32:46\"}', '2024-08-13 22:32:46', 1),
(0, '1', 'approved_vaucher_transation', 'create', '59', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"30\",\"fyear\":\"1\",\"VNo\":\"JV-14\",\"Vtype\":\"JV\",\"referenceNo\":\"1006\",\"VDate\":\"2024-08-13\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"150675.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-13 22:32:46\"}', '2024-08-13 22:32:46', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '60', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"30\",\"fyear\":\"1\",\"VNo\":\"JV-14\",\"Vtype\":\"JV\",\"referenceNo\":\"1006\",\"VDate\":\"2024-08-13\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"150675.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-13 22:32:46\"}', '2024-08-13 22:32:46', 1),
(0, '1', 'approved_vaucher_transation', 'create', '61', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"31\",\"fyear\":\"1\",\"VNo\":\"JV-15\",\"Vtype\":\"JV\",\"referenceNo\":\"1006\",\"VDate\":\"2024-08-13\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-13 22:32:46\"}', '2024-08-13 22:32:46', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '62', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"31\",\"fyear\":\"1\",\"VNo\":\"JV-15\",\"Vtype\":\"JV\",\"referenceNo\":\"1006\",\"VDate\":\"2024-08-13\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-13 22:32:46\"}', '2024-08-13 22:32:46', 1),
(0, '1', 'financial_year', 'create', '1', 'financial_year', 'financial_year', '{\"yearName\":\"2024\",\"startDate\":\"2024-01-01\",\"endDate\":\"2024-12-31\",\"status\":\"1\",\"created_by\":\"1\",\"created_date\":\"2024-03-04 08:01:24\"}', '2024-03-05 00:31:24', 1),
(0, '1', 'credit_vaucher', 'create', 'CV-1', 'acc_vaucher', 'account/accounts/store_credit_voucher', '{\"fyear\":\"1\",\"VNo\":\"CV-1\",\"Vtype\":\"CV\",\"referenceNo\":null,\"VDate\":\"2024-01-01\",\"COAID\":\"2010202\",\"Narration\":\"Opening Balance\",\"chequeNo\":\"\",\"chequeDate\":\"2024-06-17\",\"isHonour\":0,\"ledgerComment\":\"Opening Balance\",\"Debit\":0,\"Credit\":\"50000\",\"RevCodde\":\"1020501\",\"subType\":\"1\",\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:06:22\",\"status\":0}', '2024-06-17 22:36:22', 1),
(0, '1', 'approved_vaucher_transation', 'create', '1', 'acc_transaction', 'account/accounts/isactive/CV-1/active', '{\"vid\":\"1\",\"fyear\":\"1\",\"VNo\":\"CV-1\",\"Vtype\":\"CV\",\"referenceNo\":null,\"VDate\":\"2024-01-01\",\"COAID\":\"2010202\",\"Narration\":\"Opening Balance\",\"chequeNo\":\"\",\"chequeDate\":\"2024-06-17\",\"isHonour\":\"0\",\"ledgerComment\":\"Opening Balance\",\"Debit\":\"0.00\",\"Credit\":\"50000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020501\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:07:20\"}', '2024-06-17 22:37:20', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '2', 'acc_transaction', 'account/accounts/isactive/CV-1/active', '{\"vid\":\"1\",\"fyear\":\"1\",\"VNo\":\"CV-1\",\"Vtype\":\"CV\",\"referenceNo\":null,\"VDate\":\"2024-01-01\",\"COAID\":\"1020501\",\"Narration\":\"Opening Balance\",\"chequeNo\":\"\",\"chequeDate\":\"2024-06-17\",\"isHonour\":\"0\",\"ledgerComment\":\"Opening Balance\",\"Debit\":\"50000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"2010202\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:07:20\"}', '2024-06-17 22:37:20', 1),
(0, '1', 'credit_vaucher', 'create', 'CV-2', 'acc_vaucher', 'account/accounts/store_credit_voucher', '{\"fyear\":\"1\",\"VNo\":\"CV-2\",\"Vtype\":\"CV\",\"referenceNo\":null,\"VDate\":\"2024-06-17\",\"COAID\":\"2010202\",\"Narration\":\"Opening Balance\",\"chequeNo\":\"\",\"chequeDate\":\"\",\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":0,\"Credit\":\"50000\",\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:09:23\",\"status\":0}', '2024-06-17 22:39:23', 1),
(0, '1', 'credit_vaucher', 'update', 'CV-2', 'acc_vaucher', 'account/accounts/update_credit_voucher', '{\"txtVNo\":\"CV-2\",\"isApproved\":\"0\",\"fyear\":\"1\",\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:09:23\",\"finyear\":\"1\",\"cmbDebit\":\"1020101\",\"dtpDate\":\"2024-06-17\",\"txtRemarks\":\"Opening Balance\",\"cmbCode\":[\"2010202\"],\"subtype\":[\"\"],\"isSubtype\":[\"1\"],\"txtComment\":[\"Opening Balance\"],\"txtAmount\":[\"50000.00\"],\"grand_total\":\"50000\",\"save\":\"Update\"}', '2024-06-17 22:39:30', 2),
(0, '1', 'credit_vaucher', 'update', 'CV-2', 'acc_vaucher', 'account/accounts/update_credit_voucher', '{\"txtVNo\":\"CV-2\",\"isApproved\":\"0\",\"fyear\":\"1\",\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:09:23\",\"finyear\":\"1\",\"cmbDebit\":\"1020101\",\"dtpDate\":\"2024-01-01\",\"txtRemarks\":\"Opening Balance\",\"cmbCode\":[\"2010202\"],\"subtype\":[\"\"],\"isSubtype\":[\"1\"],\"txtComment\":[\"Opening Balance\"],\"txtAmount\":[\"50000.00\"],\"grand_total\":\"50000\",\"save\":\"Update\"}', '2024-06-17 22:39:40', 2),
(0, '1', 'approved_vaucher_transation', 'create', '3', 'acc_transaction', 'account/accounts/isactive/CV-2/active', '{\"vid\":\"4\",\"fyear\":\"1\",\"VNo\":\"CV-2\",\"Vtype\":\"CV\",\"referenceNo\":null,\"VDate\":\"2024-01-01\",\"COAID\":\"2010202\",\"Narration\":\"Opening Balance\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Opening Balance\",\"Debit\":\"0.00\",\"Credit\":\"50000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:09:52\"}', '2024-06-17 22:39:52', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '4', 'acc_transaction', 'account/accounts/isactive/CV-2/active', '{\"vid\":\"4\",\"fyear\":\"1\",\"VNo\":\"CV-2\",\"Vtype\":\"CV\",\"referenceNo\":null,\"VDate\":\"2024-01-01\",\"COAID\":\"1020101\",\"Narration\":\"Opening Balance\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Opening Balance\",\"Debit\":\"50000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"2010202\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:09:52\"}', '2024-06-17 22:39:52', 1),
(0, '1', 'journal_vaucher', 'create', 'JV-1', 'acc_vaucher', 'account/accounts/store_journal_voucher', '{\"fyear\":\"1\",\"VNo\":\"JV-1\",\"Vtype\":\"JV\",\"referenceNo\":\"3\",\"VDate\":\"2024-01-01\",\"COAID\":\"1020801\",\"Narration\":\"Opening Balance\",\"chequeNo\":null,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"Opening Balance\",\"Debit\":\"50000\",\"Credit\":\"\",\"RevCodde\":\"2010202\",\"subType\":\"3\",\"subCode\":\"4\",\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:26:43\",\"status\":0}', '2024-06-17 22:56:43', 1),
(0, '1', 'journal_vaucher', 'create', 'JV-1', 'acc_vaucher', 'account/accounts/store_journal_voucher', '{\"fyear\":\"1\",\"VNo\":\"JV-1\",\"Vtype\":\"JV\",\"referenceNo\":null,\"VDate\":\"2024-01-01\",\"COAID\":\"1020401\",\"Narration\":\"Opening Balance\",\"chequeNo\":null,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"Opening Balance\",\"Debit\":\"2250\",\"Credit\":\"\",\"RevCodde\":\"2010202\",\"subType\":\"1\",\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:26:43\",\"status\":0}', '2024-06-17 22:56:43', 1),
(0, '1', 'journal_vaucher', 'create', 'JV-1', 'acc_vaucher', 'account/accounts/store_journal_voucher', '{\"fyear\":\"1\",\"VNo\":\"JV-1\",\"Vtype\":\"JV\",\"referenceNo\":null,\"VDate\":\"2024-01-01\",\"COAID\":\"5020201\",\"Narration\":\"Opening Balance\",\"chequeNo\":null,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"Opening Balance\",\"Debit\":\"\",\"Credit\":\"10000\",\"RevCodde\":\"2010202\",\"subType\":\"4\",\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:26:43\",\"status\":0}', '2024-06-17 22:56:43', 1),
(0, '1', 'journal_vaucher', 'create', 'JV-1', 'acc_vaucher', 'account/accounts/store_journal_voucher', '{\"fyear\":\"1\",\"VNo\":\"JV-1\",\"Vtype\":\"JV\",\"referenceNo\":null,\"VDate\":\"2024-01-01\",\"COAID\":\"2010101\",\"Narration\":\"Opening Balance\",\"chequeNo\":null,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"Opening Balance\",\"Debit\":\"\",\"Credit\":\"142250\",\"RevCodde\":\"2010202\",\"subType\":\"1\",\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:26:43\",\"status\":0}', '2024-06-17 22:56:43', 1),
(0, '1', 'journal_vaucher', 'update', 'JV-1', 'acc_vaucher', 'account/accounts/update_journal_voucher', '{\"txtVNo\":\"JV-1\",\"isApproved\":\"0\",\"fyear\":\"1\",\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:26:43\",\"finyear\":\"1\",\"dtpDate\":\"2024-01-01\",\"txtRemarks\":\"Opening Balance\",\"cmbCode\":[\"1020801\",\"1020401\",\"5020201\",\"2010101\"],\"subtype\":[\"4\",\"\",\"5\",\"\"],\"isSubtype\":[\"3\",\"1\",\"4\",\"1\"],\"txtComment\":[\"Opening Balance\",\"Opening Balance\",\"Opening Balance\",\"Opening Balance\"],\"txtAmount\":[\"50000.00\",\"2250.00\",\"0.00\",\"0.00\"],\"txtAmountcr\":[\"0.00\",\"0.00\",\"10000.00\",\"142250.00\"],\"cmbDebit\":[\"2010202\",\"2010202\",\"2010202\",\"2010202\"],\"grand_total\":\"52250\",\"grand_total1\":\"152250\",\"save\":\"Update\"}', '2024-06-17 22:57:06', 2),
(0, '1', 'journal_vaucher', 'update', 'JV-1', 'acc_vaucher', 'account/accounts/update_journal_voucher', '{\"txtVNo\":\"JV-1\",\"isApproved\":\"0\",\"fyear\":\"1\",\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:26:43\",\"finyear\":\"1\",\"dtpDate\":\"2024-01-01\",\"txtRemarks\":\"Opening Balance\",\"cmbCode\":[\"1020801\",\"1020401\",\"5020201\",\"2010101\"],\"subtype\":[\"4\",\"\",\"5\",\"\"],\"isSubtype\":[\"3\",\"1\",\"4\",\"1\"],\"txtComment\":[\"Opening Balance\",\"Opening Balance\",\"Opening Balance\",\"Opening Balance\"],\"txtAmount\":[\"50000.00\",\"2250.00\",\"0.00\",\"0.00\"],\"txtAmountcr\":[\"0.00\",\"0.00\",\"10000.00\",\"142250.00\"],\"cmbDebit\":[\"2010202\",\"2010202\",\"2010202\",\"2010202\"],\"grand_total\":\"52250\",\"grand_total1\":\"152250\",\"save\":\"Update\"}', '2024-06-17 22:57:06', 2),
(0, '1', 'journal_vaucher', 'update', 'JV-1', 'acc_vaucher', 'account/accounts/update_journal_voucher', '{\"txtVNo\":\"JV-1\",\"isApproved\":\"0\",\"fyear\":\"1\",\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:26:43\",\"finyear\":\"1\",\"dtpDate\":\"2024-01-01\",\"txtRemarks\":\"Opening Balance\",\"cmbCode\":[\"1020801\",\"1020401\",\"5020201\",\"2010101\"],\"subtype\":[\"4\",\"\",\"5\",\"\"],\"isSubtype\":[\"3\",\"1\",\"4\",\"1\"],\"txtComment\":[\"Opening Balance\",\"Opening Balance\",\"Opening Balance\",\"Opening Balance\"],\"txtAmount\":[\"50000.00\",\"2250.00\",\"0.00\",\"0.00\"],\"txtAmountcr\":[\"0.00\",\"0.00\",\"10000.00\",\"142250.00\"],\"cmbDebit\":[\"2010202\",\"2010202\",\"2010202\",\"2010202\"],\"grand_total\":\"52250\",\"grand_total1\":\"152250\",\"save\":\"Update\"}', '2024-06-17 22:57:06', 2),
(0, '1', 'journal_vaucher', 'update', 'JV-1', 'acc_vaucher', 'account/accounts/update_journal_voucher', '{\"txtVNo\":\"JV-1\",\"isApproved\":\"0\",\"fyear\":\"1\",\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:26:43\",\"finyear\":\"1\",\"dtpDate\":\"2024-01-01\",\"txtRemarks\":\"Opening Balance\",\"cmbCode\":[\"1020801\",\"1020401\",\"5020201\",\"2010101\"],\"subtype\":[\"4\",\"\",\"5\",\"\"],\"isSubtype\":[\"3\",\"1\",\"4\",\"1\"],\"txtComment\":[\"Opening Balance\",\"Opening Balance\",\"Opening Balance\",\"Opening Balance\"],\"txtAmount\":[\"50000.00\",\"2250.00\",\"0.00\",\"0.00\"],\"txtAmountcr\":[\"0.00\",\"0.00\",\"10000.00\",\"142250.00\"],\"cmbDebit\":[\"2010202\",\"2010202\",\"2010202\",\"2010202\"],\"grand_total\":\"52250\",\"grand_total1\":\"152250\",\"save\":\"Update\"}', '2024-06-17 22:57:06', 2),
(0, '1', 'approved_vaucher_transation', 'create', '5', 'acc_transaction', 'account/accounts/isactive/JV-1/active', '{\"vid\":\"9\",\"fyear\":\"1\",\"VNo\":\"JV-1\",\"Vtype\":\"JV\",\"referenceNo\":\"3\",\"VDate\":\"2024-01-01\",\"COAID\":\"1020801\",\"Narration\":\"Opening Balance\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Opening Balance\",\"Debit\":\"50000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"2010202\",\"subType\":\"3\",\"subCode\":\"4\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:27:20\"}', '2024-06-17 22:57:20', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '6', 'acc_transaction', 'account/accounts/isactive/JV-1/active', '{\"vid\":\"9\",\"fyear\":\"1\",\"VNo\":\"JV-1\",\"Vtype\":\"JV\",\"referenceNo\":\"3\",\"VDate\":\"2024-01-01\",\"COAID\":\"2010202\",\"Narration\":\"Opening Balance\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Opening Balance\",\"Debit\":\"0.00\",\"Credit\":\"50000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020801\",\"subType\":\"3\",\"subCode\":\"4\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:27:20\"}', '2024-06-17 22:57:20', 1),
(0, '1', 'approved_vaucher_transation', 'create', '7', 'acc_transaction', 'account/accounts/isactive/JV-1/active', '{\"vid\":\"10\",\"fyear\":\"1\",\"VNo\":\"JV-1\",\"Vtype\":\"JV\",\"referenceNo\":null,\"VDate\":\"2024-01-01\",\"COAID\":\"1020401\",\"Narration\":\"Opening Balance\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Opening Balance\",\"Debit\":\"2250.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"2010202\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:27:20\"}', '2024-06-17 22:57:20', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '8', 'acc_transaction', 'account/accounts/isactive/JV-1/active', '{\"vid\":\"10\",\"fyear\":\"1\",\"VNo\":\"JV-1\",\"Vtype\":\"JV\",\"referenceNo\":null,\"VDate\":\"2024-01-01\",\"COAID\":\"2010202\",\"Narration\":\"Opening Balance\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Opening Balance\",\"Debit\":\"0.00\",\"Credit\":\"2250.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:27:20\"}', '2024-06-17 22:57:20', 1),
(0, '1', 'approved_vaucher_transation', 'create', '9', 'acc_transaction', 'account/accounts/isactive/JV-1/active', '{\"vid\":\"11\",\"fyear\":\"1\",\"VNo\":\"JV-1\",\"Vtype\":\"JV\",\"referenceNo\":\"3\",\"VDate\":\"2024-01-01\",\"COAID\":\"5020201\",\"Narration\":\"Opening Balance\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Opening Balance\",\"Debit\":\"0.00\",\"Credit\":\"10000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"2010202\",\"subType\":\"4\",\"subCode\":\"5\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:27:20\"}', '2024-06-17 22:57:20', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '10', 'acc_transaction', 'account/accounts/isactive/JV-1/active', '{\"vid\":\"11\",\"fyear\":\"1\",\"VNo\":\"JV-1\",\"Vtype\":\"JV\",\"referenceNo\":\"3\",\"VDate\":\"2024-01-01\",\"COAID\":\"2010202\",\"Narration\":\"Opening Balance\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Opening Balance\",\"Debit\":\"10000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020201\",\"subType\":\"4\",\"subCode\":\"5\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:27:20\"}', '2024-06-17 22:57:20', 1),
(0, '1', 'approved_vaucher_transation', 'create', '11', 'acc_transaction', 'account/accounts/isactive/JV-1/active', '{\"vid\":\"12\",\"fyear\":\"1\",\"VNo\":\"JV-1\",\"Vtype\":\"JV\",\"referenceNo\":null,\"VDate\":\"2024-01-01\",\"COAID\":\"2010101\",\"Narration\":\"Opening Balance\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Opening Balance\",\"Debit\":\"0.00\",\"Credit\":\"142250.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"2010202\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:27:20\"}', '2024-06-17 22:57:20', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '12', 'acc_transaction', 'account/accounts/isactive/JV-1/active', '{\"vid\":\"12\",\"fyear\":\"1\",\"VNo\":\"JV-1\",\"Vtype\":\"JV\",\"referenceNo\":null,\"VDate\":\"2024-01-01\",\"COAID\":\"2010202\",\"Narration\":\"Opening Balance\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Opening Balance\",\"Debit\":\"142250.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"2010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 17:27:20\"}', '2024-06-17 22:57:20', 1),
(0, '1', 'approved_vaucher_transation', 'create', '13', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"13\",\"fyear\":\"1\",\"VNo\":\"DV-1\",\"Vtype\":\"DV\",\"referenceNo\":\"1\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"4500.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:04:04\"}', '2024-06-17 23:04:04', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '14', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"13\",\"fyear\":\"1\",\"VNo\":\"DV-1\",\"Vtype\":\"DV\",\"referenceNo\":\"1\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020101\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"4500.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:04:04\"}', '2024-06-17 23:04:04', 1),
(0, '1', 'approved_vaucher_transation', 'create', '15', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"14\",\"fyear\":\"1\",\"VNo\":\"DV-2\",\"Vtype\":\"DV\",\"referenceNo\":\"2\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"4500.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020501\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:05:52\"}', '2024-06-17 23:05:52', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '16', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"14\",\"fyear\":\"1\",\"VNo\":\"DV-2\",\"Vtype\":\"DV\",\"referenceNo\":\"2\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020501\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"4500.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:05:52\"}', '2024-06-17 23:05:52', 1),
(0, '1', 'approved_vaucher_transation', 'create', '17', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"15\",\"fyear\":\"1\",\"VNo\":\"JV-2\",\"Vtype\":\"JV\",\"referenceNo\":\"3\",\"VDate\":\"2024-06-17\",\"COAID\":\"5020201\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"2250.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"4\",\"subCode\":\"5\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:06:45\"}', '2024-06-17 23:06:45', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '18', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"15\",\"fyear\":\"1\",\"VNo\":\"JV-2\",\"Vtype\":\"JV\",\"referenceNo\":\"3\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"2250.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020201\",\"subType\":\"4\",\"subCode\":\"5\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:06:45\"}', '2024-06-17 23:06:45', 1),
(0, '1', 'approved_vaucher_transation', 'create', '19', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"16\",\"fyear\":\"1\",\"VNo\":\"DV-3\",\"Vtype\":\"DV\",\"referenceNo\":\"4\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"810.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:09:44\"}', '2024-06-17 23:09:44', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '20', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"16\",\"fyear\":\"1\",\"VNo\":\"DV-3\",\"Vtype\":\"DV\",\"referenceNo\":\"4\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020101\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"810.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:09:44\"}', '2024-06-17 23:09:44', 1),
(0, '1', 'approved_vaucher_transation', 'create', '21', 'acc_transaction', 'return/returns/return_suppliers', '{\"vid\":\"17\",\"fyear\":\"1\",\"VNo\":\"JV-3\",\"Vtype\":\"JV\",\"referenceNo\":\"3231159768\",\"VDate\":\"2024-06-17\",\"COAID\":\"5020201\",\"Narration\":\"Purchase Return Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Return Voucher for supplier\",\"Debit\":\"900.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"4\",\"subCode\":\"1\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:11:59\"}', '2024-06-17 23:11:59', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '22', 'acc_transaction', 'return/returns/return_suppliers', '{\"vid\":\"17\",\"fyear\":\"1\",\"VNo\":\"JV-3\",\"Vtype\":\"JV\",\"referenceNo\":\"3231159768\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Return Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Return Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"900.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020201\",\"subType\":\"4\",\"subCode\":\"1\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:11:59\"}', '2024-06-17 23:11:59', 1),
(0, '1', 'approved_vaucher_transation', 'create', '23', 'acc_transaction', 'return/returns/return_suppliers', '{\"vid\":\"18\",\"fyear\":\"1\",\"VNo\":\"DV-4\",\"Vtype\":\"DV\",\"referenceNo\":\"3231326182\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Return Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Return Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"2250.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:13:26\"}', '2024-06-17 23:13:26', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '24', 'acc_transaction', 'return/returns/return_suppliers', '{\"vid\":\"18\",\"fyear\":\"1\",\"VNo\":\"DV-4\",\"Vtype\":\"DV\",\"referenceNo\":\"3231326182\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020101\",\"Narration\":\"Purchase Return Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Return Voucher for supplier\",\"Debit\":\"2250.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:13:26\"}', '2024-06-17 23:13:26', 1),
(0, '1', 'approved_vaucher_transation', 'create', '25', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"19\",\"fyear\":\"1\",\"VNo\":\"CV-3\",\"Vtype\":\"CV\",\"referenceNo\":\"1000\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020101\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"3000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:16:19\"}', '2024-06-17 23:16:19', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '26', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"19\",\"fyear\":\"1\",\"VNo\":\"CV-3\",\"Vtype\":\"CV\",\"referenceNo\":\"1000\",\"VDate\":\"2024-06-17\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"3000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:16:19\"}', '2024-06-17 23:16:19', 1),
(0, '1', 'approved_vaucher_transation', 'create', '27', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"20\",\"fyear\":\"1\",\"VNo\":\"JV-4\",\"Vtype\":\"JV\",\"referenceNo\":\"1000\",\"VDate\":\"2024-06-17\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"2250.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:16:19\"}', '2024-06-17 23:16:19', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '28', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"20\",\"fyear\":\"1\",\"VNo\":\"JV-4\",\"Vtype\":\"JV\",\"referenceNo\":\"1000\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"2250.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:16:19\"}', '2024-06-17 23:16:19', 1);
INSERT INTO `activity_logs` (`activity_id`, `user_id`, `type`, `action`, `action_id`, `table_name`, `slug`, `form_data`, `create_date`, `status`) VALUES
(0, '1', 'approved_vaucher_transation', 'create', '29', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"21\",\"fyear\":\"1\",\"VNo\":\"JV-5\",\"Vtype\":\"JV\",\"referenceNo\":\"1000\",\"VDate\":\"2024-06-17\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:16:19\"}', '2024-06-17 23:16:19', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '30', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"21\",\"fyear\":\"1\",\"VNo\":\"JV-5\",\"Vtype\":\"JV\",\"referenceNo\":\"1000\",\"VDate\":\"2024-06-17\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:16:19\"}', '2024-06-17 23:16:19', 1),
(0, '1', 'approved_vaucher_transation', 'create', '31', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"22\",\"fyear\":\"1\",\"VNo\":\"CV-4\",\"Vtype\":\"CV\",\"referenceNo\":\"1001\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020501\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"3000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:17:50\"}', '2024-06-17 23:17:50', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '32', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"22\",\"fyear\":\"1\",\"VNo\":\"CV-4\",\"Vtype\":\"CV\",\"referenceNo\":\"1001\",\"VDate\":\"2024-06-17\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"3000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020501\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:17:50\"}', '2024-06-17 23:17:50', 1),
(0, '1', 'approved_vaucher_transation', 'create', '33', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"23\",\"fyear\":\"1\",\"VNo\":\"JV-6\",\"Vtype\":\"JV\",\"referenceNo\":\"1001\",\"VDate\":\"2024-06-17\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"2250.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:17:50\"}', '2024-06-17 23:17:50', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '34', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"23\",\"fyear\":\"1\",\"VNo\":\"JV-6\",\"Vtype\":\"JV\",\"referenceNo\":\"1001\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"2250.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:17:50\"}', '2024-06-17 23:17:50', 1),
(0, '1', 'approved_vaucher_transation', 'create', '35', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"24\",\"fyear\":\"1\",\"VNo\":\"JV-7\",\"Vtype\":\"JV\",\"referenceNo\":\"1001\",\"VDate\":\"2024-06-17\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:17:50\"}', '2024-06-17 23:17:50', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '36', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"24\",\"fyear\":\"1\",\"VNo\":\"JV-7\",\"Vtype\":\"JV\",\"referenceNo\":\"1001\",\"VDate\":\"2024-06-17\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:17:50\"}', '2024-06-17 23:17:50', 1),
(0, '1', 'approved_vaucher_transation', 'create', '37', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"25\",\"fyear\":\"1\",\"VNo\":\"JV-8\",\"Vtype\":\"JV\",\"referenceNo\":\"1002\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020801\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"3000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"3\",\"subCode\":\"4\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:19:06\"}', '2024-06-17 23:19:06', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '38', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"25\",\"fyear\":\"1\",\"VNo\":\"JV-8\",\"Vtype\":\"JV\",\"referenceNo\":\"1002\",\"VDate\":\"2024-06-17\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"3000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020801\",\"subType\":\"3\",\"subCode\":\"4\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:19:06\"}', '2024-06-17 23:19:06', 1),
(0, '1', 'approved_vaucher_transation', 'create', '39', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"26\",\"fyear\":\"1\",\"VNo\":\"JV-9\",\"Vtype\":\"JV\",\"referenceNo\":\"1002\",\"VDate\":\"2024-06-17\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"2250.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:19:06\"}', '2024-06-17 23:19:06', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '40', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"26\",\"fyear\":\"1\",\"VNo\":\"JV-9\",\"Vtype\":\"JV\",\"referenceNo\":\"1002\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"2250.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:19:06\"}', '2024-06-17 23:19:06', 1),
(0, '1', 'approved_vaucher_transation', 'create', '41', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"27\",\"fyear\":\"1\",\"VNo\":\"JV-10\",\"Vtype\":\"JV\",\"referenceNo\":\"1002\",\"VDate\":\"2024-06-17\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:19:06\"}', '2024-06-17 23:19:06', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '42', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"27\",\"fyear\":\"1\",\"VNo\":\"JV-10\",\"Vtype\":\"JV\",\"referenceNo\":\"1002\",\"VDate\":\"2024-06-17\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:19:06\"}', '2024-06-17 23:19:06', 1),
(0, '1', 'approved_vaucher_transation', 'create', '43', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"28\",\"fyear\":\"1\",\"VNo\":\"CV-5\",\"Vtype\":\"CV\",\"referenceNo\":\"1003\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020101\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"540.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:20:48\"}', '2024-06-17 23:20:48', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '44', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"28\",\"fyear\":\"1\",\"VNo\":\"CV-5\",\"Vtype\":\"CV\",\"referenceNo\":\"1003\",\"VDate\":\"2024-06-17\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"540.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:20:48\"}', '2024-06-17 23:20:48', 1),
(0, '1', 'approved_vaucher_transation', 'create', '45', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"29\",\"fyear\":\"1\",\"VNo\":\"JV-11\",\"Vtype\":\"JV\",\"referenceNo\":\"1003\",\"VDate\":\"2024-06-17\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:20:48\"}', '2024-06-17 23:20:48', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '46', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"29\",\"fyear\":\"1\",\"VNo\":\"JV-11\",\"Vtype\":\"JV\",\"referenceNo\":\"1003\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:20:48\"}', '2024-06-17 23:20:48', 1),
(0, '1', 'approved_vaucher_transation', 'create', '47', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"30\",\"fyear\":\"1\",\"VNo\":\"JV-12\",\"Vtype\":\"JV\",\"referenceNo\":\"1003\",\"VDate\":\"2024-06-17\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:20:48\"}', '2024-06-17 23:20:48', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '48', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"30\",\"fyear\":\"1\",\"VNo\":\"JV-12\",\"Vtype\":\"JV\",\"referenceNo\":\"1003\",\"VDate\":\"2024-06-17\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:20:48\"}', '2024-06-17 23:20:48', 1),
(0, '1', 'approved_vaucher_transation', 'create', '49', 'acc_transaction', 'return/returns/return_invoice', '{\"vid\":\"31\",\"fyear\":\"1\",\"VNo\":\"JV-13\",\"Vtype\":\"JV\",\"referenceNo\":\"3232231770\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020801\",\"Narration\":\"Sales Return Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Return Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"1800.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"3\",\"subCode\":\"4\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:22:31\"}', '2024-06-17 23:22:31', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '50', 'acc_transaction', 'return/returns/return_invoice', '{\"vid\":\"31\",\"fyear\":\"1\",\"VNo\":\"JV-13\",\"Vtype\":\"JV\",\"referenceNo\":\"3232231770\",\"VDate\":\"2024-06-17\",\"COAID\":\"3010301\",\"Narration\":\"Sales Return Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Return Voucher for customer\",\"Debit\":\"1800.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020801\",\"subType\":\"3\",\"subCode\":\"4\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:22:31\"}', '2024-06-17 23:22:31', 1),
(0, '1', 'approved_vaucher_transation', 'create', '51', 'acc_transaction', 'return/returns/return_invoice', '{\"vid\":\"32\",\"fyear\":\"1\",\"VNo\":\"JV-14\",\"Vtype\":\"JV\",\"referenceNo\":\"3232231770\",\"VDate\":\"2024-06-17\",\"COAID\":\"4010101\",\"Narration\":\"Sales Return Voucher for Cost of Goods\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Return cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"1350.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:22:31\"}', '2024-06-17 23:22:31', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '52', 'acc_transaction', 'return/returns/return_invoice', '{\"vid\":\"32\",\"fyear\":\"1\",\"VNo\":\"JV-14\",\"Vtype\":\"JV\",\"referenceNo\":\"3232231770\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020401\",\"Narration\":\"Sales Return Voucher for Cost of Goods\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Return cost of goods Voucher for customer\",\"Debit\":\"1350.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:22:31\"}', '2024-06-17 23:22:31', 1),
(0, '1', 'approved_vaucher_transation', 'create', '53', 'acc_transaction', 'account/accounts/create_customer_receive', '{\"vid\":\"33\",\"fyear\":\"1\",\"VNo\":\"CV-6\",\"Vtype\":\"CV\",\"referenceNo\":\"1002\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020801\",\"Narration\":\"Sales Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Due Voucher for Customer\",\"Debit\":\"0.00\",\"Credit\":\"1200.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"3\",\"subCode\":\"4\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:28:01\"}', '2024-06-17 23:28:01', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '54', 'acc_transaction', 'account/accounts/create_customer_receive', '{\"vid\":\"33\",\"fyear\":\"1\",\"VNo\":\"CV-6\",\"Vtype\":\"CV\",\"referenceNo\":\"1002\",\"VDate\":\"2024-06-17\",\"COAID\":\"1020101\",\"Narration\":\"Sales Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Due Voucher for Customer\",\"Debit\":\"1200.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020801\",\"subType\":\"3\",\"subCode\":\"4\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 23:28:01\"}', '2024-06-17 23:28:01', 1),
(0, '1', 'coa_account', 'create', '238', 'acc_coa', 'account/accounts/insert_coa', '{\"HeadCode\":\"30201\",\"Pheadcode\":\"302\",\"HeadName\":\"House Rent\",\"PHeadName\":\"Indirect Income\",\"HeadLevel\":\"3\",\"IsActive\":\"1\",\"isStock\":0,\"isSubType\":0,\"DepreciationRate\":0,\"HeadType\":\"I\",\"IsBudget\":0,\"isCashNature\":0,\"isBankNature\":0,\"isFixedAssetSch\":0,\"assetCode\":null,\"depCode\":null,\"subType\":1,\"noteNo\":null,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 18:02:11\"}', '2024-06-17 23:32:11', 1),
(0, '1', 'coa_account', 'update', '30201', 'acc_coa', 'account/accounts/insert_coa', '{\"HeadCode\":\"30201\",\"Pheadcode\":\"302\",\"HeadName\":\"House Rent Income\",\"PHeadName\":\"Indirect Income\",\"HeadLevel\":\"3\",\"IsActive\":\"1\",\"isStock\":0,\"isSubType\":0,\"DepreciationRate\":0,\"HeadType\":\"I\",\"IsBudget\":0,\"isCashNature\":0,\"isBankNature\":0,\"isFixedAssetSch\":0,\"assetCode\":null,\"depCode\":null,\"subType\":1,\"noteNo\":null,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 18:02:30\"}', '2024-06-17 23:32:30', 2),
(0, '1', 'coa_account', 'update', '30201', 'acc_coa', 'account/accounts/insert_coa', '{\"HeadCode\":\"30201\",\"Pheadcode\":\"302\",\"HeadName\":\"Other Income\",\"PHeadName\":\"Indirect Income\",\"HeadLevel\":\"3\",\"IsActive\":\"1\",\"isStock\":0,\"isSubType\":0,\"DepreciationRate\":0,\"HeadType\":\"I\",\"IsBudget\":0,\"isCashNature\":0,\"isBankNature\":0,\"isFixedAssetSch\":0,\"assetCode\":null,\"depCode\":null,\"subType\":1,\"noteNo\":null,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 18:03:21\"}', '2024-06-17 23:33:21', 2),
(0, '1', 'coa_account', 'create', '239', 'acc_coa', 'account/accounts/insert_coa', '{\"HeadCode\":\"3020101\",\"Pheadcode\":\"30201\",\"HeadName\":\"House Rent Income\",\"PHeadName\":\"Other Income\",\"HeadLevel\":\"4\",\"IsActive\":\"1\",\"isStock\":0,\"isSubType\":0,\"DepreciationRate\":0,\"HeadType\":\"I\",\"IsBudget\":0,\"isCashNature\":0,\"isBankNature\":0,\"isFixedAssetSch\":0,\"assetCode\":null,\"depCode\":null,\"subType\":1,\"noteNo\":null,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 18:03:34\"}', '2024-06-17 23:33:34', 1),
(0, '1', 'credit_vaucher', 'create', 'CV-7', 'acc_vaucher', 'account/accounts/store_credit_voucher', '{\"fyear\":\"1\",\"VNo\":\"CV-7\",\"Vtype\":\"CV\",\"referenceNo\":null,\"VDate\":\"2024-06-17\",\"COAID\":\"3020101\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":\"\",\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":0,\"Credit\":\"5000\",\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 18:04:12\",\"status\":0}', '2024-06-17 23:34:12', 1),
(0, '1', 'debit_vaucher', 'create', 'DV-5', 'acc_vaucher', 'account/accounts/store_debit_voucher', '{\"fyear\":\"1\",\"VNo\":\"DV-5\",\"Vtype\":\"DV\",\"referenceNo\":null,\"VDate\":\"2024-06-17\",\"COAID\":\"4021001\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":\"\",\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":\"3000\",\"Credit\":0,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 18:05:14\",\"status\":0}', '2024-06-17 23:35:14', 1),
(0, '1', 'contra_vaucher', 'create', 'CT-1', 'acc_vaucher', 'account/accounts/store_contra_voucher', '{\"fyear\":\"1\",\"VNo\":\"CT-1\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-06-17\",\"COAID\":\"1020501\",\"Narration\":\"\",\"chequeNo\":null,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":\"2500\",\"Credit\":\"\",\"RevCodde\":\"1020101\",\"subType\":1,\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 18:05:53\",\"status\":0}', '2024-06-17 23:35:53', 1),
(0, '1', 'approved_vaucher_transation', 'create', '55', 'acc_transaction', 'account/accounts/bulk_voucher_approve', '{\"vid\":\"35\",\"fyear\":\"1\",\"VNo\":\"DV-5\",\"Vtype\":\"DV\",\"referenceNo\":null,\"VDate\":\"2024-06-17\",\"COAID\":\"4021001\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":\"0000-00-00\",\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"3000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 18:06:17\"}', '2024-06-17 23:36:17', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '56', 'acc_transaction', 'account/accounts/bulk_voucher_approve', '{\"vid\":\"35\",\"fyear\":\"1\",\"VNo\":\"DV-5\",\"Vtype\":\"DV\",\"referenceNo\":null,\"VDate\":\"2024-06-17\",\"COAID\":\"1020101\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":\"0000-00-00\",\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"0.00\",\"Credit\":\"3000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021001\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 18:06:17\"}', '2024-06-17 23:36:17', 1),
(0, '1', 'approved_vaucher_transation', 'create', '57', 'acc_transaction', 'account/accounts/bulk_voucher_approve', '{\"vid\":\"34\",\"fyear\":\"1\",\"VNo\":\"CV-7\",\"Vtype\":\"CV\",\"referenceNo\":null,\"VDate\":\"2024-06-17\",\"COAID\":\"3020101\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":\"0000-00-00\",\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"0.00\",\"Credit\":\"5000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 18:06:17\"}', '2024-06-17 23:36:17', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '58', 'acc_transaction', 'account/accounts/bulk_voucher_approve', '{\"vid\":\"34\",\"fyear\":\"1\",\"VNo\":\"CV-7\",\"Vtype\":\"CV\",\"referenceNo\":null,\"VDate\":\"2024-06-17\",\"COAID\":\"1020101\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":\"0000-00-00\",\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"5000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 18:06:17\"}', '2024-06-17 23:36:17', 1),
(0, '1', 'approved_vaucher_transation', 'create', '59', 'acc_transaction', 'account/accounts/bulk_voucher_approve', '{\"vid\":\"36\",\"fyear\":\"1\",\"VNo\":\"CT-1\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-06-17\",\"COAID\":\"1020501\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"2500.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 18:06:17\"}', '2024-06-17 23:36:17', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '60', 'acc_transaction', 'account/accounts/bulk_voucher_approve', '{\"vid\":\"36\",\"fyear\":\"1\",\"VNo\":\"CT-1\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-06-17\",\"COAID\":\"1020101\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"0.00\",\"Credit\":\"2500.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020501\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-17 18:06:17\"}', '2024-06-17 23:36:17', 1),
(0, '1', 'coa_account', 'create', '240', 'acc_coa', 'account/accounts/insert_coa', '{\"HeadCode\":\"1020103\",\"Pheadcode\":\"10201\",\"HeadName\":\"3rd Party Cheque\",\"PHeadName\":\"Cash\",\"HeadLevel\":\"4\",\"IsActive\":\"1\",\"isStock\":0,\"isSubType\":0,\"DepreciationRate\":0,\"HeadType\":\"A\",\"IsBudget\":0,\"isCashNature\":\"1\",\"isBankNature\":0,\"isFixedAssetSch\":0,\"assetCode\":null,\"depCode\":null,\"subType\":1,\"noteNo\":null,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-18 14:31:19\"}', '2024-06-18 20:01:19', 1),
(0, '1', 'approved_vaucher_transation', 'create', '61', 'acc_transaction', 'account/accounts/create_supplier_payment', '{\"vid\":\"37\",\"fyear\":\"1\",\"VNo\":\"DV-6\",\"Vtype\":\"DV\",\"referenceNo\":\"3\",\"VDate\":\"2024-06-20\",\"COAID\":\"5020201\",\"Narration\":\"Purchase Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Due Voucher for supplier\",\"Debit\":\"1350.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"4\",\"subCode\":\"5\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-20 18:15:47\"}', '2024-06-20 18:15:47', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '62', 'acc_transaction', 'account/accounts/create_supplier_payment', '{\"vid\":\"37\",\"fyear\":\"1\",\"VNo\":\"DV-6\",\"Vtype\":\"DV\",\"referenceNo\":\"3\",\"VDate\":\"2024-06-20\",\"COAID\":\"1020101\",\"Narration\":\"Purchase Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Due Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"1350.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020201\",\"subType\":\"4\",\"subCode\":\"5\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-20 18:15:47\"}', '2024-06-20 18:15:47', 1),
(0, '1', 'approved_vaucher_transation', 'create', '63', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"38\",\"fyear\":\"1\",\"VNo\":\"CV-8\",\"Vtype\":\"CV\",\"referenceNo\":\"1004\",\"VDate\":\"2024-06-21\",\"COAID\":\"1020103\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"600.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:25:00\"}', '2024-06-21 23:25:00', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '64', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"38\",\"fyear\":\"1\",\"VNo\":\"CV-8\",\"Vtype\":\"CV\",\"referenceNo\":\"1004\",\"VDate\":\"2024-06-21\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"600.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:25:00\"}', '2024-06-21 23:25:00', 1),
(0, '1', 'approved_vaucher_transation', 'create', '65', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"39\",\"fyear\":\"1\",\"VNo\":\"JV-15\",\"Vtype\":\"JV\",\"referenceNo\":\"1004\",\"VDate\":\"2024-06-21\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:25:00\"}', '2024-06-21 23:25:00', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '66', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"39\",\"fyear\":\"1\",\"VNo\":\"JV-15\",\"Vtype\":\"JV\",\"referenceNo\":\"1004\",\"VDate\":\"2024-06-21\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:25:00\"}', '2024-06-21 23:25:00', 1),
(0, '1', 'approved_vaucher_transation', 'create', '67', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"40\",\"fyear\":\"1\",\"VNo\":\"JV-16\",\"Vtype\":\"JV\",\"referenceNo\":\"1004\",\"VDate\":\"2024-06-21\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:25:00\"}', '2024-06-21 23:25:00', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '68', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"40\",\"fyear\":\"1\",\"VNo\":\"JV-16\",\"Vtype\":\"JV\",\"referenceNo\":\"1004\",\"VDate\":\"2024-06-21\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:25:00\"}', '2024-06-21 23:25:00', 1),
(0, '1', 'approved_vaucher_transation', 'create', '69', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"41\",\"fyear\":\"1\",\"VNo\":\"CV-9\",\"Vtype\":\"CV\",\"referenceNo\":\"1005\",\"VDate\":\"2024-06-21\",\"COAID\":\"1020103\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"600.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:29:08\"}', '2024-06-21 23:29:08', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '70', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"41\",\"fyear\":\"1\",\"VNo\":\"CV-9\",\"Vtype\":\"CV\",\"referenceNo\":\"1005\",\"VDate\":\"2024-06-21\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"600.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:29:08\"}', '2024-06-21 23:29:08', 1),
(0, '1', 'approved_vaucher_transation', 'create', '71', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"42\",\"fyear\":\"1\",\"VNo\":\"JV-17\",\"Vtype\":\"JV\",\"referenceNo\":\"1005\",\"VDate\":\"2024-06-21\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:29:08\"}', '2024-06-21 23:29:08', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '72', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"42\",\"fyear\":\"1\",\"VNo\":\"JV-17\",\"Vtype\":\"JV\",\"referenceNo\":\"1005\",\"VDate\":\"2024-06-21\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:29:08\"}', '2024-06-21 23:29:08', 1),
(0, '1', 'approved_vaucher_transation', 'create', '73', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"43\",\"fyear\":\"1\",\"VNo\":\"JV-18\",\"Vtype\":\"JV\",\"referenceNo\":\"1005\",\"VDate\":\"2024-06-21\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:29:08\"}', '2024-06-21 23:29:08', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '74', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"43\",\"fyear\":\"1\",\"VNo\":\"JV-18\",\"Vtype\":\"JV\",\"referenceNo\":\"1005\",\"VDate\":\"2024-06-21\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:29:08\"}', '2024-06-21 23:29:08', 1),
(0, '1', 'approved_vaucher_transation', 'create', '75', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"44\",\"fyear\":\"1\",\"VNo\":\"CV-10\",\"Vtype\":\"CV\",\"referenceNo\":\"1006\",\"VDate\":\"2024-06-21\",\"COAID\":\"1020103\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"600.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:31:16\"}', '2024-06-21 23:31:16', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '76', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"44\",\"fyear\":\"1\",\"VNo\":\"CV-10\",\"Vtype\":\"CV\",\"referenceNo\":\"1006\",\"VDate\":\"2024-06-21\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"600.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:31:16\"}', '2024-06-21 23:31:16', 1),
(0, '1', 'approved_vaucher_transation', 'create', '77', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"45\",\"fyear\":\"1\",\"VNo\":\"JV-19\",\"Vtype\":\"JV\",\"referenceNo\":\"1006\",\"VDate\":\"2024-06-21\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:31:16\"}', '2024-06-21 23:31:16', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '78', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"45\",\"fyear\":\"1\",\"VNo\":\"JV-19\",\"Vtype\":\"JV\",\"referenceNo\":\"1006\",\"VDate\":\"2024-06-21\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:31:16\"}', '2024-06-21 23:31:16', 1),
(0, '1', 'approved_vaucher_transation', 'create', '79', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"46\",\"fyear\":\"1\",\"VNo\":\"JV-20\",\"Vtype\":\"JV\",\"referenceNo\":\"1006\",\"VDate\":\"2024-06-21\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:31:17\"}', '2024-06-21 23:31:17', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '80', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"46\",\"fyear\":\"1\",\"VNo\":\"JV-20\",\"Vtype\":\"JV\",\"referenceNo\":\"1006\",\"VDate\":\"2024-06-21\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-21 23:31:17\"}', '2024-06-21 23:31:17', 1),
(0, '1', 'contra_vaucher', 'create', 'CT-2', 'acc_vaucher', 'account/accounts/store_contra_voucher', '{\"fyear\":\"1\",\"VNo\":\"CT-2\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-06-22\",\"COAID\":\"1020501\",\"Narration\":\"\",\"chequeNo\":null,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":\"1000\",\"Credit\":\"\",\"RevCodde\":\"1020101\",\"subType\":1,\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-22 16:20:16\",\"status\":0}', '2024-06-22 21:50:16', 1),
(0, '1', 'contra_vaucher', 'create', 'CT-3', 'acc_vaucher', 'account/accounts/store_contra_voucher', '{\"fyear\":\"1\",\"VNo\":\"CT-3\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-06-23\",\"COAID\":\"1020502\",\"Narration\":\"\",\"chequeNo\":null,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":\"600\",\"Credit\":0,\"RevCodde\":\"1020103\",\"subType\":1,\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 07:09:50\",\"status\":0}', '2024-06-23 12:39:50', 1),
(0, '1', 'approved_vaucher_transation', 'create', '81', 'acc_transaction', 'account/accounts/isactive/CT-3/active', '{\"vid\":\"48\",\"fyear\":\"1\",\"VNo\":\"CT-3\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-06-23\",\"COAID\":\"1020502\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"600.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 07:10:05\"}', '2024-06-23 12:40:05', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '82', 'acc_transaction', 'account/accounts/isactive/CT-3/active', '{\"vid\":\"48\",\"fyear\":\"1\",\"VNo\":\"CT-3\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-06-23\",\"COAID\":\"1020103\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"0.00\",\"Credit\":\"600.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020502\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 07:10:05\"}', '2024-06-23 12:40:05', 1),
(0, '1', 'approved_vaucher_transation', 'create', '83', 'acc_transaction', 'account/accounts/isactive/CT-2/active', '{\"vid\":\"47\",\"fyear\":\"1\",\"VNo\":\"CT-2\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-06-22\",\"COAID\":\"1020501\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"1000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 07:10:08\"}', '2024-06-23 12:40:08', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '84', 'acc_transaction', 'account/accounts/isactive/CT-2/active', '{\"vid\":\"47\",\"fyear\":\"1\",\"VNo\":\"CT-2\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-06-22\",\"COAID\":\"1020101\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"0.00\",\"Credit\":\"1000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020501\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 07:10:08\"}', '2024-06-23 12:40:08', 1),
(0, '1', 'approved_vaucher_transation', 'create', '85', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"49\",\"fyear\":\"1\",\"VNo\":\"CV-11\",\"Vtype\":\"CV\",\"referenceNo\":\"1007\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020101\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"100.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:27:40\"}', '2024-06-23 22:27:40', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '86', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"49\",\"fyear\":\"1\",\"VNo\":\"CV-11\",\"Vtype\":\"CV\",\"referenceNo\":\"1007\",\"VDate\":\"2024-06-23\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"100.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:27:40\"}', '2024-06-23 22:27:40', 1),
(0, '1', 'approved_vaucher_transation', 'create', '87', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"50\",\"fyear\":\"1\",\"VNo\":\"CV-12\",\"Vtype\":\"CV\",\"referenceNo\":\"1007\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020103\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"500.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:27:40\"}', '2024-06-23 22:27:40', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '88', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"50\",\"fyear\":\"1\",\"VNo\":\"CV-12\",\"Vtype\":\"CV\",\"referenceNo\":\"1007\",\"VDate\":\"2024-06-23\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"500.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:27:40\"}', '2024-06-23 22:27:40', 1),
(0, '1', 'approved_vaucher_transation', 'create', '89', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"51\",\"fyear\":\"1\",\"VNo\":\"JV-21\",\"Vtype\":\"JV\",\"referenceNo\":\"1007\",\"VDate\":\"2024-06-23\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:27:40\"}', '2024-06-23 22:27:40', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '90', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"51\",\"fyear\":\"1\",\"VNo\":\"JV-21\",\"Vtype\":\"JV\",\"referenceNo\":\"1007\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:27:40\"}', '2024-06-23 22:27:40', 1),
(0, '1', 'approved_vaucher_transation', 'create', '91', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"52\",\"fyear\":\"1\",\"VNo\":\"JV-22\",\"Vtype\":\"JV\",\"referenceNo\":\"1007\",\"VDate\":\"2024-06-23\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:27:40\"}', '2024-06-23 22:27:40', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '92', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"52\",\"fyear\":\"1\",\"VNo\":\"JV-22\",\"Vtype\":\"JV\",\"referenceNo\":\"1007\",\"VDate\":\"2024-06-23\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:27:40\"}', '2024-06-23 22:27:40', 1),
(0, '1', 'approved_vaucher_transation', 'create', '93', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"53\",\"fyear\":\"1\",\"VNo\":\"DV-7\",\"Vtype\":\"DV\",\"referenceNo\":\"5\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"500.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:34:04\"}', '2024-06-23 22:34:04', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '94', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"53\",\"fyear\":\"1\",\"VNo\":\"DV-7\",\"Vtype\":\"DV\",\"referenceNo\":\"5\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020103\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"500.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:34:04\"}', '2024-06-23 22:34:04', 1),
(0, '1', 'approved_vaucher_transation', 'create', '95', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"54\",\"fyear\":\"1\",\"VNo\":\"DV-8\",\"Vtype\":\"DV\",\"referenceNo\":\"5\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"4000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020501\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:34:04\"}', '2024-06-23 22:34:04', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '96', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"54\",\"fyear\":\"1\",\"VNo\":\"DV-8\",\"Vtype\":\"DV\",\"referenceNo\":\"5\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020501\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"4000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:34:04\"}', '2024-06-23 22:34:04', 1),
(0, '1', 'approved_vaucher_transation', 'create', '97', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"55\",\"fyear\":\"1\",\"VNo\":\"CV-13\",\"Vtype\":\"CV\",\"referenceNo\":\"1008\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020103\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"700.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:42:46\"}', '2024-06-23 22:42:46', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '98', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"55\",\"fyear\":\"1\",\"VNo\":\"CV-13\",\"Vtype\":\"CV\",\"referenceNo\":\"1008\",\"VDate\":\"2024-06-23\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"700.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:42:46\"}', '2024-06-23 22:42:46', 1);
INSERT INTO `activity_logs` (`activity_id`, `user_id`, `type`, `action`, `action_id`, `table_name`, `slug`, `form_data`, `create_date`, `status`) VALUES
(0, '1', 'approved_vaucher_transation', 'create', '99', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"56\",\"fyear\":\"1\",\"VNo\":\"JV-23\",\"Vtype\":\"JV\",\"referenceNo\":\"1008\",\"VDate\":\"2024-06-23\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:42:46\"}', '2024-06-23 22:42:46', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '100', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"56\",\"fyear\":\"1\",\"VNo\":\"JV-23\",\"Vtype\":\"JV\",\"referenceNo\":\"1008\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:42:46\"}', '2024-06-23 22:42:46', 1),
(0, '1', 'approved_vaucher_transation', 'create', '101', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"57\",\"fyear\":\"1\",\"VNo\":\"JV-24\",\"Vtype\":\"JV\",\"referenceNo\":\"1008\",\"VDate\":\"2024-06-23\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:42:46\"}', '2024-06-23 22:42:46', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '102', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"57\",\"fyear\":\"1\",\"VNo\":\"JV-24\",\"Vtype\":\"JV\",\"referenceNo\":\"1008\",\"VDate\":\"2024-06-23\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:42:46\"}', '2024-06-23 22:42:46', 1),
(0, '1', 'approved_vaucher_transation', 'create', '103', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"58\",\"fyear\":\"1\",\"VNo\":\"CV-14\",\"Vtype\":\"CV\",\"referenceNo\":\"1009\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020103\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"800.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:46:12\"}', '2024-06-23 22:46:12', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '104', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"58\",\"fyear\":\"1\",\"VNo\":\"CV-14\",\"Vtype\":\"CV\",\"referenceNo\":\"1009\",\"VDate\":\"2024-06-23\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"800.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:46:12\"}', '2024-06-23 22:46:12', 1),
(0, '1', 'approved_vaucher_transation', 'create', '105', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"59\",\"fyear\":\"1\",\"VNo\":\"JV-25\",\"Vtype\":\"JV\",\"referenceNo\":\"1009\",\"VDate\":\"2024-06-23\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:46:12\"}', '2024-06-23 22:46:12', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '106', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"59\",\"fyear\":\"1\",\"VNo\":\"JV-25\",\"Vtype\":\"JV\",\"referenceNo\":\"1009\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:46:12\"}', '2024-06-23 22:46:12', 1),
(0, '1', 'approved_vaucher_transation', 'create', '107', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"60\",\"fyear\":\"1\",\"VNo\":\"JV-26\",\"Vtype\":\"JV\",\"referenceNo\":\"1009\",\"VDate\":\"2024-06-23\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:46:12\"}', '2024-06-23 22:46:12', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '108', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"60\",\"fyear\":\"1\",\"VNo\":\"JV-26\",\"Vtype\":\"JV\",\"referenceNo\":\"1009\",\"VDate\":\"2024-06-23\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:46:12\"}', '2024-06-23 22:46:12', 1),
(0, '1', 'approved_vaucher_transation', 'create', '109', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"61\",\"fyear\":\"1\",\"VNo\":\"CV-15\",\"Vtype\":\"CV\",\"referenceNo\":\"1010\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020103\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"800.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:49:02\"}', '2024-06-23 22:49:02', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '110', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"61\",\"fyear\":\"1\",\"VNo\":\"CV-15\",\"Vtype\":\"CV\",\"referenceNo\":\"1010\",\"VDate\":\"2024-06-23\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"800.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:49:02\"}', '2024-06-23 22:49:02', 1),
(0, '1', 'approved_vaucher_transation', 'create', '111', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"62\",\"fyear\":\"1\",\"VNo\":\"JV-27\",\"Vtype\":\"JV\",\"referenceNo\":\"1010\",\"VDate\":\"2024-06-23\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:49:02\"}', '2024-06-23 22:49:02', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '112', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"62\",\"fyear\":\"1\",\"VNo\":\"JV-27\",\"Vtype\":\"JV\",\"referenceNo\":\"1010\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:49:02\"}', '2024-06-23 22:49:02', 1),
(0, '1', 'approved_vaucher_transation', 'create', '113', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"63\",\"fyear\":\"1\",\"VNo\":\"JV-28\",\"Vtype\":\"JV\",\"referenceNo\":\"1010\",\"VDate\":\"2024-06-23\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:49:02\"}', '2024-06-23 22:49:02', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '114', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"63\",\"fyear\":\"1\",\"VNo\":\"JV-28\",\"Vtype\":\"JV\",\"referenceNo\":\"1010\",\"VDate\":\"2024-06-23\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 22:49:02\"}', '2024-06-23 22:49:02', 1),
(0, '1', 'approved_vaucher_transation', 'create', '115', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"64\",\"fyear\":\"1\",\"VNo\":\"CV-16\",\"Vtype\":\"CV\",\"referenceNo\":\"1011\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020101\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"160.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:34:16\"}', '2024-06-23 23:34:16', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '116', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"64\",\"fyear\":\"1\",\"VNo\":\"CV-16\",\"Vtype\":\"CV\",\"referenceNo\":\"1011\",\"VDate\":\"2024-06-23\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"160.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:34:16\"}', '2024-06-23 23:34:16', 1),
(0, '1', 'approved_vaucher_transation', 'create', '117', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"65\",\"fyear\":\"1\",\"VNo\":\"JV-29\",\"Vtype\":\"JV\",\"referenceNo\":\"1011\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020801\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"1440.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"3\",\"subCode\":\"7\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:34:16\"}', '2024-06-23 23:34:16', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '118', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"65\",\"fyear\":\"1\",\"VNo\":\"JV-29\",\"Vtype\":\"JV\",\"referenceNo\":\"1011\",\"VDate\":\"2024-06-23\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"1440.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020801\",\"subType\":\"3\",\"subCode\":\"7\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:34:16\"}', '2024-06-23 23:34:16', 1),
(0, '1', 'approved_vaucher_transation', 'create', '119', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"66\",\"fyear\":\"1\",\"VNo\":\"JV-30\",\"Vtype\":\"JV\",\"referenceNo\":\"1011\",\"VDate\":\"2024-06-23\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"900.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:34:16\"}', '2024-06-23 23:34:16', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '120', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"66\",\"fyear\":\"1\",\"VNo\":\"JV-30\",\"Vtype\":\"JV\",\"referenceNo\":\"1011\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"900.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:34:16\"}', '2024-06-23 23:34:16', 1),
(0, '1', 'approved_vaucher_transation', 'create', '121', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"67\",\"fyear\":\"1\",\"VNo\":\"JV-31\",\"Vtype\":\"JV\",\"referenceNo\":\"1011\",\"VDate\":\"2024-06-23\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:34:16\"}', '2024-06-23 23:34:16', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '122', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"67\",\"fyear\":\"1\",\"VNo\":\"JV-31\",\"Vtype\":\"JV\",\"referenceNo\":\"1011\",\"VDate\":\"2024-06-23\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:34:16\"}', '2024-06-23 23:34:16', 1),
(0, '1', 'approved_vaucher_transation', 'create', '123', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"68\",\"fyear\":\"1\",\"VNo\":\"CV-17\",\"Vtype\":\"CV\",\"referenceNo\":\"1012\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020103\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"400.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:36:15\"}', '2024-06-23 23:36:15', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '124', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"68\",\"fyear\":\"1\",\"VNo\":\"CV-17\",\"Vtype\":\"CV\",\"referenceNo\":\"1012\",\"VDate\":\"2024-06-23\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"400.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:36:15\"}', '2024-06-23 23:36:15', 1),
(0, '1', 'approved_vaucher_transation', 'create', '125', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"69\",\"fyear\":\"1\",\"VNo\":\"CV-18\",\"Vtype\":\"CV\",\"referenceNo\":\"1012\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020101\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"100.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:36:15\"}', '2024-06-23 23:36:15', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '126', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"69\",\"fyear\":\"1\",\"VNo\":\"CV-18\",\"Vtype\":\"CV\",\"referenceNo\":\"1012\",\"VDate\":\"2024-06-23\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"100.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:36:15\"}', '2024-06-23 23:36:15', 1),
(0, '1', 'approved_vaucher_transation', 'create', '127', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"70\",\"fyear\":\"1\",\"VNo\":\"JV-32\",\"Vtype\":\"JV\",\"referenceNo\":\"1012\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020801\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"1100.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"3\",\"subCode\":\"7\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:36:15\"}', '2024-06-23 23:36:15', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '128', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"70\",\"fyear\":\"1\",\"VNo\":\"JV-32\",\"Vtype\":\"JV\",\"referenceNo\":\"1012\",\"VDate\":\"2024-06-23\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"1100.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020801\",\"subType\":\"3\",\"subCode\":\"7\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:36:15\"}', '2024-06-23 23:36:15', 1),
(0, '1', 'approved_vaucher_transation', 'create', '129', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"71\",\"fyear\":\"1\",\"VNo\":\"JV-33\",\"Vtype\":\"JV\",\"referenceNo\":\"1012\",\"VDate\":\"2024-06-23\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"900.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:36:15\"}', '2024-06-23 23:36:15', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '130', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"71\",\"fyear\":\"1\",\"VNo\":\"JV-33\",\"Vtype\":\"JV\",\"referenceNo\":\"1012\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"900.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:36:15\"}', '2024-06-23 23:36:15', 1),
(0, '1', 'approved_vaucher_transation', 'create', '131', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"72\",\"fyear\":\"1\",\"VNo\":\"JV-34\",\"Vtype\":\"JV\",\"referenceNo\":\"1012\",\"VDate\":\"2024-06-23\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:36:15\"}', '2024-06-23 23:36:15', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '132', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"72\",\"fyear\":\"1\",\"VNo\":\"JV-34\",\"Vtype\":\"JV\",\"referenceNo\":\"1012\",\"VDate\":\"2024-06-23\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:36:15\"}', '2024-06-23 23:36:15', 1),
(0, '1', 'approved_vaucher_transation', 'create', '133', 'acc_transaction', 'account/accounts/create_customer_receive', '{\"vid\":\"73\",\"fyear\":\"1\",\"VNo\":\"CV-19\",\"Vtype\":\"CV\",\"referenceNo\":\"1012\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020801\",\"Narration\":\"Sales Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Due Voucher for Customer\",\"Debit\":\"0.00\",\"Credit\":\"100.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"3\",\"subCode\":\"7\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:45:08\"}', '2024-06-23 23:45:08', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '134', 'acc_transaction', 'account/accounts/create_customer_receive', '{\"vid\":\"73\",\"fyear\":\"1\",\"VNo\":\"CV-19\",\"Vtype\":\"CV\",\"referenceNo\":\"1012\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020103\",\"Narration\":\"Sales Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Due Voucher for Customer\",\"Debit\":\"100.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020801\",\"subType\":\"3\",\"subCode\":\"7\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:45:08\"}', '2024-06-23 23:45:08', 1),
(0, '1', 'approved_vaucher_transation', 'create', '135', 'acc_transaction', 'account/accounts/create_customer_receive', '{\"vid\":\"74\",\"fyear\":\"1\",\"VNo\":\"CV-20\",\"Vtype\":\"CV\",\"referenceNo\":\"1012\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020801\",\"Narration\":\"Sales Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Due Voucher for Customer\",\"Debit\":\"0.00\",\"Credit\":\"300.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"3\",\"subCode\":\"7\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:47:31\"}', '2024-06-23 23:47:31', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '136', 'acc_transaction', 'account/accounts/create_customer_receive', '{\"vid\":\"74\",\"fyear\":\"1\",\"VNo\":\"CV-20\",\"Vtype\":\"CV\",\"referenceNo\":\"1012\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020103\",\"Narration\":\"Sales Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Due Voucher for Customer\",\"Debit\":\"300.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020801\",\"subType\":\"3\",\"subCode\":\"7\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:47:31\"}', '2024-06-23 23:47:31', 1),
(0, '1', 'approved_vaucher_transation', 'create', '137', 'acc_transaction', 'account/accounts/create_customer_receive', '{\"vid\":\"75\",\"fyear\":\"1\",\"VNo\":\"CV-21\",\"Vtype\":\"CV\",\"referenceNo\":\"1012\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020801\",\"Narration\":\"Sales Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Due Voucher for Customer\",\"Debit\":\"0.00\",\"Credit\":\"700.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"3\",\"subCode\":\"7\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:47:31\"}', '2024-06-23 23:47:31', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '138', 'acc_transaction', 'account/accounts/create_customer_receive', '{\"vid\":\"75\",\"fyear\":\"1\",\"VNo\":\"CV-21\",\"Vtype\":\"CV\",\"referenceNo\":\"1012\",\"VDate\":\"2024-06-23\",\"COAID\":\"1020103\",\"Narration\":\"Sales Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Due Voucher for Customer\",\"Debit\":\"700.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020801\",\"subType\":\"3\",\"subCode\":\"7\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 23:47:31\"}', '2024-06-23 23:47:31', 1),
(0, '1', 'approved_vaucher_transation', 'create', '139', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"76\",\"fyear\":\"1\",\"VNo\":\"DV-9\",\"Vtype\":\"DV\",\"referenceNo\":\"6\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"100.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 00:08:06\"}', '2024-06-24 00:08:06', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '140', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"76\",\"fyear\":\"1\",\"VNo\":\"DV-9\",\"Vtype\":\"DV\",\"referenceNo\":\"6\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020103\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"100.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 00:08:06\"}', '2024-06-24 00:08:06', 1),
(0, '1', 'approved_vaucher_transation', 'create', '141', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"77\",\"fyear\":\"1\",\"VNo\":\"DV-10\",\"Vtype\":\"DV\",\"referenceNo\":\"6\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"250.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 00:08:06\"}', '2024-06-24 00:08:06', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '142', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"77\",\"fyear\":\"1\",\"VNo\":\"DV-10\",\"Vtype\":\"DV\",\"referenceNo\":\"6\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020101\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"250.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 00:08:06\"}', '2024-06-24 00:08:06', 1),
(0, '1', 'approved_vaucher_transation', 'create', '143', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"78\",\"fyear\":\"1\",\"VNo\":\"JV-35\",\"Vtype\":\"JV\",\"referenceNo\":\"6\",\"VDate\":\"2024-06-24\",\"COAID\":\"5020201\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"1000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"4\",\"subCode\":\"1\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 00:08:06\"}', '2024-06-24 00:08:06', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '144', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"78\",\"fyear\":\"1\",\"VNo\":\"JV-35\",\"Vtype\":\"JV\",\"referenceNo\":\"6\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"1000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020201\",\"subType\":\"4\",\"subCode\":\"1\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 00:08:06\"}', '2024-06-24 00:08:06', 1),
(0, '1', 'approved_vaucher_transation', 'create', '145', 'acc_transaction', 'account/accounts/create_supplier_payment', '{\"vid\":\"79\",\"fyear\":\"1\",\"VNo\":\"DV-11\",\"Vtype\":\"DV\",\"referenceNo\":\"6\",\"VDate\":\"2024-06-24\",\"COAID\":\"5020201\",\"Narration\":\"Purchase Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Due Voucher for supplier\",\"Debit\":\"800.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"4\",\"subCode\":\"8\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 00:16:48\"}', '2024-06-24 00:16:48', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '146', 'acc_transaction', 'account/accounts/create_supplier_payment', '{\"vid\":\"79\",\"fyear\":\"1\",\"VNo\":\"DV-11\",\"Vtype\":\"DV\",\"referenceNo\":\"6\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020103\",\"Narration\":\"Purchase Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Due Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"800.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020201\",\"subType\":\"4\",\"subCode\":\"8\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 00:16:48\"}', '2024-06-24 00:16:48', 1),
(0, '1', 'approved_vaucher_transation', 'create', '147', 'acc_transaction', 'account/accounts/create_supplier_payment', '{\"vid\":\"80\",\"fyear\":\"1\",\"VNo\":\"DV-12\",\"Vtype\":\"DV\",\"referenceNo\":\"6\",\"VDate\":\"2024-06-24\",\"COAID\":\"5020201\",\"Narration\":\"Purchase Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Due Voucher for supplier\",\"Debit\":\"200.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020501\",\"subType\":\"4\",\"subCode\":\"8\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 00:16:48\"}', '2024-06-24 00:16:48', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '148', 'acc_transaction', 'account/accounts/create_supplier_payment', '{\"vid\":\"80\",\"fyear\":\"1\",\"VNo\":\"DV-12\",\"Vtype\":\"DV\",\"referenceNo\":\"6\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020501\",\"Narration\":\"Purchase Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Due Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"200.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020201\",\"subType\":\"4\",\"subCode\":\"8\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 00:16:48\"}', '2024-06-24 00:16:48', 1),
(0, '1', 'contra_vaucher', 'create', 'CT-4', 'acc_vaucher', 'account/accounts/store_contra_voucher', '{\"fyear\":\"1\",\"VNo\":\"CT-4\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-06-18\",\"COAID\":\"1020501\",\"Narration\":\"\",\"chequeNo\":null,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":\"400\",\"Credit\":0,\"RevCodde\":\"1020103\",\"subType\":1,\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 18:53:46\",\"status\":0}', '2024-06-24 00:23:46', 1),
(0, '1', 'approved_vaucher_transation', 'create', '149', 'acc_transaction', 'account/accounts/isactive/CT-4/active', '{\"vid\":\"81\",\"fyear\":\"1\",\"VNo\":\"CT-4\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-06-18\",\"COAID\":\"1020501\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"400.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 18:54:17\"}', '2024-06-24 00:24:17', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '150', 'acc_transaction', 'account/accounts/isactive/CT-4/active', '{\"vid\":\"81\",\"fyear\":\"1\",\"VNo\":\"CT-4\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-06-18\",\"COAID\":\"1020103\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"0.00\",\"Credit\":\"400.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020501\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-23 18:54:17\"}', '2024-06-24 00:24:17', 1),
(0, '1', 'approved_vaucher_transation', 'create', '151', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"82\",\"fyear\":\"1\",\"VNo\":\"DV-13\",\"Vtype\":\"DV\",\"referenceNo\":\"7\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"600.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:24:56\"}', '2024-06-24 15:24:56', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '152', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"82\",\"fyear\":\"1\",\"VNo\":\"DV-13\",\"Vtype\":\"DV\",\"referenceNo\":\"7\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020103\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"600.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:24:56\"}', '2024-06-24 15:24:56', 1),
(0, '1', 'approved_vaucher_transation', 'create', '153', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"83\",\"fyear\":\"1\",\"VNo\":\"DV-14\",\"Vtype\":\"DV\",\"referenceNo\":\"7\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"3000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:24:56\"}', '2024-06-24 15:24:56', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '154', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"83\",\"fyear\":\"1\",\"VNo\":\"DV-14\",\"Vtype\":\"DV\",\"referenceNo\":\"7\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020101\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"3000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:24:56\"}', '2024-06-24 15:24:56', 1),
(0, '1', 'approved_vaucher_transation', 'create', '155', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"84\",\"fyear\":\"1\",\"VNo\":\"DV-15\",\"Vtype\":\"DV\",\"referenceNo\":\"7\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"400.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020501\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:24:56\"}', '2024-06-24 15:24:56', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '156', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"84\",\"fyear\":\"1\",\"VNo\":\"DV-15\",\"Vtype\":\"DV\",\"referenceNo\":\"7\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020501\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"400.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:24:56\"}', '2024-06-24 15:24:56', 1),
(0, '1', 'approved_vaucher_transation', 'create', '157', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"85\",\"fyear\":\"1\",\"VNo\":\"JV-36\",\"Vtype\":\"JV\",\"referenceNo\":\"7\",\"VDate\":\"2024-06-24\",\"COAID\":\"5020201\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"500.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"4\",\"subCode\":\"1\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:24:56\"}', '2024-06-24 15:24:56', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '158', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"85\",\"fyear\":\"1\",\"VNo\":\"JV-36\",\"Vtype\":\"JV\",\"referenceNo\":\"7\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"500.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020201\",\"subType\":\"4\",\"subCode\":\"1\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:24:56\"}', '2024-06-24 15:24:57', 1),
(0, '1', 'approved_vaucher_transation', 'create', '159', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"86\",\"fyear\":\"1\",\"VNo\":\"CV-22\",\"Vtype\":\"CV\",\"referenceNo\":\"1013\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020101\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"600.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:45:25\"}', '2024-06-24 15:45:25', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '160', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"86\",\"fyear\":\"1\",\"VNo\":\"CV-22\",\"Vtype\":\"CV\",\"referenceNo\":\"1013\",\"VDate\":\"2024-06-24\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"600.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:45:25\"}', '2024-06-24 15:45:25', 1),
(0, '1', 'approved_vaucher_transation', 'create', '161', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"87\",\"fyear\":\"1\",\"VNo\":\"JV-37\",\"Vtype\":\"JV\",\"referenceNo\":\"1013\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020801\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"200.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"3\",\"subCode\":\"4\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:45:25\"}', '2024-06-24 15:45:25', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '162', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"87\",\"fyear\":\"1\",\"VNo\":\"JV-37\",\"Vtype\":\"JV\",\"referenceNo\":\"1013\",\"VDate\":\"2024-06-24\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"200.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020801\",\"subType\":\"3\",\"subCode\":\"4\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:45:25\"}', '2024-06-24 15:45:25', 1),
(0, '1', 'approved_vaucher_transation', 'create', '163', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"88\",\"fyear\":\"1\",\"VNo\":\"JV-38\",\"Vtype\":\"JV\",\"referenceNo\":\"1013\",\"VDate\":\"2024-06-24\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:45:25\"}', '2024-06-24 15:45:25', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '164', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"88\",\"fyear\":\"1\",\"VNo\":\"JV-38\",\"Vtype\":\"JV\",\"referenceNo\":\"1013\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:45:25\"}', '2024-06-24 15:45:25', 1),
(0, '1', 'approved_vaucher_transation', 'create', '165', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"89\",\"fyear\":\"1\",\"VNo\":\"JV-39\",\"Vtype\":\"JV\",\"referenceNo\":\"1013\",\"VDate\":\"2024-06-24\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:45:25\"}', '2024-06-24 15:45:25', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '166', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"89\",\"fyear\":\"1\",\"VNo\":\"JV-39\",\"Vtype\":\"JV\",\"referenceNo\":\"1013\",\"VDate\":\"2024-06-24\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:45:25\"}', '2024-06-24 15:45:25', 1),
(0, '1', 'approved_vaucher_transation', 'create', '167', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"90\",\"fyear\":\"1\",\"VNo\":\"CV-23\",\"Vtype\":\"CV\",\"referenceNo\":\"1014\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020101\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"600.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:45:41\"}', '2024-06-24 15:45:41', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '168', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"90\",\"fyear\":\"1\",\"VNo\":\"CV-23\",\"Vtype\":\"CV\",\"referenceNo\":\"1014\",\"VDate\":\"2024-06-24\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"600.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:45:41\"}', '2024-06-24 15:45:41', 1),
(0, '1', 'approved_vaucher_transation', 'create', '169', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"91\",\"fyear\":\"1\",\"VNo\":\"JV-40\",\"Vtype\":\"JV\",\"referenceNo\":\"1014\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020801\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"200.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"3\",\"subCode\":\"4\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:45:41\"}', '2024-06-24 15:45:41', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '170', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"91\",\"fyear\":\"1\",\"VNo\":\"JV-40\",\"Vtype\":\"JV\",\"referenceNo\":\"1014\",\"VDate\":\"2024-06-24\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"200.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020801\",\"subType\":\"3\",\"subCode\":\"4\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:45:41\"}', '2024-06-24 15:45:41', 1),
(0, '1', 'approved_vaucher_transation', 'create', '171', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"92\",\"fyear\":\"1\",\"VNo\":\"JV-41\",\"Vtype\":\"JV\",\"referenceNo\":\"1014\",\"VDate\":\"2024-06-24\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:45:41\"}', '2024-06-24 15:45:41', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '172', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"92\",\"fyear\":\"1\",\"VNo\":\"JV-41\",\"Vtype\":\"JV\",\"referenceNo\":\"1014\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:45:41\"}', '2024-06-24 15:45:41', 1),
(0, '1', 'approved_vaucher_transation', 'create', '173', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"93\",\"fyear\":\"1\",\"VNo\":\"JV-42\",\"Vtype\":\"JV\",\"referenceNo\":\"1014\",\"VDate\":\"2024-06-24\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:45:41\"}', '2024-06-24 15:45:41', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '174', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"93\",\"fyear\":\"1\",\"VNo\":\"JV-42\",\"Vtype\":\"JV\",\"referenceNo\":\"1014\",\"VDate\":\"2024-06-24\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:45:41\"}', '2024-06-24 15:45:41', 1),
(0, '1', 'approved_vaucher_transation', 'create', '175', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"94\",\"fyear\":\"1\",\"VNo\":\"CV-24\",\"Vtype\":\"CV\",\"referenceNo\":\"1015\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020101\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"800.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:46:02\"}', '2024-06-24 15:46:02', 1);
INSERT INTO `activity_logs` (`activity_id`, `user_id`, `type`, `action`, `action_id`, `table_name`, `slug`, `form_data`, `create_date`, `status`) VALUES
(0, '1', 'approved_vaucher_reversetransa', 'create', '176', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"94\",\"fyear\":\"1\",\"VNo\":\"CV-24\",\"Vtype\":\"CV\",\"referenceNo\":\"1015\",\"VDate\":\"2024-06-24\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"800.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:46:02\"}', '2024-06-24 15:46:02', 1),
(0, '1', 'approved_vaucher_transation', 'create', '177', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"95\",\"fyear\":\"1\",\"VNo\":\"JV-43\",\"Vtype\":\"JV\",\"referenceNo\":\"1015\",\"VDate\":\"2024-06-24\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:46:02\"}', '2024-06-24 15:46:02', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '178', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"95\",\"fyear\":\"1\",\"VNo\":\"JV-43\",\"Vtype\":\"JV\",\"referenceNo\":\"1015\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:46:02\"}', '2024-06-24 15:46:02', 1),
(0, '1', 'approved_vaucher_transation', 'create', '179', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"96\",\"fyear\":\"1\",\"VNo\":\"JV-44\",\"Vtype\":\"JV\",\"referenceNo\":\"1015\",\"VDate\":\"2024-06-24\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:46:02\"}', '2024-06-24 15:46:02', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '180', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"96\",\"fyear\":\"1\",\"VNo\":\"JV-44\",\"Vtype\":\"JV\",\"referenceNo\":\"1015\",\"VDate\":\"2024-06-24\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 15:46:02\"}', '2024-06-24 15:46:02', 1),
(0, '1', 'approved_vaucher_transation', 'create', '181', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"97\",\"fyear\":\"1\",\"VNo\":\"JV-45\",\"Vtype\":\"JV\",\"referenceNo\":\"8\",\"VDate\":\"2024-06-24\",\"COAID\":\"5020201\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"4\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 20:20:05\"}', '2024-06-24 20:20:05', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '182', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"97\",\"fyear\":\"1\",\"VNo\":\"JV-45\",\"Vtype\":\"JV\",\"referenceNo\":\"8\",\"VDate\":\"2024-06-24\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020201\",\"subType\":\"4\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-24 20:20:05\"}', '2024-06-24 20:20:05', 1),
(0, '1', 'approved_vaucher_transation', 'create', '183', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"98\",\"fyear\":\"1\",\"VNo\":\"CV-25\",\"Vtype\":\"CV\",\"referenceNo\":\"1016\",\"VDate\":\"2024-06-27\",\"COAID\":\"1020103\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"800.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:01:52\"}', '2024-06-27 21:01:52', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '184', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"98\",\"fyear\":\"1\",\"VNo\":\"CV-25\",\"Vtype\":\"CV\",\"referenceNo\":\"1016\",\"VDate\":\"2024-06-27\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"800.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:01:52\"}', '2024-06-27 21:01:52', 1),
(0, '1', 'approved_vaucher_transation', 'create', '185', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"99\",\"fyear\":\"1\",\"VNo\":\"JV-46\",\"Vtype\":\"JV\",\"referenceNo\":\"1016\",\"VDate\":\"2024-06-27\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:01:52\"}', '2024-06-27 21:01:52', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '186', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"99\",\"fyear\":\"1\",\"VNo\":\"JV-46\",\"Vtype\":\"JV\",\"referenceNo\":\"1016\",\"VDate\":\"2024-06-27\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:01:52\"}', '2024-06-27 21:01:53', 1),
(0, '1', 'approved_vaucher_transation', 'create', '187', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"100\",\"fyear\":\"1\",\"VNo\":\"JV-47\",\"Vtype\":\"JV\",\"referenceNo\":\"1016\",\"VDate\":\"2024-06-27\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:01:53\"}', '2024-06-27 21:01:53', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '188', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"100\",\"fyear\":\"1\",\"VNo\":\"JV-47\",\"Vtype\":\"JV\",\"referenceNo\":\"1016\",\"VDate\":\"2024-06-27\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:01:53\"}', '2024-06-27 21:01:53', 1),
(0, '1', 'approved_vaucher_transation', 'create', '189', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"101\",\"fyear\":\"1\",\"VNo\":\"CV-26\",\"Vtype\":\"CV\",\"referenceNo\":\"1017\",\"VDate\":\"2024-06-27\",\"COAID\":\"1020103\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"800.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:06:03\"}', '2024-06-27 21:06:03', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '190', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"101\",\"fyear\":\"1\",\"VNo\":\"CV-26\",\"Vtype\":\"CV\",\"referenceNo\":\"1017\",\"VDate\":\"2024-06-27\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"800.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:06:03\"}', '2024-06-27 21:06:03', 1),
(0, '1', 'approved_vaucher_transation', 'create', '191', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"102\",\"fyear\":\"1\",\"VNo\":\"JV-48\",\"Vtype\":\"JV\",\"referenceNo\":\"1017\",\"VDate\":\"2024-06-27\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:06:03\"}', '2024-06-27 21:06:03', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '192', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"102\",\"fyear\":\"1\",\"VNo\":\"JV-48\",\"Vtype\":\"JV\",\"referenceNo\":\"1017\",\"VDate\":\"2024-06-27\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:06:03\"}', '2024-06-27 21:06:03', 1),
(0, '1', 'approved_vaucher_transation', 'create', '193', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"103\",\"fyear\":\"1\",\"VNo\":\"JV-49\",\"Vtype\":\"JV\",\"referenceNo\":\"1017\",\"VDate\":\"2024-06-27\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:06:03\"}', '2024-06-27 21:06:03', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '194', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"103\",\"fyear\":\"1\",\"VNo\":\"JV-49\",\"Vtype\":\"JV\",\"referenceNo\":\"1017\",\"VDate\":\"2024-06-27\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:06:03\"}', '2024-06-27 21:06:03', 1),
(0, '1', 'approved_vaucher_transation', 'create', '195', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"104\",\"fyear\":\"1\",\"VNo\":\"CV-27\",\"Vtype\":\"CV\",\"referenceNo\":\"1018\",\"VDate\":\"2024-06-27\",\"COAID\":\"1020103\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"800.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:25:55\"}', '2024-06-27 21:25:55', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '196', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"104\",\"fyear\":\"1\",\"VNo\":\"CV-27\",\"Vtype\":\"CV\",\"referenceNo\":\"1018\",\"VDate\":\"2024-06-27\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"800.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:25:55\"}', '2024-06-27 21:25:55', 1),
(0, '1', 'approved_vaucher_transation', 'create', '197', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"105\",\"fyear\":\"1\",\"VNo\":\"JV-50\",\"Vtype\":\"JV\",\"referenceNo\":\"1018\",\"VDate\":\"2024-06-27\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:25:55\"}', '2024-06-27 21:25:55', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '198', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"105\",\"fyear\":\"1\",\"VNo\":\"JV-50\",\"Vtype\":\"JV\",\"referenceNo\":\"1018\",\"VDate\":\"2024-06-27\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:25:55\"}', '2024-06-27 21:25:55', 1),
(0, '1', 'approved_vaucher_transation', 'create', '199', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"106\",\"fyear\":\"1\",\"VNo\":\"JV-51\",\"Vtype\":\"JV\",\"referenceNo\":\"1018\",\"VDate\":\"2024-06-27\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:25:55\"}', '2024-06-27 21:25:55', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '200', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"106\",\"fyear\":\"1\",\"VNo\":\"JV-51\",\"Vtype\":\"JV\",\"referenceNo\":\"1018\",\"VDate\":\"2024-06-27\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:25:55\"}', '2024-06-27 21:25:55', 1),
(0, '1', 'approved_vaucher_transation', 'create', '201', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"107\",\"fyear\":\"1\",\"VNo\":\"JV-52\",\"Vtype\":\"JV\",\"referenceNo\":\"1019\",\"VDate\":\"2024-06-27\",\"COAID\":\"1020801\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"1600.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"3\",\"subCode\":\"4\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:27:07\"}', '2024-06-27 21:27:07', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '202', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"107\",\"fyear\":\"1\",\"VNo\":\"JV-52\",\"Vtype\":\"JV\",\"referenceNo\":\"1019\",\"VDate\":\"2024-06-27\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"1600.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020801\",\"subType\":\"3\",\"subCode\":\"4\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:27:07\"}', '2024-06-27 21:27:07', 1),
(0, '1', 'approved_vaucher_transation', 'create', '203', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"108\",\"fyear\":\"1\",\"VNo\":\"JV-53\",\"Vtype\":\"JV\",\"referenceNo\":\"1019\",\"VDate\":\"2024-06-27\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"900.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:27:07\"}', '2024-06-27 21:27:07', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '204', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"108\",\"fyear\":\"1\",\"VNo\":\"JV-53\",\"Vtype\":\"JV\",\"referenceNo\":\"1019\",\"VDate\":\"2024-06-27\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"900.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:27:07\"}', '2024-06-27 21:27:07', 1),
(0, '1', 'approved_vaucher_transation', 'create', '205', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"109\",\"fyear\":\"1\",\"VNo\":\"JV-54\",\"Vtype\":\"JV\",\"referenceNo\":\"1019\",\"VDate\":\"2024-06-27\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:27:07\"}', '2024-06-27 21:27:07', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '206', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"109\",\"fyear\":\"1\",\"VNo\":\"JV-54\",\"Vtype\":\"JV\",\"referenceNo\":\"1019\",\"VDate\":\"2024-06-27\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:27:07\"}', '2024-06-27 21:27:07', 1),
(0, '1', 'approved_vaucher_transation', 'create', '207', 'acc_transaction', 'account/accounts/create_customer_receive', '{\"vid\":\"110\",\"fyear\":\"1\",\"VNo\":\"CV-28\",\"Vtype\":\"CV\",\"referenceNo\":\"1019\",\"VDate\":\"2024-06-27\",\"COAID\":\"1020801\",\"Narration\":\"Sales Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Due Voucher for Customer\",\"Debit\":\"0.00\",\"Credit\":\"1600.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"3\",\"subCode\":\"4\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:28:27\"}', '2024-06-27 21:28:27', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '208', 'acc_transaction', 'account/accounts/create_customer_receive', '{\"vid\":\"110\",\"fyear\":\"1\",\"VNo\":\"CV-28\",\"Vtype\":\"CV\",\"referenceNo\":\"1019\",\"VDate\":\"2024-06-27\",\"COAID\":\"1020103\",\"Narration\":\"Sales Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Due Voucher for Customer\",\"Debit\":\"1600.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020801\",\"subType\":\"3\",\"subCode\":\"4\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 21:28:27\"}', '2024-06-27 21:28:27', 1),
(0, '1', 'contra_vaucher', 'create', 'CT-5', 'acc_vaucher', 'account/accounts/store_contra_voucher', '{\"fyear\":\"1\",\"VNo\":\"CT-5\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-06-27\",\"COAID\":\"1020502\",\"Narration\":\"\",\"chequeNo\":null,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":\"300\",\"Credit\":0,\"RevCodde\":\"1020103\",\"subType\":1,\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 16:01:27\",\"status\":0}', '2024-06-27 21:31:27', 1),
(0, '1', 'approved_vaucher_transation', 'create', '209', 'acc_transaction', 'account/accounts/isactive/CT-5/active', '{\"vid\":\"111\",\"fyear\":\"1\",\"VNo\":\"CT-5\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-06-27\",\"COAID\":\"1020502\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"300.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 16:04:09\"}', '2024-06-27 21:34:09', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '210', 'acc_transaction', 'account/accounts/isactive/CT-5/active', '{\"vid\":\"111\",\"fyear\":\"1\",\"VNo\":\"CT-5\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-06-27\",\"COAID\":\"1020103\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"0.00\",\"Credit\":\"300.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020502\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-06-27 16:04:09\"}', '2024-06-27 21:34:09', 1),
(0, '1', 'sub_account', 'create', '9', 'acc_subcode', 'subaccount_list', '{\"subTypeId\":\"6\",\"name\":\"Agent 1\",\"referenceNo\":0,\"status\":1,\"created_by\":\"1\",\"created_date\":\"2024-07-03 06:38:34\"}', '2024-07-04 00:08:34', 1),
(0, '1', 'sub_account', 'create', '10', 'acc_subcode', 'subaccount_list', '{\"subTypeId\":\"6\",\"name\":\"Agent 2\",\"referenceNo\":0,\"status\":1,\"created_by\":\"1\",\"created_date\":\"2024-07-03 06:38:50\"}', '2024-07-04 00:08:50', 1),
(0, '1', 'coa_account', 'create', '242', 'acc_coa', 'account/accounts/insert_coa', '{\"HeadCode\":\"1020803\",\"Pheadcode\":\"10208\",\"HeadName\":\"Debtors\",\"PHeadName\":\"Accounts Receivable\",\"HeadLevel\":\"4\",\"IsActive\":\"1\",\"isStock\":0,\"isSubType\":\"1\",\"DepreciationRate\":0,\"HeadType\":\"A\",\"IsBudget\":0,\"isCashNature\":0,\"isBankNature\":0,\"isFixedAssetSch\":0,\"assetCode\":null,\"depCode\":null,\"subType\":\"6\",\"noteNo\":null,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-03 18:41:17\"}', '2024-07-04 00:11:17', 1),
(0, '1', 'coa_account', 'create', '243', 'acc_coa', 'account/accounts/insert_coa', '{\"HeadCode\":\"5020202\",\"Pheadcode\":\"50202\",\"HeadName\":\"Creditors\",\"PHeadName\":\"Accounts Payable\",\"HeadLevel\":\"4\",\"IsActive\":\"1\",\"isStock\":0,\"isSubType\":\"1\",\"DepreciationRate\":0,\"HeadType\":\"L\",\"IsBudget\":0,\"isCashNature\":0,\"isBankNature\":0,\"isFixedAssetSch\":0,\"assetCode\":null,\"depCode\":null,\"subType\":\"6\",\"noteNo\":null,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-03 18:44:53\"}', '2024-07-04 00:14:53', 1),
(0, '1', 'sub_account', 'create', '11', 'acc_subcode', 'subaccount_list', '{\"subTypeId\":\"6\",\"name\":\"Creditor 1\",\"referenceNo\":0,\"status\":1,\"created_by\":\"1\",\"created_date\":\"2024-07-03 06:45:44\"}', '2024-07-04 00:15:44', 1),
(0, '1', 'sub_account', 'create', '12', 'acc_subcode', 'subaccount_list', '{\"subTypeId\":\"6\",\"name\":\"Creditor 2\",\"referenceNo\":0,\"status\":1,\"created_by\":\"1\",\"created_date\":\"2024-07-03 06:45:56\"}', '2024-07-04 00:15:56', 1),
(0, '1', 'sub_account', 'create', '13', 'acc_subcode', 'subaccount_list', '{\"subTypeId\":\"1\",\"name\":\"Debtor 1\",\"referenceNo\":0,\"status\":1,\"created_by\":\"1\",\"created_date\":\"2024-07-03 07:30:10\"}', '2024-07-04 01:00:10', 1),
(0, '1', 'sub_account', 'create', '14', 'acc_subcode', 'subaccount_list', '{\"subTypeId\":\"1\",\"name\":\"Debtor 2\",\"referenceNo\":0,\"status\":1,\"created_by\":\"1\",\"created_date\":\"2024-07-03 07:30:26\"}', '2024-07-04 01:00:26', 1),
(0, '1', 'coa_account', 'update', '5020202', 'acc_coa', 'account/accounts/insert_coa', '{\"HeadCode\":\"5020202\",\"Pheadcode\":\"50202\",\"HeadName\":\"Creditors\",\"PHeadName\":\"Accounts Payable\",\"HeadLevel\":\"4\",\"IsActive\":\"1\",\"isStock\":0,\"isSubType\":\"1\",\"DepreciationRate\":0,\"HeadType\":\"L\",\"IsBudget\":0,\"isCashNature\":0,\"isBankNature\":0,\"isFixedAssetSch\":0,\"assetCode\":null,\"depCode\":null,\"subType\":\"1\",\"noteNo\":null,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-04 15:16:30\"}', '2024-07-04 20:46:30', 2),
(0, '1', 'coa_account', 'update', '5020202', 'acc_coa', 'account/accounts/insert_coa', '{\"HeadCode\":\"5020202\",\"Pheadcode\":\"50202\",\"HeadName\":\"Creditors\",\"PHeadName\":\"Accounts Payable\",\"HeadLevel\":\"4\",\"IsActive\":\"1\",\"isStock\":0,\"isSubType\":\"1\",\"DepreciationRate\":0,\"HeadType\":\"L\",\"IsBudget\":0,\"isCashNature\":0,\"isBankNature\":0,\"isFixedAssetSch\":0,\"assetCode\":null,\"depCode\":null,\"subType\":\"2\",\"noteNo\":null,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-04 15:17:35\"}', '2024-07-04 20:47:35', 2),
(0, '1', 'sub_account', 'delete', '13', 'acc_subcode', 'account/accounts/delete_sub_account/13', '{\"id\":\"13\",\"subTypeId\":\"1\",\"name\":\"Debtor 1\",\"deleteBy\":\"1\",\"deleteDate\":\"2024-07-04 03:17:59\"}', '2024-07-04 20:47:59', 3),
(0, '1', 'sub_account', 'delete', '14', 'acc_subcode', 'account/accounts/delete_sub_account/14', '{\"id\":\"14\",\"subTypeId\":\"1\",\"name\":\"Debtor 2\",\"deleteBy\":\"1\",\"deleteDate\":\"2024-07-04 03:18:07\"}', '2024-07-04 20:48:07', 3),
(0, '1', 'sub_account', 'create', '15', 'acc_subcode', 'subaccount_list', '{\"subTypeId\":\"2\",\"name\":\"Debtor X1\",\"referenceNo\":0,\"status\":1,\"created_by\":\"1\",\"created_date\":\"2024-07-04 03:18:28\"}', '2024-07-04 20:48:28', 1),
(0, '1', 'sub_account', 'create', '16', 'acc_subcode', 'subaccount_list', '{\"subTypeId\":\"2\",\"name\":\"Debtor X2\",\"referenceNo\":0,\"status\":1,\"created_by\":\"1\",\"created_date\":\"2024-07-04 03:18:37\"}', '2024-07-04 20:48:37', 1),
(0, '1', 'sub_account', 'update', '15', 'acc_subcode', 'account/accounts/update_sub_account', '{\"id\":\"15\",\"subTypeId\":\"2\",\"name\":\"Creditors X1\",\"updated_by\":\"1\",\"updated_date\":\"2024-07-04 03:19:25\"}', '2024-07-04 20:49:25', 2),
(0, '1', 'sub_account', 'delete', '11', 'acc_subcode', 'account/accounts/delete_sub_account/11', '{\"id\":\"11\",\"subTypeId\":\"6\",\"name\":\"Creditor 1\",\"deleteBy\":\"1\",\"deleteDate\":\"2024-07-05 04:57:31\"}', '2024-07-05 22:27:31', 3),
(0, '1', 'sub_account', 'delete', '12', 'acc_subcode', 'account/accounts/delete_sub_account/12', '{\"id\":\"12\",\"subTypeId\":\"6\",\"name\":\"Creditor 2\",\"deleteBy\":\"1\",\"deleteDate\":\"2024-07-05 04:57:52\"}', '2024-07-05 22:27:52', 3),
(0, '1', 'sub_account', 'delete', '16', 'acc_subcode', 'account/accounts/delete_sub_account/16', '{\"id\":\"16\",\"subTypeId\":\"2\",\"name\":\"Debtor X2\",\"deleteBy\":\"1\",\"deleteDate\":\"2024-07-05 04:57:58\"}', '2024-07-05 22:27:58', 3),
(0, '1', 'sub_account', 'delete', '15', 'acc_subcode', 'account/accounts/delete_sub_account/15', '{\"id\":\"15\",\"subTypeId\":\"2\",\"name\":\"Creditors X1\",\"deleteBy\":\"1\",\"deleteDate\":\"2024-07-05 04:58:02\"}', '2024-07-05 22:28:02', 3),
(0, '1', 'coa_account', 'update', '1020803', 'acc_coa', 'account/accounts/insert_coa', '{\"HeadCode\":\"1020803\",\"Pheadcode\":\"10208\",\"HeadName\":\"Debtors\",\"PHeadName\":\"Accounts Receivable\",\"HeadLevel\":\"4\",\"IsActive\":\"1\",\"isStock\":0,\"isSubType\":\"1\",\"DepreciationRate\":0,\"HeadType\":\"A\",\"IsBudget\":0,\"isCashNature\":0,\"isBankNature\":0,\"isFixedAssetSch\":0,\"assetCode\":null,\"depCode\":null,\"subType\":\"7\",\"noteNo\":null,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-05 16:58:44\"}', '2024-07-05 22:28:44', 2),
(0, '1', 'coa_account', 'update', '5020202', 'acc_coa', 'account/accounts/insert_coa', '{\"HeadCode\":\"5020202\",\"Pheadcode\":\"50202\",\"HeadName\":\"Creditors\",\"PHeadName\":\"Accounts Payable\",\"HeadLevel\":\"4\",\"IsActive\":\"1\",\"isStock\":0,\"isSubType\":\"1\",\"DepreciationRate\":0,\"HeadType\":\"L\",\"IsBudget\":0,\"isCashNature\":0,\"isBankNature\":0,\"isFixedAssetSch\":0,\"assetCode\":null,\"depCode\":null,\"subType\":\"8\",\"noteNo\":null,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-05 16:59:16\"}', '2024-07-05 22:29:16', 2),
(0, '1', 'sub_account', 'create', '17', 'acc_subcode', 'subaccount_list', '{\"subTypeId\":\"7\",\"name\":\"Debtor Test 01\",\"referenceNo\":0,\"status\":1,\"created_by\":\"1\",\"created_date\":\"2024-07-05 05:01:33\"}', '2024-07-05 22:31:33', 1),
(0, '1', 'sub_account', 'create', '18', 'acc_subcode', 'subaccount_list', '{\"subTypeId\":\"7\",\"name\":\"Debtor Test 02\",\"referenceNo\":0,\"status\":1,\"created_by\":\"1\",\"created_date\":\"2024-07-05 05:01:42\"}', '2024-07-05 22:31:42', 1),
(0, '1', 'sub_account', 'create', '19', 'acc_subcode', 'subaccount_list', '{\"subTypeId\":\"8\",\"name\":\"Creditor Test 01\",\"referenceNo\":0,\"status\":1,\"created_by\":\"1\",\"created_date\":\"2024-07-05 05:01:58\"}', '2024-07-05 22:31:58', 1),
(0, '1', 'sub_account', 'create', '20', 'acc_subcode', 'subaccount_list', '{\"subTypeId\":\"8\",\"name\":\"Creditor Test 02\",\"referenceNo\":0,\"status\":1,\"created_by\":\"1\",\"created_date\":\"2024-07-05 05:02:08\"}', '2024-07-05 22:32:08', 1),
(0, '1', 'credit_vaucher', 'create', 'CV-29', 'acc_vaucher', 'account/accounts/store_credit_voucher', '{\"fyear\":\"1\",\"VNo\":\"CV-29\",\"Vtype\":\"CV\",\"referenceNo\":null,\"VDate\":\"2024-07-05\",\"COAID\":\"3020101\",\"Narration\":\"\",\"chequeNo\":null,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":0,\"Credit\":\"250\",\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-05 18:01:33\",\"status\":0}', '2024-07-05 23:31:33', 1),
(0, '1', 'approved_vaucher_transation', 'create', '211', 'acc_transaction', 'account/accounts/isactive/CV-29/active', '{\"vid\":\"112\",\"fyear\":\"1\",\"VNo\":\"CV-29\",\"Vtype\":\"CV\",\"referenceNo\":null,\"VDate\":\"2024-07-05\",\"COAID\":\"3020101\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"0.00\",\"Credit\":\"250.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-05 18:15:48\"}', '2024-07-05 23:45:48', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '212', 'acc_transaction', 'account/accounts/isactive/CV-29/active', '{\"vid\":\"112\",\"fyear\":\"1\",\"VNo\":\"CV-29\",\"Vtype\":\"CV\",\"referenceNo\":null,\"VDate\":\"2024-07-05\",\"COAID\":\"1020103\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"250.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-05 18:15:48\"}', '2024-07-05 23:45:48', 1),
(0, '1', 'credit_vaucher', 'create', 'CV-30', 'acc_vaucher', 'account/accounts/store_credit_voucher', '{\"fyear\":\"1\",\"VNo\":\"CV-30\",\"Vtype\":\"CV\",\"referenceNo\":null,\"VDate\":\"2024-07-05\",\"COAID\":\"3020101\",\"Narration\":\"XX\",\"chequeNo\":null,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"XXX\",\"Debit\":0,\"Credit\":\"450\",\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-05 18:24:10\",\"status\":0}', '2024-07-05 23:54:10', 1),
(0, '1', 'debit_vaucher', 'create', 'DV-16', 'acc_vaucher', 'account/accounts/store_debit_voucher', '{\"fyear\":\"1\",\"VNo\":\"DV-16\",\"Vtype\":\"DV\",\"referenceNo\":null,\"VDate\":\"2024-07-05\",\"COAID\":\"4021001\",\"Narration\":\"YY\",\"chequeNo\":null,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"YYY\",\"Debit\":\"700\",\"Credit\":0,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-05 18:29:11\",\"status\":0}', '2024-07-05 23:59:11', 1),
(0, '1', 'approved_vaucher_transation', 'create', '213', 'acc_transaction', 'account/accounts/bulk_voucher_approve', '{\"vid\":\"114\",\"fyear\":\"1\",\"VNo\":\"DV-16\",\"Vtype\":\"DV\",\"referenceNo\":null,\"VDate\":\"2024-07-05\",\"COAID\":\"4021001\",\"Narration\":\"YY\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"YYY\",\"Debit\":\"700.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-05 18:29:32\"}', '2024-07-05 23:59:32', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '214', 'acc_transaction', 'account/accounts/bulk_voucher_approve', '{\"vid\":\"114\",\"fyear\":\"1\",\"VNo\":\"DV-16\",\"Vtype\":\"DV\",\"referenceNo\":null,\"VDate\":\"2024-07-05\",\"COAID\":\"1020103\",\"Narration\":\"YY\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"YYY\",\"Debit\":\"0.00\",\"Credit\":\"700.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021001\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-05 18:29:32\"}', '2024-07-05 23:59:32', 1),
(0, '1', 'approved_vaucher_transation', 'create', '215', 'acc_transaction', 'account/accounts/bulk_voucher_approve', '{\"vid\":\"113\",\"fyear\":\"1\",\"VNo\":\"CV-30\",\"Vtype\":\"CV\",\"referenceNo\":null,\"VDate\":\"2024-07-05\",\"COAID\":\"3020101\",\"Narration\":\"XX\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"XXX\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-05 18:29:32\"}', '2024-07-05 23:59:32', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '216', 'acc_transaction', 'account/accounts/bulk_voucher_approve', '{\"vid\":\"113\",\"fyear\":\"1\",\"VNo\":\"CV-30\",\"Vtype\":\"CV\",\"referenceNo\":null,\"VDate\":\"2024-07-05\",\"COAID\":\"1020103\",\"Narration\":\"XX\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"XXX\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-05 18:29:32\"}', '2024-07-05 23:59:32', 1),
(0, '1', 'credit_vaucher', 'create', 'CV-31', 'acc_vaucher', 'account/accounts/store_credit_voucher', '{\"fyear\":\"1\",\"VNo\":\"CV-31\",\"Vtype\":\"CV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-17\",\"COAID\":\"5020202\",\"Narration\":\"\",\"chequeNo\":null,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":0,\"Credit\":\"10000\",\"RevCodde\":\"1020103\",\"subType\":\"8\",\"subCode\":\"19\",\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-17 15:29:19\",\"status\":0}', '2024-07-17 20:59:19', 1),
(0, '1', 'approved_vaucher_transation', 'create', '217', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"116\",\"fyear\":\"1\",\"VNo\":\"CV-32\",\"Vtype\":\"CV\",\"referenceNo\":\"1020\",\"VDate\":\"2024-07-21\",\"COAID\":\"1020103\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"800.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 00:53:00\"}', '2024-07-21 00:53:00', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '218', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"116\",\"fyear\":\"1\",\"VNo\":\"CV-32\",\"Vtype\":\"CV\",\"referenceNo\":\"1020\",\"VDate\":\"2024-07-21\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"800.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 00:53:00\"}', '2024-07-21 00:53:00', 1),
(0, '1', 'approved_vaucher_transation', 'create', '219', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"117\",\"fyear\":\"1\",\"VNo\":\"JV-55\",\"Vtype\":\"JV\",\"referenceNo\":\"1020\",\"VDate\":\"2024-07-21\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 00:53:00\"}', '2024-07-21 00:53:00', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '220', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"117\",\"fyear\":\"1\",\"VNo\":\"JV-55\",\"Vtype\":\"JV\",\"referenceNo\":\"1020\",\"VDate\":\"2024-07-21\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 00:53:00\"}', '2024-07-21 00:53:00', 1),
(0, '1', 'approved_vaucher_transation', 'create', '221', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"118\",\"fyear\":\"1\",\"VNo\":\"JV-56\",\"Vtype\":\"JV\",\"referenceNo\":\"1020\",\"VDate\":\"2024-07-21\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 00:53:00\"}', '2024-07-21 00:53:00', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '222', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"118\",\"fyear\":\"1\",\"VNo\":\"JV-56\",\"Vtype\":\"JV\",\"referenceNo\":\"1020\",\"VDate\":\"2024-07-21\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 00:53:00\"}', '2024-07-21 00:53:00', 1),
(0, '1', 'debit_vaucher', 'create', 'DV-17', 'acc_vaucher', 'account/accounts/store_debit_voucher', '{\"fyear\":\"1\",\"VNo\":\"DV-17\",\"Vtype\":\"DV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-21\",\"COAID\":\"5020202\",\"Narration\":\"\",\"chequeNo\":\"30\",\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":\"300\",\"Credit\":0,\"RevCodde\":\"1020103\",\"subType\":\"8\",\"subCode\":\"19\",\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 06:09:24\",\"status\":0}', '2024-07-21 11:39:24', 1),
(0, '1', 'approved_vaucher_transation', 'create', '223', 'acc_transaction', 'account/accounts/isactive/DV-17/active', '{\"vid\":\"119\",\"fyear\":\"1\",\"VNo\":\"DV-17\",\"Vtype\":\"DV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-21\",\"COAID\":\"5020202\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"300.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"8\",\"subCode\":\"19\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 06:10:25\"}', '2024-07-21 11:40:25', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '224', 'acc_transaction', 'account/accounts/isactive/DV-17/active', '{\"vid\":\"119\",\"fyear\":\"1\",\"VNo\":\"DV-17\",\"Vtype\":\"DV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-21\",\"COAID\":\"1020103\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"0.00\",\"Credit\":\"300.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020202\",\"subType\":\"8\",\"subCode\":\"19\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 06:10:25\"}', '2024-07-21 11:40:25', 1),
(0, '1', 'credit_vaucher', 'create', 'CV-33', 'acc_vaucher', 'account/accounts/store_credit_voucher', '{\"fyear\":\"1\",\"VNo\":\"CV-33\",\"Vtype\":\"CV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-21\",\"COAID\":\"1020803\",\"Narration\":\"\",\"chequeNo\":33,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":0,\"Credit\":\"300\",\"RevCodde\":\"1020103\",\"subType\":\"7\",\"subCode\":\"17\",\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 06:11:54\",\"status\":0}', '2024-07-21 11:41:54', 1),
(0, '1', 'approved_vaucher_transation', 'create', '225', 'acc_transaction', 'account/accounts/isactive/CV-33/active', '{\"vid\":\"120\",\"fyear\":\"1\",\"VNo\":\"CV-33\",\"Vtype\":\"CV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-21\",\"COAID\":\"1020803\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"0.00\",\"Credit\":\"300.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"7\",\"subCode\":\"17\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 06:12:52\"}', '2024-07-21 11:42:52', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '226', 'acc_transaction', 'account/accounts/isactive/CV-33/active', '{\"vid\":\"120\",\"fyear\":\"1\",\"VNo\":\"CV-33\",\"Vtype\":\"CV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-21\",\"COAID\":\"1020103\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"300.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020803\",\"subType\":\"7\",\"subCode\":\"17\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 06:12:52\"}', '2024-07-21 11:42:52', 1),
(0, '1', 'approved_vaucher_transation', 'create', '227', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"121\",\"fyear\":\"1\",\"VNo\":\"CV-34\",\"Vtype\":\"CV\",\"referenceNo\":\"1021\",\"VDate\":\"2024-07-21\",\"COAID\":\"1020101\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"800.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 12:43:07\"}', '2024-07-21 12:43:07', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '228', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"121\",\"fyear\":\"1\",\"VNo\":\"CV-34\",\"Vtype\":\"CV\",\"referenceNo\":\"1021\",\"VDate\":\"2024-07-21\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"800.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 12:43:07\"}', '2024-07-21 12:43:07', 1),
(0, '1', 'approved_vaucher_transation', 'create', '229', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"122\",\"fyear\":\"1\",\"VNo\":\"JV-57\",\"Vtype\":\"JV\",\"referenceNo\":\"1021\",\"VDate\":\"2024-07-21\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 12:43:07\"}', '2024-07-21 12:43:07', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '230', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"122\",\"fyear\":\"1\",\"VNo\":\"JV-57\",\"Vtype\":\"JV\",\"referenceNo\":\"1021\",\"VDate\":\"2024-07-21\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 12:43:07\"}', '2024-07-21 12:43:07', 1),
(0, '1', 'approved_vaucher_transation', 'create', '231', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"123\",\"fyear\":\"1\",\"VNo\":\"JV-58\",\"Vtype\":\"JV\",\"referenceNo\":\"1021\",\"VDate\":\"2024-07-21\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 12:43:07\"}', '2024-07-21 12:43:07', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '232', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"123\",\"fyear\":\"1\",\"VNo\":\"JV-58\",\"Vtype\":\"JV\",\"referenceNo\":\"1021\",\"VDate\":\"2024-07-21\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 12:43:07\"}', '2024-07-21 12:43:07', 1),
(0, '1', 'approved_vaucher_transation', 'create', '233', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"124\",\"fyear\":\"1\",\"VNo\":\"CV-35\",\"Vtype\":\"CV\",\"referenceNo\":\"1022\",\"VDate\":\"2024-07-21\",\"COAID\":\"1020103\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"800.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 12:46:15\"}', '2024-07-21 12:46:15', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '234', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"124\",\"fyear\":\"1\",\"VNo\":\"CV-35\",\"Vtype\":\"CV\",\"referenceNo\":\"1022\",\"VDate\":\"2024-07-21\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"800.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 12:46:15\"}', '2024-07-21 12:46:15', 1),
(0, '1', 'approved_vaucher_transation', 'create', '235', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"125\",\"fyear\":\"1\",\"VNo\":\"JV-59\",\"Vtype\":\"JV\",\"referenceNo\":\"1022\",\"VDate\":\"2024-07-21\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 12:46:15\"}', '2024-07-21 12:46:15', 1);
INSERT INTO `activity_logs` (`activity_id`, `user_id`, `type`, `action`, `action_id`, `table_name`, `slug`, `form_data`, `create_date`, `status`) VALUES
(0, '1', 'approved_vaucher_reversetransa', 'create', '236', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"125\",\"fyear\":\"1\",\"VNo\":\"JV-59\",\"Vtype\":\"JV\",\"referenceNo\":\"1022\",\"VDate\":\"2024-07-21\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 12:46:15\"}', '2024-07-21 12:46:15', 1),
(0, '1', 'approved_vaucher_transation', 'create', '237', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"126\",\"fyear\":\"1\",\"VNo\":\"JV-60\",\"Vtype\":\"JV\",\"referenceNo\":\"1022\",\"VDate\":\"2024-07-21\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 12:46:15\"}', '2024-07-21 12:46:15', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '238', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"126\",\"fyear\":\"1\",\"VNo\":\"JV-60\",\"Vtype\":\"JV\",\"referenceNo\":\"1022\",\"VDate\":\"2024-07-21\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 12:46:15\"}', '2024-07-21 12:46:15', 1),
(0, '1', 'credit_vaucher', 'create', 'CV-36', 'acc_vaucher', 'account/accounts/store_credit_voucher', '{\"fyear\":\"1\",\"VNo\":\"CV-36\",\"Vtype\":\"CV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-21\",\"COAID\":\"5020202\",\"Narration\":\"\",\"chequeNo\":35,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":0,\"Credit\":\"100\",\"RevCodde\":\"1020103\",\"subType\":\"8\",\"subCode\":\"19\",\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 07:19:32\",\"status\":0}', '2024-07-21 12:49:32', 1),
(0, '1', 'approved_vaucher_transation', 'create', '239', 'acc_transaction', 'account/accounts/isactive/CV-36/active', '{\"vid\":\"127\",\"fyear\":\"1\",\"VNo\":\"CV-36\",\"Vtype\":\"CV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-21\",\"COAID\":\"5020202\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"0.00\",\"Credit\":\"100.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"8\",\"subCode\":\"19\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 07:19:51\"}', '2024-07-21 12:49:51', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '240', 'acc_transaction', 'account/accounts/isactive/CV-36/active', '{\"vid\":\"127\",\"fyear\":\"1\",\"VNo\":\"CV-36\",\"Vtype\":\"CV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-21\",\"COAID\":\"1020103\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"100.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020202\",\"subType\":\"8\",\"subCode\":\"19\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 07:19:51\"}', '2024-07-21 12:49:51', 1),
(0, '1', 'debit_vaucher', 'create', 'DV-18', 'acc_vaucher', 'account/accounts/store_debit_voucher', '{\"fyear\":\"1\",\"VNo\":\"DV-18\",\"Vtype\":\"DV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-21\",\"COAID\":\"5020202\",\"Narration\":\"\",\"chequeNo\":\"36\",\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":\"100\",\"Credit\":0,\"RevCodde\":\"1020103\",\"subType\":\"8\",\"subCode\":\"19\",\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 07:21:01\",\"status\":0}', '2024-07-21 12:51:01', 1),
(0, '1', 'credit_vaucher', 'create', 'CV-37', 'acc_vaucher', 'account/accounts/store_credit_voucher', '{\"fyear\":\"1\",\"VNo\":\"CV-37\",\"Vtype\":\"CV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-21\",\"COAID\":\"5020202\",\"Narration\":\"\",\"chequeNo\":0,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":0,\"Credit\":\"120\",\"RevCodde\":\"1020101\",\"subType\":\"8\",\"subCode\":\"20\",\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 07:44:09\",\"status\":0}', '2024-07-21 13:14:09', 1),
(0, '1', 'approved_vaucher_transation', 'create', '241', 'acc_transaction', 'account/accounts/isactive/CV-37/active', '{\"vid\":\"129\",\"fyear\":\"1\",\"VNo\":\"CV-37\",\"Vtype\":\"CV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-21\",\"COAID\":\"5020202\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"0.00\",\"Credit\":\"120.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"8\",\"subCode\":\"20\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 07:45:03\"}', '2024-07-21 13:15:03', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '242', 'acc_transaction', 'account/accounts/isactive/CV-37/active', '{\"vid\":\"129\",\"fyear\":\"1\",\"VNo\":\"CV-37\",\"Vtype\":\"CV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-21\",\"COAID\":\"1020101\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"120.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020202\",\"subType\":\"8\",\"subCode\":\"20\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-21 07:45:03\"}', '2024-07-21 13:15:03', 1),
(0, '1', 'approved_vaucher_transation', 'create', '1', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"1\",\"fyear\":\"1\",\"VNo\":\"DV-1\",\"Vtype\":\"DV\",\"referenceNo\":\"1\",\"VDate\":\"2024-07-27\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"4500.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-27 14:18:56\"}', '2024-07-27 14:18:56', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '2', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"1\",\"fyear\":\"1\",\"VNo\":\"DV-1\",\"Vtype\":\"DV\",\"referenceNo\":\"1\",\"VDate\":\"2024-07-27\",\"COAID\":\"1020101\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"4500.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-27 14:18:56\"}', '2024-07-27 14:18:56', 1),
(0, '1', 'approved_vaucher_transation', 'create', '3', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"2\",\"fyear\":\"1\",\"VNo\":\"CV-1\",\"Vtype\":\"CV\",\"referenceNo\":\"1000\",\"VDate\":\"2024-07-27\",\"COAID\":\"1020103\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"800.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-27 14:20:02\"}', '2024-07-27 14:20:02', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '4', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"2\",\"fyear\":\"1\",\"VNo\":\"CV-1\",\"Vtype\":\"CV\",\"referenceNo\":\"1000\",\"VDate\":\"2024-07-27\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"800.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-27 14:20:02\"}', '2024-07-27 14:20:02', 1),
(0, '1', 'approved_vaucher_transation', 'create', '5', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"3\",\"fyear\":\"1\",\"VNo\":\"JV-1\",\"Vtype\":\"JV\",\"referenceNo\":\"1000\",\"VDate\":\"2024-07-27\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-27 14:20:02\"}', '2024-07-27 14:20:02', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '6', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"3\",\"fyear\":\"1\",\"VNo\":\"JV-1\",\"Vtype\":\"JV\",\"referenceNo\":\"1000\",\"VDate\":\"2024-07-27\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-27 14:20:02\"}', '2024-07-27 14:20:02', 1),
(0, '1', 'approved_vaucher_transation', 'create', '7', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"4\",\"fyear\":\"1\",\"VNo\":\"JV-2\",\"Vtype\":\"JV\",\"referenceNo\":\"1000\",\"VDate\":\"2024-07-27\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-27 14:20:02\"}', '2024-07-27 14:20:02', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '8', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"4\",\"fyear\":\"1\",\"VNo\":\"JV-2\",\"Vtype\":\"JV\",\"referenceNo\":\"1000\",\"VDate\":\"2024-07-27\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-27 14:20:02\"}', '2024-07-27 14:20:02', 1),
(0, '1', 'debit_vaucher', 'create', 'DV-2', 'acc_vaucher', 'account/accounts/store_debit_voucher', '{\"fyear\":\"1\",\"VNo\":\"DV-2\",\"Vtype\":\"DV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-27\",\"COAID\":\"5020202\",\"Narration\":\"\",\"chequeNo\":\"1\",\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"Testing\",\"Debit\":\"800\",\"Credit\":0,\"RevCodde\":\"1020103\",\"subType\":\"8\",\"subCode\":\"19\",\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-07-27 08:52:47\",\"status\":0}', '2024-07-27 14:22:47', 1),
(0, '1', 'approved_vaucher_transation', 'create', '9', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"6\",\"fyear\":\"1\",\"VNo\":\"CV-2\",\"Vtype\":\"CV\",\"referenceNo\":\"1001\",\"VDate\":\"2024-08-03\",\"COAID\":\"1020103\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"200.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-03 09:01:57\"}', '2024-08-03 09:01:57', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '10', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"6\",\"fyear\":\"1\",\"VNo\":\"CV-2\",\"Vtype\":\"CV\",\"referenceNo\":\"1001\",\"VDate\":\"2024-08-03\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"200.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-03 09:01:57\"}', '2024-08-03 09:01:57', 1),
(0, '1', 'approved_vaucher_transation', 'create', '11', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"7\",\"fyear\":\"1\",\"VNo\":\"CV-3\",\"Vtype\":\"CV\",\"referenceNo\":\"1001\",\"VDate\":\"2024-08-03\",\"COAID\":\"1020101\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"600.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-03 09:01:57\"}', '2024-08-03 09:01:57', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '12', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"7\",\"fyear\":\"1\",\"VNo\":\"CV-3\",\"Vtype\":\"CV\",\"referenceNo\":\"1001\",\"VDate\":\"2024-08-03\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"600.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-03 09:01:57\"}', '2024-08-03 09:01:57', 1),
(0, '1', 'approved_vaucher_transation', 'create', '13', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"8\",\"fyear\":\"1\",\"VNo\":\"JV-3\",\"Vtype\":\"JV\",\"referenceNo\":\"1001\",\"VDate\":\"2024-08-03\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-03 09:01:57\"}', '2024-08-03 09:01:57', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '14', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"8\",\"fyear\":\"1\",\"VNo\":\"JV-3\",\"Vtype\":\"JV\",\"referenceNo\":\"1001\",\"VDate\":\"2024-08-03\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-03 09:01:57\"}', '2024-08-03 09:01:57', 1),
(0, '1', 'approved_vaucher_transation', 'create', '15', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"9\",\"fyear\":\"1\",\"VNo\":\"JV-4\",\"Vtype\":\"JV\",\"referenceNo\":\"1001\",\"VDate\":\"2024-08-03\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-03 09:01:57\"}', '2024-08-03 09:01:57', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '16', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"9\",\"fyear\":\"1\",\"VNo\":\"JV-4\",\"Vtype\":\"JV\",\"referenceNo\":\"1001\",\"VDate\":\"2024-08-03\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-03 09:01:57\"}', '2024-08-03 09:01:57', 1),
(0, '1', 'approved_vaucher_transation', 'create', '17', 'acc_transaction', 'account/accounts/isactive/DV-2/active', '{\"vid\":\"5\",\"fyear\":\"1\",\"VNo\":\"DV-2\",\"Vtype\":\"DV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-27\",\"COAID\":\"5020202\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Testing\",\"Debit\":\"800.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"8\",\"subCode\":\"19\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-03 03:32:45\"}', '2024-08-03 09:02:45', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '18', 'acc_transaction', 'account/accounts/isactive/DV-2/active', '{\"vid\":\"5\",\"fyear\":\"1\",\"VNo\":\"DV-2\",\"Vtype\":\"DV\",\"referenceNo\":\"0\",\"VDate\":\"2024-07-27\",\"COAID\":\"1020103\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Testing\",\"Debit\":\"0.00\",\"Credit\":\"800.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020202\",\"subType\":\"8\",\"subCode\":\"19\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-03 03:32:45\"}', '2024-08-03 09:02:45', 1),
(0, '1', 'approved_vaucher_transation', 'create', '19', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"10\",\"fyear\":\"1\",\"VNo\":\"DV-3\",\"Vtype\":\"DV\",\"referenceNo\":\"2\",\"VDate\":\"2024-08-05\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"20000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 11:43:32\"}', '2024-08-05 11:43:32', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '20', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"10\",\"fyear\":\"1\",\"VNo\":\"DV-3\",\"Vtype\":\"DV\",\"referenceNo\":\"2\",\"VDate\":\"2024-08-05\",\"COAID\":\"1020101\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"20000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 11:43:32\"}', '2024-08-05 11:43:32', 1),
(0, '1', 'approved_vaucher_transation', 'create', '21', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"11\",\"fyear\":\"1\",\"VNo\":\"CV-4\",\"Vtype\":\"CV\",\"referenceNo\":\"1002\",\"VDate\":\"2024-08-05\",\"COAID\":\"1020101\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"800.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 18:34:20\"}', '2024-08-05 18:34:20', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '22', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"11\",\"fyear\":\"1\",\"VNo\":\"CV-4\",\"Vtype\":\"CV\",\"referenceNo\":\"1002\",\"VDate\":\"2024-08-05\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"800.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 18:34:20\"}', '2024-08-05 18:34:20', 1),
(0, '1', 'approved_vaucher_transation', 'create', '23', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"12\",\"fyear\":\"1\",\"VNo\":\"JV-5\",\"Vtype\":\"JV\",\"referenceNo\":\"1002\",\"VDate\":\"2024-08-05\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"450.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 18:34:20\"}', '2024-08-05 18:34:20', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '24', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"12\",\"fyear\":\"1\",\"VNo\":\"JV-5\",\"Vtype\":\"JV\",\"referenceNo\":\"1002\",\"VDate\":\"2024-08-05\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"450.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 18:34:20\"}', '2024-08-05 18:34:20', 1),
(0, '1', 'approved_vaucher_transation', 'create', '25', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"13\",\"fyear\":\"1\",\"VNo\":\"JV-6\",\"Vtype\":\"JV\",\"referenceNo\":\"1002\",\"VDate\":\"2024-08-05\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 18:34:20\"}', '2024-08-05 18:34:20', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '26', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"13\",\"fyear\":\"1\",\"VNo\":\"JV-6\",\"Vtype\":\"JV\",\"referenceNo\":\"1002\",\"VDate\":\"2024-08-05\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 18:34:20\"}', '2024-08-05 18:34:20', 1),
(0, '1', 'credit_vaucher', 'create', 'CV-5', 'acc_vaucher', 'account/accounts/store_credit_voucher', '{\"fyear\":\"1\",\"VNo\":\"CV-5\",\"Vtype\":\"CV\",\"referenceNo\":\"0\",\"VDate\":\"2024-08-05\",\"COAID\":\"5020202\",\"Narration\":\"\",\"chequeNo\":3,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":0,\"Credit\":\"1002\",\"RevCodde\":\"1020103\",\"subType\":\"8\",\"subCode\":\"19\",\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 14:16:01\",\"status\":0}', '2024-08-05 19:46:01', 1),
(0, '1', 'approved_vaucher_transation', 'create', '27', 'acc_transaction', 'account/accounts/isactive/CV-5/active', '{\"vid\":\"14\",\"fyear\":\"1\",\"VNo\":\"CV-5\",\"Vtype\":\"CV\",\"referenceNo\":\"0\",\"VDate\":\"2024-08-05\",\"COAID\":\"5020202\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"0.00\",\"Credit\":\"1002.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"8\",\"subCode\":\"19\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 14:16:24\"}', '2024-08-05 19:46:24', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '28', 'acc_transaction', 'account/accounts/isactive/CV-5/active', '{\"vid\":\"14\",\"fyear\":\"1\",\"VNo\":\"CV-5\",\"Vtype\":\"CV\",\"referenceNo\":\"0\",\"VDate\":\"2024-08-05\",\"COAID\":\"1020103\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"1002.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020202\",\"subType\":\"8\",\"subCode\":\"19\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 14:16:24\"}', '2024-08-05 19:46:24', 1),
(0, '1', 'debit_vaucher', 'create', 'DV-4', 'acc_vaucher', 'account/accounts/store_debit_voucher', '{\"fyear\":\"1\",\"VNo\":\"DV-4\",\"Vtype\":\"DV\",\"referenceNo\":\"0\",\"VDate\":\"2024-08-05\",\"COAID\":\"1020803\",\"Narration\":\"\",\"chequeNo\":\"3\",\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":\"1002\",\"Credit\":0,\"RevCodde\":\"1020103\",\"subType\":\"7\",\"subCode\":\"18\",\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 14:18:50\",\"status\":0}', '2024-08-05 19:48:50', 1),
(0, '1', 'approved_vaucher_transation', 'create', '29', 'acc_transaction', 'account/accounts/bulk_voucher_approve', '{\"vid\":\"15\",\"fyear\":\"1\",\"VNo\":\"DV-4\",\"Vtype\":\"DV\",\"referenceNo\":\"0\",\"VDate\":\"2024-08-05\",\"COAID\":\"1020803\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"1002.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"7\",\"subCode\":\"18\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 14:20:09\"}', '2024-08-05 19:50:09', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '30', 'acc_transaction', 'account/accounts/bulk_voucher_approve', '{\"vid\":\"15\",\"fyear\":\"1\",\"VNo\":\"DV-4\",\"Vtype\":\"DV\",\"referenceNo\":\"0\",\"VDate\":\"2024-08-05\",\"COAID\":\"1020103\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"0.00\",\"Credit\":\"1002.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020803\",\"subType\":\"7\",\"subCode\":\"18\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 14:20:09\"}', '2024-08-05 19:50:09', 1),
(0, '1', 'contra_vaucher', 'create', 'CT-1', 'acc_vaucher', 'account/accounts/store_contra_voucher', '{\"fyear\":\"1\",\"VNo\":\"CT-1\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-08-05\",\"COAID\":\"1020501\",\"Narration\":\"\",\"chequeNo\":\"2\",\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":\"200\",\"Credit\":0,\"RevCodde\":\"1020103\",\"subType\":1,\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 14:21:59\",\"status\":0}', '2024-08-05 19:51:59', 1),
(0, '1', 'approved_vaucher_transation', 'create', '31', 'acc_transaction', 'account/accounts/isactive/CT-1/active', '{\"vid\":\"16\",\"fyear\":\"1\",\"VNo\":\"CT-1\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-08-05\",\"COAID\":\"1020501\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"200.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020103\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 14:22:21\"}', '2024-08-05 19:52:21', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '32', 'acc_transaction', 'account/accounts/isactive/CT-1/active', '{\"vid\":\"16\",\"fyear\":\"1\",\"VNo\":\"CT-1\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2024-08-05\",\"COAID\":\"1020103\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"\",\"Debit\":\"0.00\",\"Credit\":\"200.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020501\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-05 14:22:21\"}', '2024-08-05 19:52:21', 1),
(0, '1', 'approved_vaucher_transation', 'create', '33', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"17\",\"fyear\":\"1\",\"VNo\":\"CV-6\",\"Vtype\":\"CV\",\"referenceNo\":\"1003\",\"VDate\":\"2024-08-07\",\"COAID\":\"1020101\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"1000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-07 11:04:13\"}', '2024-08-07 11:04:13', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '34', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"17\",\"fyear\":\"1\",\"VNo\":\"CV-6\",\"Vtype\":\"CV\",\"referenceNo\":\"1003\",\"VDate\":\"2024-08-07\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"1000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-07 11:04:13\"}', '2024-08-07 11:04:13', 1),
(0, '1', 'approved_vaucher_transation', 'create', '35', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"18\",\"fyear\":\"1\",\"VNo\":\"JV-7\",\"Vtype\":\"JV\",\"referenceNo\":\"1003\",\"VDate\":\"2024-08-07\",\"COAID\":\"1020801\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"600.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"3\",\"subCode\":\"2\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-07 11:04:13\"}', '2024-08-07 11:04:13', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '36', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"18\",\"fyear\":\"1\",\"VNo\":\"JV-7\",\"Vtype\":\"JV\",\"referenceNo\":\"1003\",\"VDate\":\"2024-08-07\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"600.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020801\",\"subType\":\"3\",\"subCode\":\"2\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-07 11:04:13\"}', '2024-08-07 11:04:13', 1),
(0, '1', 'approved_vaucher_transation', 'create', '37', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"19\",\"fyear\":\"1\",\"VNo\":\"JV-8\",\"Vtype\":\"JV\",\"referenceNo\":\"1003\",\"VDate\":\"2024-08-07\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"900.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-07 11:04:13\"}', '2024-08-07 11:04:13', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '38', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"19\",\"fyear\":\"1\",\"VNo\":\"JV-8\",\"Vtype\":\"JV\",\"referenceNo\":\"1003\",\"VDate\":\"2024-08-07\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"900.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-07 11:04:13\"}', '2024-08-07 11:04:13', 1),
(0, '1', 'approved_vaucher_transation', 'create', '39', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"20\",\"fyear\":\"1\",\"VNo\":\"JV-9\",\"Vtype\":\"JV\",\"referenceNo\":\"1003\",\"VDate\":\"2024-08-07\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-07 11:04:13\"}', '2024-08-07 11:04:13', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '40', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"20\",\"fyear\":\"1\",\"VNo\":\"JV-9\",\"Vtype\":\"JV\",\"referenceNo\":\"1003\",\"VDate\":\"2024-08-07\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-07 11:04:13\"}', '2024-08-07 11:04:13', 1),
(0, '1', 'approved_vaucher_transation', 'create', '41', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"21\",\"fyear\":\"1\",\"VNo\":\"CV-7\",\"Vtype\":\"CV\",\"referenceNo\":\"1004\",\"VDate\":\"2024-08-07\",\"COAID\":\"1020101\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"1600.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-07 11:04:38\"}', '2024-08-07 11:04:38', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '42', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"21\",\"fyear\":\"1\",\"VNo\":\"CV-7\",\"Vtype\":\"CV\",\"referenceNo\":\"1004\",\"VDate\":\"2024-08-07\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"1600.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-07 11:04:38\"}', '2024-08-07 11:04:38', 1),
(0, '1', 'approved_vaucher_transation', 'create', '43', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"22\",\"fyear\":\"1\",\"VNo\":\"JV-10\",\"Vtype\":\"JV\",\"referenceNo\":\"1004\",\"VDate\":\"2024-08-07\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"900.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-07 11:04:38\"}', '2024-08-07 11:04:38', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '44', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"22\",\"fyear\":\"1\",\"VNo\":\"JV-10\",\"Vtype\":\"JV\",\"referenceNo\":\"1004\",\"VDate\":\"2024-08-07\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"900.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-07 11:04:38\"}', '2024-08-07 11:04:38', 1),
(0, '1', 'approved_vaucher_transation', 'create', '45', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"23\",\"fyear\":\"1\",\"VNo\":\"JV-11\",\"Vtype\":\"JV\",\"referenceNo\":\"1004\",\"VDate\":\"2024-08-07\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-07 11:04:38\"}', '2024-08-07 11:04:38', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '46', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"23\",\"fyear\":\"1\",\"VNo\":\"JV-11\",\"Vtype\":\"JV\",\"referenceNo\":\"1004\",\"VDate\":\"2024-08-07\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-07 11:04:38\"}', '2024-08-07 11:04:38', 1),
(0, '1', 'approved_vaucher_transation', 'create', '47', 'acc_transaction', 'account/accounts/create_customer_receive', '{\"vid\":\"24\",\"fyear\":\"1\",\"VNo\":\"CV-8\",\"Vtype\":\"CV\",\"referenceNo\":\"1003\",\"VDate\":\"2024-08-11\",\"COAID\":\"1020801\",\"Narration\":\"Sales Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Due Voucher for Customer\",\"Debit\":\"0.00\",\"Credit\":\"100.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"3\",\"subCode\":\"2\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-11 17:37:45\"}', '2024-08-11 17:37:45', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '48', 'acc_transaction', 'account/accounts/create_customer_receive', '{\"vid\":\"24\",\"fyear\":\"1\",\"VNo\":\"CV-8\",\"Vtype\":\"CV\",\"referenceNo\":\"1003\",\"VDate\":\"2024-08-11\",\"COAID\":\"1020101\",\"Narration\":\"Sales Due Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Due Voucher for Customer\",\"Debit\":\"100.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020801\",\"subType\":\"3\",\"subCode\":\"2\",\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-11 17:37:45\"}', '2024-08-11 17:37:45', 1),
(0, '1', 'approved_vaucher_transation', 'create', '49', 'acc_transaction', 'invoice/invoice/bdtask_manual_possales_insert', '{\"vid\":\"25\",\"fyear\":\"1\",\"VNo\":\"CV-9\",\"Vtype\":\"CV\",\"referenceNo\":\"1005\",\"VDate\":\"2024-08-11\",\"COAID\":\"1020101\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"550000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-11 21:34:31\"}', '2024-08-11 21:34:31', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '50', 'acc_transaction', 'invoice/invoice/bdtask_manual_possales_insert', '{\"vid\":\"25\",\"fyear\":\"1\",\"VNo\":\"CV-9\",\"Vtype\":\"CV\",\"referenceNo\":\"1005\",\"VDate\":\"2024-08-11\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"550000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-11 21:34:31\"}', '2024-08-11 21:34:31', 1),
(0, '1', 'approved_vaucher_transation', 'create', '51', 'acc_transaction', 'invoice/invoice/bdtask_manual_possales_insert', '{\"vid\":\"26\",\"fyear\":\"1\",\"VNo\":\"JV-12\",\"Vtype\":\"JV\",\"referenceNo\":\"1005\",\"VDate\":\"2024-08-11\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-11 21:34:31\"}', '2024-08-11 21:34:31', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '52', 'acc_transaction', 'invoice/invoice/bdtask_manual_possales_insert', '{\"vid\":\"26\",\"fyear\":\"1\",\"VNo\":\"JV-12\",\"Vtype\":\"JV\",\"referenceNo\":\"1005\",\"VDate\":\"2024-08-11\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-11 21:34:31\"}', '2024-08-11 21:34:31', 1),
(0, '1', 'approved_vaucher_transation', 'create', '53', 'acc_transaction', 'invoice/invoice/bdtask_manual_possales_insert', '{\"vid\":\"27\",\"fyear\":\"1\",\"VNo\":\"JV-13\",\"Vtype\":\"JV\",\"referenceNo\":\"1005\",\"VDate\":\"2024-08-11\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-11 21:34:31\"}', '2024-08-11 21:34:31', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '54', 'acc_transaction', 'invoice/invoice/bdtask_manual_possales_insert', '{\"vid\":\"27\",\"fyear\":\"1\",\"VNo\":\"JV-13\",\"Vtype\":\"JV\",\"referenceNo\":\"1005\",\"VDate\":\"2024-08-11\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-11 21:34:31\"}', '2024-08-11 21:34:31', 1),
(0, '1', 'approved_vaucher_transation', 'create', '55', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"28\",\"fyear\":\"1\",\"VNo\":\"DV-5\",\"Vtype\":\"DV\",\"referenceNo\":\"3\",\"VDate\":\"2024-08-13\",\"COAID\":\"1020401\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"400000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-13 22:31:11\"}', '2024-08-13 22:31:11', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '56', 'acc_transaction', 'purchase/purchase/bdtask_save_purchase', '{\"vid\":\"28\",\"fyear\":\"1\",\"VNo\":\"DV-5\",\"Vtype\":\"DV\",\"referenceNo\":\"3\",\"VDate\":\"2024-08-13\",\"COAID\":\"1020101\",\"Narration\":\"Purchase Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Purchase Voucher for supplier\",\"Debit\":\"0.00\",\"Credit\":\"400000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-13 22:31:11\"}', '2024-08-13 22:31:11', 1),
(0, '1', 'approved_vaucher_transation', 'create', '57', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"29\",\"fyear\":\"1\",\"VNo\":\"CV-10\",\"Vtype\":\"CV\",\"referenceNo\":\"1006\",\"VDate\":\"2024-08-13\",\"COAID\":\"1020101\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"600000.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"3010301\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-13 22:32:46\"}', '2024-08-13 22:32:46', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '58', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"29\",\"fyear\":\"1\",\"VNo\":\"CV-10\",\"Vtype\":\"CV\",\"referenceNo\":\"1006\",\"VDate\":\"2024-08-13\",\"COAID\":\"3010301\",\"Narration\":\"Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"600000.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-13 22:32:46\"}', '2024-08-13 22:32:46', 1),
(0, '1', 'approved_vaucher_transation', 'create', '59', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"30\",\"fyear\":\"1\",\"VNo\":\"JV-14\",\"Vtype\":\"JV\",\"referenceNo\":\"1006\",\"VDate\":\"2024-08-13\",\"COAID\":\"4010101\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"150675.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"1020401\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-13 22:32:46\"}', '2024-08-13 22:32:46', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '60', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"30\",\"fyear\":\"1\",\"VNo\":\"JV-14\",\"Vtype\":\"JV\",\"referenceNo\":\"1006\",\"VDate\":\"2024-08-13\",\"COAID\":\"1020401\",\"Narration\":\"Sales cost of goods Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Sales cost of goods Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"150675.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4010101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-13 22:32:46\"}', '2024-08-13 22:32:46', 1),
(0, '1', 'approved_vaucher_transation', 'create', '61', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"31\",\"fyear\":\"1\",\"VNo\":\"JV-15\",\"Vtype\":\"JV\",\"referenceNo\":\"1006\",\"VDate\":\"2024-08-13\",\"COAID\":\"4021101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"5020101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-13 22:32:46\"}', '2024-08-13 22:32:46', 1),
(0, '1', 'approved_vaucher_reversetransa', 'create', '62', 'acc_transaction', 'invoice/invoice/bdtask_manual_sales_insert', '{\"vid\":\"31\",\"fyear\":\"1\",\"VNo\":\"JV-15\",\"Vtype\":\"JV\",\"referenceNo\":\"1006\",\"VDate\":\"2024-08-13\",\"COAID\":\"5020101\",\"Narration\":\"Tax for Sales Voucher\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":\"0\",\"ledgerComment\":\"Tax for Sales Voucher for customer\",\"Debit\":\"0.00\",\"Credit\":\"0.00\",\"StoreID\":0,\"IsPosted\":1,\"RevCodde\":\"4021101\",\"subType\":\"1\",\"subCode\":null,\"IsAppove\":1,\"CreateBy\":\"1\",\"CreateDate\":\"2024-08-13 22:32:46\"}', '2024-08-13 22:32:46', 1),
(0, '1', 'contra_vaucher', 'create', 'CT-1', 'acc_vaucher', 'account/accounts/store_contra_voucher', '{\"fyear\":0,\"VNo\":\"CT-1\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2026-05-10\",\"COAID\":\"2\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":\"100\",\"Credit\":0,\"RevCodde\":\"1\",\"subType\":1,\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2026-05-10 19:20:48\",\"status\":0}', '2026-05-11 00:50:48', 1),
(0, '1', 'debit_vaucher', 'create', 'DV-1', 'acc_vaucher', 'account/accounts/store_debit_voucher', '{\"fyear\":0,\"VNo\":\"DV-1\",\"Vtype\":\"DV\",\"referenceNo\":null,\"VDate\":\"2026-05-11\",\"COAID\":\"0\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":\"50\",\"Credit\":0,\"RevCodde\":\"1\",\"subType\":\"1\",\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2026-05-10 19:44:17\",\"status\":0}', '2026-05-11 01:14:17', 1),
(0, '1', 'debit_vaucher', 'create', 'DV-1', 'acc_vaucher', 'account/accounts/store_debit_voucher', '{\"fyear\":0,\"VNo\":\"DV-1\",\"Vtype\":\"DV\",\"referenceNo\":null,\"VDate\":\"2026-05-11\",\"COAID\":\"0\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":\"60\",\"Credit\":0,\"RevCodde\":\"1\",\"subType\":\"\",\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2026-05-10 19:44:17\",\"status\":0}', '2026-05-11 01:14:17', 1),
(0, '1', 'credit_vaucher', 'create', 'CV-1', 'acc_vaucher', 'account/accounts/store_credit_voucher', '{\"fyear\":0,\"VNo\":\"CV-1\",\"Vtype\":\"CV\",\"referenceNo\":null,\"VDate\":\"2026-05-11\",\"COAID\":\"0\",\"Narration\":\"\",\"chequeNo\":0,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":0,\"Credit\":\"40\",\"RevCodde\":\"1\",\"subType\":\"\",\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2026-05-10 19:44:43\",\"status\":0}', '2026-05-11 01:14:43', 1);
INSERT INTO `activity_logs` (`activity_id`, `user_id`, `type`, `action`, `action_id`, `table_name`, `slug`, `form_data`, `create_date`, `status`) VALUES
(0, '1', 'credit_vaucher', 'create', 'CV-1', 'acc_vaucher', 'account/accounts/store_credit_voucher', '{\"fyear\":0,\"VNo\":\"CV-1\",\"Vtype\":\"CV\",\"referenceNo\":null,\"VDate\":\"2026-05-11\",\"COAID\":\"0\",\"Narration\":\"\",\"chequeNo\":0,\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":0,\"Credit\":\"30\",\"RevCodde\":\"1\",\"subType\":\"\",\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2026-05-10 19:44:43\",\"status\":0}', '2026-05-11 01:14:43', 1),
(0, '1', 'debit_vaucher', 'create', 'DV-2', 'acc_vaucher', 'account/accounts/store_debit_voucher', '{\"fyear\":0,\"VNo\":\"DV-2\",\"Vtype\":\"DV\",\"referenceNo\":null,\"VDate\":\"2026-05-12\",\"COAID\":\"0\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":\"50\",\"Credit\":0,\"RevCodde\":\"1\",\"subType\":\"1\",\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2026-05-12 07:12:36\",\"status\":0}', '2026-05-12 12:42:36', 1),
(0, '1', 'contra_vaucher', 'create', 'CT-2', 'acc_vaucher', 'account/accounts/store_contra_voucher', '{\"fyear\":0,\"VNo\":\"CT-2\",\"Vtype\":\"CT\",\"referenceNo\":null,\"VDate\":\"2026-05-12\",\"COAID\":\"2\",\"Narration\":\"\",\"chequeNo\":\"\",\"chequeDate\":null,\"isHonour\":0,\"ledgerComment\":\"\",\"Debit\":\"100000\",\"Credit\":0,\"RevCodde\":\"1\",\"subType\":1,\"subCode\":null,\"isApproved\":0,\"CreateBy\":\"1\",\"CreateDate\":\"2026-05-12 07:18:28\",\"status\":0}', '2026-05-12 12:48:28', 1);

-- --------------------------------------------------------

--
-- Table structure for table `adj_stock`
--

CREATE TABLE `adj_stock` (
  `id` int(11) NOT NULL,
  `date` varchar(255) NOT NULL,
  `reason` varchar(2000) NOT NULL,
  `type2` varbinary(1000) NOT NULL,
  `type` varchar(255) NOT NULL,
  `stocktype` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `app_setting`
--

CREATE TABLE `app_setting` (
  `id` int(11) NOT NULL,
  `localhserver` varchar(250) DEFAULT NULL,
  `onlineserver` varchar(250) DEFAULT NULL,
  `hotspot` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `app_setting`
--

INSERT INTO `app_setting` (`id`, `localhserver`, `onlineserver`, `hotspot`) VALUES
(1, 'https://demo.bdtask.com', 'https://demo.bdtask.com', 'https://demo.bdtask.com');

-- --------------------------------------------------------

--
-- Table structure for table `audit_stock`
--

CREATE TABLE `audit_stock` (
  `id` int(11) NOT NULL,
  `product` int(11) NOT NULL,
  `date` varchar(2000) NOT NULL,
  `scenario` varchar(2000) NOT NULL,
  `incident` varchar(2000) NOT NULL,
  `pvoucher` varbinary(1000) NOT NULL,
  `voucher` varbinary(1000) NOT NULL,
  `pid` int(11) NOT NULL,
  `store` int(11) NOT NULL,
  `astockstr` varbinary(1000) NOT NULL,
  `pstockstr` varbinary(1000) NOT NULL,
  `astock` varbinary(1000) NOT NULL,
  `pstock` varbinary(1000) NOT NULL,
  `lastupdateddate` datetime NOT NULL,
  `type2` varbinary(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bank_add`
--

CREATE TABLE `bank_add` (
  `id` int(11) NOT NULL,
  `bank_id` varchar(255) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `ac_name` varchar(250) DEFAULT NULL,
  `ac_number` varchar(250) DEFAULT NULL,
  `branch` varchar(250) DEFAULT NULL,
  `signature_pic` varchar(250) DEFAULT NULL,
  `status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `barcode_sticker`
--

CREATE TABLE `barcode_sticker` (
  `product_id` varchar(255) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `price` varchar(255) NOT NULL,
  `batch_id` int(11) DEFAULT NULL,
  `batch_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branch`
--

CREATE TABLE `branch` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varbinary(1000) NOT NULL,
  `status` int(11) NOT NULL,
  `nature` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bulk_branch_details`
--

CREATE TABLE `bulk_branch_details` (
  `id` int(11) NOT NULL,
  `uploaded_id` varbinary(200) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `uploadedby` int(11) DEFAULT NULL,
  `branch_ids` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bulk_brand_details`
--

CREATE TABLE `bulk_brand_details` (
  `id` int(11) NOT NULL,
  `uploaded_id` varbinary(200) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `uploadedby` int(11) DEFAULT NULL,
  `brand_ids` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bulk_category_details`
--

CREATE TABLE `bulk_category_details` (
  `id` int(11) NOT NULL,
  `uploaded_id` varbinary(200) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `uploadedby` int(11) DEFAULT NULL,
  `category_ids` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bulk_conversionratio_details`
--

CREATE TABLE `bulk_conversionratio_details` (
  `id` int(11) NOT NULL,
  `uploaded_id` varbinary(255) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `uploadedby` int(11) DEFAULT NULL,
  `conversionratio_ids` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bulk_customer_details`
--

CREATE TABLE `bulk_customer_details` (
  `id` int(11) NOT NULL,
  `uploaded_id` varbinary(200) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `uploadedby` int(11) DEFAULT NULL,
  `customer_ids` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bulk_designation_details`
--

CREATE TABLE `bulk_designation_details` (
  `id` int(11) NOT NULL,
  `uploaded_id` varbinary(200) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `uploadedby` int(11) DEFAULT NULL,
  `designation_ids` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bulk_details`
--

CREATE TABLE `bulk_details` (
  `id` int(11) NOT NULL,
  `uploaded_id` varbinary(2000) NOT NULL,
  `date` varchar(255) NOT NULL,
  `uploadedby` int(10) NOT NULL,
  `invoices` varbinary(2000) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bulk_employee_details`
--

CREATE TABLE `bulk_employee_details` (
  `id` int(11) NOT NULL,
  `uploaded_id` varbinary(200) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `uploadedby` int(11) DEFAULT NULL,
  `employee_ids` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bulk_openingstock_details`
--

CREATE TABLE `bulk_openingstock_details` (
  `id` int(11) NOT NULL,
  `uploaded_id` varbinary(200) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `uploadedby` int(11) DEFAULT NULL,
  `openingstock_ids` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bulk_paymentmethod_details`
--

CREATE TABLE `bulk_paymentmethod_details` (
  `id` int(11) NOT NULL,
  `uploaded_id` varbinary(200) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `uploadedby` int(11) DEFAULT NULL,
  `paymentmethod_ids` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bulk_productgroup_details`
--

CREATE TABLE `bulk_productgroup_details` (
  `id` int(11) NOT NULL,
  `uploaded_id` varbinary(200) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `uploadedby` int(11) DEFAULT NULL,
  `productgroup_ids` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bulk_product_details`
--

CREATE TABLE `bulk_product_details` (
  `id` int(11) NOT NULL,
  `uploaded_id` varbinary(255) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `uploadedby` int(11) DEFAULT NULL,
  `product_ids` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bulk_service_details`
--

CREATE TABLE `bulk_service_details` (
  `id` int(11) NOT NULL,
  `uploaded_id` varbinary(200) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `uploadedby` int(11) DEFAULT NULL,
  `service_ids` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bulk_stockadjustment_details`
--

CREATE TABLE `bulk_stockadjustment_details` (
  `id` int(11) NOT NULL,
  `uploaded_id` varbinary(200) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `uploadedby` int(11) DEFAULT NULL,
  `stockadjustment_ids` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bulk_stockbatch_details`
--

CREATE TABLE `bulk_stockbatch_details` (
  `id` int(11) NOT NULL,
  `uploaded_id` varbinary(200) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `uploadedby` int(11) DEFAULT NULL,
  `stockbatch_ids` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bulk_store_details`
--

CREATE TABLE `bulk_store_details` (
  `id` int(11) NOT NULL,
  `uploaded_id` varbinary(200) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `uploadedby` int(11) DEFAULT NULL,
  `store_ids` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bulk_subcategory_details`
--

CREATE TABLE `bulk_subcategory_details` (
  `id` int(11) NOT NULL,
  `uploaded_id` varbinary(200) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `uploadedby` int(11) DEFAULT NULL,
  `subcategory_ids` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bulk_supplier_details`
--

CREATE TABLE `bulk_supplier_details` (
  `id` int(11) NOT NULL,
  `uploaded_id` varbinary(200) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `uploadedby` int(11) DEFAULT NULL,
  `supplier_ids` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bulk_unit_details`
--

CREATE TABLE `bulk_unit_details` (
  `id` int(11) NOT NULL,
  `uploaded_id` varbinary(200) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `uploadedby` int(11) DEFAULT NULL,
  `unit_ids` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bulk_upload_csv`
--

CREATE TABLE `bulk_upload_csv` (
  `id` int(11) NOT NULL,
  `batch_table` varchar(100) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `original_csv` longtext NOT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_information`
--

CREATE TABLE `company_information` (
  `company_id` int(250) NOT NULL,
  `company_name` varbinary(1000) NOT NULL,
  `email` varbinary(1000) NOT NULL,
  `address` varbinary(1000) NOT NULL,
  `mobile` varbinary(1000) NOT NULL,
  `website` varbinary(1000) NOT NULL,
  `vat_no` varbinary(1000) DEFAULT NULL,
  `cr_no` varbinary(1000) DEFAULT NULL,
  `status` int(11) NOT NULL,
  `instance_type` varchar(10) DEFAULT NULL,
  `footer_text` varbinary(1000) NOT NULL,
  `password` varbinary(1000) NOT NULL,
  `header_text` varbinary(1000) DEFAULT NULL,
  `password_enable` int(10) DEFAULT NULL,
  `theme_color` varchar(50) NOT NULL DEFAULT 'theme-azure',
  `theme_dark` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `company_information`
--


INSERT INTO `company_information` (`company_id`, `company_name`, `email`, `address`, `mobile`, `website`, `vat_no`, `cr_no`, `status`, `instance_type`, `footer_text`, `password`, `header_text`, `password_enable`, `theme_color`, `theme_dark`) VALUES
(1, 0x4d2631bede323c3a6ec1dd4cbd05ef58, 0xc07ea54c5bebf537d9734e19b80e3873, 0xd19731806ceed875382a8bab454d8c45, 0x02396e6d62bc694eb76e6ed6611cfa21, 0xc07ea54c5bebf537d9734e19b80e3873, 0xc07ea54c5bebf537d9734e19b80e3873, 0xc07ea54c5bebf537d9734e19b80e3873, 1, 'BETA', 0xa055ccf04c691f296dd9a173e766128fc958409cb093a7d574bd4c0c4750f7fc19cff27db1eb145cdd0da7270da07d74a5eb7cbc032a537b20605b6a9c25fadce10425f4d5f7561a41ed3e4806bdcf6a, 0xc07ea54c5bebf537d9734e19b80e3873, 0x4d2631bede323c3a6ec1dd4cbd05ef58, 0, 'theme-teal', 1),
(2, 0x4d2631bede323c3a6ec1dd4cbd05ef58, 0xc07ea54c5bebf537d9734e19b80e3873, 0xd19731806ceed875382a8bab454d8c45, 0x02396e6d62bc694eb76e6ed6611cfa21, 0xc07ea54c5bebf537d9734e19b80e3873, 0xc07ea54c5bebf537d9734e19b80e3873, 0xc07ea54c5bebf537d9734e19b80e3873, 1, 'BETA', 0xa055ccf04c691f296dd9a173e766128fc958409cb093a7d574bd4c0c4750f7fc19cff27db1eb145cdd0da7270da07d74a5eb7cbc032a537b20605b6a9c25fadce10425f4d5f7561a41ed3e4806bdcf6a, 0xc07ea54c5bebf537d9734e19b80e3873, 0x4d2631bede323c3a6ec1dd4cbd05ef58, 0, 'theme-azure', 0),
(3, 0x4d2631bede323c3a6ec1dd4cbd05ef58, 0xc07ea54c5bebf537d9734e19b80e3873, 0xd19731806ceed875382a8bab454d8c45, 0x02396e6d62bc694eb76e6ed6611cfa21, 0xc07ea54c5bebf537d9734e19b80e3873, 0xc07ea54c5bebf537d9734e19b80e3873, 0xc07ea54c5bebf537d9734e19b80e3873, 1, '', 0x32e817732f7735d8d7df87def0a3fe265a5720a01e608f4c51de74800719ed99a8fd0233d75b675efaa68a8009196812e9ac2a6b95bf9f00bdd8fc796d1eb00a10a511a689fef8e4a93150a0b97f48bddab3aa42552e4c8c19c2f269ae3d30e29f11b6d786364eb7d3c114095320594c, 0xc07ea54c5bebf537d9734e19b80e3873, 0xd54007983ed682cd17cb6e5dac3a5bdb, 0, 'theme-orange', 1),
(9, 0x6c2d6f0fb68fd42cd6a9549b67ad8e82, 0xc07ea54c5bebf537d9734e19b80e3873, 0x6c2d6f0fb68fd42cd6a9549b67ad8e82, 0x296f66607fac2575e472607e59ea6479, 0xc07ea54c5bebf537d9734e19b80e3873, 0xc07ea54c5bebf537d9734e19b80e3873, 0xc07ea54c5bebf537d9734e19b80e3873, 0, '', 0xc07ea54c5bebf537d9734e19b80e3873, 0xc07ea54c5bebf537d9734e19b80e3873, 0xc07ea54c5bebf537d9734e19b80e3873, 1, 'theme-azure', 0);

-- --------------------------------------------------------

--
-- Table structure for table `conversion_ratio`
--

CREATE TABLE `conversion_ratio` (
  `conversionratio_id` int(11) NOT NULL,
  `product` int(11) NOT NULL,
  `convertiontype` varchar(255) NOT NULL,
  `subunit` int(23) NOT NULL,
  `conversion_ratio` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL,
  `bd` varchar(255) NOT NULL DEFAULT '',
  `ad` varchar(255) NOT NULL DEFAULT '',
  `addigit` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `currency_tbl`
--

CREATE TABLE `currency_tbl` (
  `id` int(11) NOT NULL,
  `currency_name` varchar(50) NOT NULL,
  `icon` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `currency_tbl`
--

INSERT INTO `currency_tbl` (`id`, `currency_name`, `icon`) VALUES
(2, 'USD', '$'),
(3, 'LKR', 'LKR');

-- --------------------------------------------------------

--
-- Table structure for table `customer_information`
--

CREATE TABLE `customer_information` (
  `customer_id` bigint(20) NOT NULL,
  `customer_name` varbinary(1000) DEFAULT NULL,
  `customer_address` varbinary(1000) DEFAULT NULL,
  `address2` varbinary(1000) NOT NULL,
  `customer_mobile` varbinary(1000) DEFAULT NULL,
  `customer_email` varbinary(1000) DEFAULT NULL,
  `email_address` varbinary(1000) DEFAULT NULL,
  `contact` varbinary(1000) DEFAULT NULL,
  `phone` varbinary(1000) DEFAULT NULL,
  `fax` varchar(100) DEFAULT NULL,
  `city` text DEFAULT NULL,
  `state` text DEFAULT NULL,
  `zip` varchar(50) DEFAULT NULL,
  `country` varchar(250) DEFAULT NULL,
  `status` int(11) NOT NULL COMMENT '1=paid,2=credit',
  `create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `create_by` varchar(30) DEFAULT NULL,
  `customer_calling_name` varbinary(2000) NOT NULL,
  `customer_billing_name` varbinary(2000) NOT NULL,
  `nic_no` varbinary(2000) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `customer_information`
--

INSERT INTO `customer_information` (`customer_id`, `customer_name`, `customer_address`, `address2`, `customer_mobile`, `customer_email`, `email_address`, `contact`, `phone`, `fax`, `city`, `state`, `zip`, `country`, `status`, `create_date`, `create_by`, `customer_calling_name`, `customer_billing_name`, `nic_no`) VALUES
(1, 0xbf868df4d6f344184ae396dc1a3a89bb, 0xc07ea54c5bebf537d9734e19b80e3873, 0xc07ea54c5bebf537d9734e19b80e3873, 0xc07ea54c5bebf537d9734e19b80e3873, 0xc07ea54c5bebf537d9734e19b80e3873, 0xc07ea54c5bebf537d9734e19b80e3873, 0xc07ea54c5bebf537d9734e19b80e3873, 0xc07ea54c5bebf537d9734e19b80e3873, '', '', '', '', '', 1, '2025-10-30 12:15:46', '1', 0xbf868df4d6f344184ae396dc1a3a89bb, 0xbf868df4d6f344184ae396dc1a3a89bb, 0xc07ea54c5bebf537d9734e19b80e3873);

-- --------------------------------------------------------

--
-- Table structure for table `designation`
--

CREATE TABLE `designation` (
  `id` int(11) NOT NULL,
  `designation` varchar(150) NOT NULL,
  `details` text NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `designation`
--

INSERT INTO `designation` (`id`, `designation`, `details`, `status`) VALUES
(11, 'Test_Designation_01', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `email_config`
--

CREATE TABLE `email_config` (
  `id` int(11) NOT NULL,
  `protocol` text NOT NULL,
  `smtp_host` text NOT NULL,
  `smtp_port` text NOT NULL,
  `smtp_user` varchar(35) NOT NULL,
  `smtp_pass` varchar(35) NOT NULL,
  `mailtype` varchar(30) DEFAULT NULL,
  `isinvoice` tinyint(4) NOT NULL,
  `isservice` tinyint(4) NOT NULL,
  `isquotation` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `email_config`
--

INSERT INTO `email_config` (`id`, `protocol`, `smtp_host`, `smtp_port`, `smtp_user`, `smtp_pass`, `mailtype`, `isinvoice`, `isservice`, `isquotation`) VALUES
(1, 'ssmtp', 'ssl://ssmtp.gmail.com', '25', 'info@eholol.com', 'demo123456', 'html', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `employee_history`
--

CREATE TABLE `employee_history` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `designation` varchar(100) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `rate_type` int(11) NOT NULL,
  `hrate` float NOT NULL,
  `email` varchar(50) NOT NULL,
  `blood_group` varchar(10) NOT NULL,
  `address_line_1` text NOT NULL,
  `address_line_2` text NOT NULL,
  `state` varchar(50) NOT NULL DEFAULT '',
  `image` text DEFAULT NULL,
  `country` varchar(50) NOT NULL,
  `city` varchar(50) NOT NULL,
  `zip` varchar(50) NOT NULL,
  `status` tinyint(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `employee_history`
--

INSERT INTO `employee_history` (`id`, `first_name`, `last_name`, `designation`, `phone`, `rate_type`, `hrate`, `email`, `blood_group`, `address_line_1`, `address_line_2`, `state`, `image`, `country`, `city`, `zip`, `status`) VALUES
(1, 'N/A', '000001', '11', '', 0, 0, '', '', '', '', '', NULL, 'Sri Lanka', '', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `financial_year`
--

CREATE TABLE `financial_year` (
  `id` int(11) NOT NULL,
  `yearName` varchar(30) NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date NOT NULL,
  `isCloseReq` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 2 COMMENT '1=active, 0=closed, 2=pending',
  `created_by` int(6) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_by` int(6) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `financial_year`
--

INSERT INTO `financial_year` (`id`, `yearName`, `startDate`, `endDate`, `isCloseReq`, `status`, `created_by`, `created_date`, `updated_by`, `updated_date`) VALUES
(1, '2024', '2024-01-01', '2024-12-31', 0, 1, 1, '2024-03-04 08:01:24', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `gdn_stock`
--

CREATE TABLE `gdn_stock` (
  `id` int(11) NOT NULL,
  `gdn_id` varbinary(1000) NOT NULL,
  `date` varchar(255) NOT NULL,
  `detail` varchar(2000) NOT NULL,
  `vehicleno` varchar(2000) NOT NULL,
  `type` varchar(50) NOT NULL,
  `voucherno` int(11) NOT NULL,
  `createddate` datetime NOT NULL,
  `lastupdateddate` datetime NOT NULL,
  `type2` varbinary(2000) NOT NULL,
  `customer_id` int(10) NOT NULL,
  `employee_id` int(10) NOT NULL,
  `already` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grn_stock`
--

CREATE TABLE `grn_stock` (
  `id` int(11) NOT NULL,
  `grn_id` varbinary(1000) NOT NULL,
  `date` varchar(255) NOT NULL,
  `detail` varchar(2000) NOT NULL,
  `vehicleno` varchar(2000) NOT NULL,
  `type` varchar(50) NOT NULL,
  `voucherno` int(11) NOT NULL,
  `createddate` datetime NOT NULL,
  `lastupdateddate` datetime NOT NULL,
  `type2` varbinary(255) NOT NULL,
  `supplier_id` int(10) NOT NULL,
  `already` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `id` int(11) NOT NULL,
  `invoice_id` bigint(20) NOT NULL,
  `customer_id` bigint(20) NOT NULL,
  `date` varchar(50) DEFAULT NULL,
  `total_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `due_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `prevous_due` decimal(20,2) NOT NULL DEFAULT 0.00,
  `shipping_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `invoice` bigint(20) NOT NULL,
  `offline_invoice_no` bigint(20) DEFAULT NULL,
  `invoice_discount` decimal(10,2) DEFAULT 0.00 COMMENT 'invoice discount',
  `total_discount` decimal(10,2) DEFAULT 0.00 COMMENT 'total invoice discount',
  `total_vat_amnt` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'total invoice vat',
  `total_tax` decimal(10,2) DEFAULT 0.00,
  `ret_adjust_amnt` decimal(10,2) DEFAULT NULL,
  `returnable_amount` decimal(10,2) DEFAULT NULL,
  `sales_by` varchar(50) NOT NULL,
  `delivery_note` text DEFAULT NULL,
  `invoice_details` text NOT NULL,
  `is_credit` tinyint(4) DEFAULT NULL,
  `is_fixed` int(11) NOT NULL DEFAULT 0,
  `is_dynamic` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL,
  `bank_id` varchar(30) DEFAULT NULL,
  `payment_type` int(11) NOT NULL,
  `is_online` int(11) NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_details`
--

CREATE TABLE `invoice_details` (
  `id` int(11) NOT NULL,
  `invoice_details_id` varchar(100) NOT NULL,
  `invoice_id` varchar(100) NOT NULL,
  `ret_invoice_id` bigint(20) DEFAULT NULL,
  `product_id` varchar(30) NOT NULL,
  `serial_no` varchar(30) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `quantity` decimal(10,2) DEFAULT NULL,
  `rate` decimal(10,2) DEFAULT NULL,
  `batch_id` varchar(30) DEFAULT NULL,
  `supplier_rate` float DEFAULT NULL,
  `total_price` decimal(12,2) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `discount_per` varchar(15) DEFAULT NULL,
  `vat_amnt` decimal(10,2) DEFAULT NULL,
  `vat_amnt_per` varchar(15) DEFAULT NULL,
  `tax` decimal(10,2) DEFAULT NULL,
  `paid_amount` decimal(12,0) DEFAULT NULL,
  `due_amount` decimal(10,2) DEFAULT NULL,
  `status` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `labelprint`
--

CREATE TABLE `labelprint` (
  `id` int(50) NOT NULL,
  `no_of_labels` varchar(255) NOT NULL,
  `product_code` varchar(255) NOT NULL,
  `product` varchar(1000) NOT NULL,
  `category` varchar(255) NOT NULL,
  `price` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `language`
--

CREATE TABLE `language` (
  `id` int(10) UNSIGNED NOT NULL,
  `phrase` text NOT NULL,
  `english` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `language`
--

INSERT INTO `language` (`id`, `phrase`, `english`) VALUES
(1, 'user_profile', 'User Profile'),
(2, 'setting', 'Settings & Preferences'),
(3, 'language', 'Language'),
(4, 'manage_users', 'Manage User'),
(5, 'add_user', 'Add User'),
(6, 'manage_company', 'Manage Company'),
(7, 'web_settings', 'Software Settings'),
(8, 'manage_accounts', 'Manage Accounts'),
(9, 'create_accounts', 'Create Account'),
(10, 'manage_bank', 'Manage Bank'),
(11, 'add_new_bank', 'Add New Bank'),
(12, 'settings', 'Preferences'),
(13, 'closing_report', 'Cashier Register Report'),
(14, 'closing', 'Closing'),
(15, 'cheque_manager', 'Cheque Manager'),
(16, 'accounts_summary', 'Accounts Summary'),
(17, 'expense', 'Expense'),
(18, 'income', 'Income'),
(19, 'accounts', 'Accounts'),
(20, 'stock_report', 'Comprehensive Stock Report'),
(21, 'stock', 'Inventory'),
(22, 'pos_invoice', 'POS Sale'),
(23, 'manage_invoice', 'Manage Sale'),
(24, 'new_invoice', 'New Sale'),
(25, 'invoice', 'Sales of Product'),
(26, 'manage_purchase', 'Manage Purchase'),
(27, 'add_purchase', 'New Purchase'),
(28, 'purchase', 'Purchase'),
(29, 'paid_customer', 'Paid Customer'),
(30, 'manage_customer', 'Manage Customer'),
(31, 'add_customer', 'Add Customer'),
(32, 'customer', 'Customer'),
(33, 'supplier_payment_actual', 'Supplier Payment Ledger'),
(34, 'supplier_sales_summary', 'Supplier Sales Summary'),
(35, 'supplier_sales_details', 'Supplier Sales Details'),
(36, 'supplier_ledger', 'Supplier Ledger'),
(37, 'manage_supplier', 'Supplier List'),
(38, 'add_supplier', 'Add Supplier'),
(39, 'supplier', 'Supplier'),
(40, 'product_statement', 'Product Statement'),
(41, 'manage_product', 'Product List'),
(42, 'add_product', 'Add Product'),
(43, 'product', 'Product'),
(44, 'manage_category', 'Manage Category'),
(45, 'add_category', 'Add Category'),
(46, 'category', 'Category'),
(47, 'sales_report_product_wise', 'Sales Report (Product Wise)'),
(48, 'purchase_report', 'Purchase Report (Invoice Wise)'),
(49, 'sales_report', 'Sales Report (Invoice Wise)'),
(50, 'todays_report', 'Daily Sales & Purchase Report'),
(51, 'report', 'Reports'),
(52, 'dashboard', 'Dashboard'),
(53, 'online', 'Online'),
(54, 'logout', 'Logout'),
(55, 'change_password', 'Change Password'),
(56, 'total_purchase', 'Total Purchase'),
(57, 'total_amount', 'Total Amount'),
(58, 'supplier_name', 'Supplier Name'),
(59, 'invoice_no', 'Invoice No'),
(60, 'purchase_date', 'Purchase Date'),
(61, 'todays_purchase_report', 'Todays Purchase Report'),
(62, 'total_sales', 'Total Sales'),
(63, 'customer_name', 'Customer Name'),
(64, 'sales_date', 'Sales Date'),
(65, 'todays_sales_report', 'Todays Sales Report'),
(66, 'home', 'Home'),
(67, 'todays_sales_and_purchase_report', 'Todays sales and purchase report'),
(68, 'total_ammount', 'Total Amount'),
(69, 'rate', 'Rate'),
(70, 'product_model', 'Product Model'),
(71, 'product_name', 'Product Name'),
(72, 'search', 'Search'),
(73, 'end_date', 'End Date'),
(74, 'start_date', 'Start Date'),
(75, 'total_purchase_report', 'Total Purchase Report'),
(76, 'total_sales_report', 'Total Sales Report'),
(77, 'total_seles', 'Total Sales'),
(78, 'all_stock_report', 'All Stock Report'),
(79, 'search_by_product', 'Search By Product'),
(80, 'date', 'Date'),
(81, 'print', 'Print'),
(82, 'stock_date', 'Stock Date'),
(83, 'print_date', 'Print Date'),
(84, 'sales', 'Sales'),
(85, 'price', 'Price'),
(86, 'sl', 'SL.'),
(87, 'add_new_category', 'Add new category'),
(88, 'category_name', 'Category Name'),
(89, 'save', 'Save'),
(90, 'delete', 'Delete'),
(91, 'update', 'Update'),
(92, 'action', 'Action'),
(93, 'manage_your_category', 'Manage your category '),
(94, 'category_edit', 'Category Edit'),
(95, 'status', 'Status'),
(96, 'active', 'Active'),
(97, 'inactive', 'Inactive'),
(98, 'save_changes', 'Save Changes'),
(99, 'save_and_add_another', 'Save And Add Another'),
(100, 'model', 'Model'),
(101, 'supplier_price', 'Supplier Price'),
(102, 'sell_price', 'Last Sale Price'),
(103, 'image', 'Image'),
(104, 'select_one', 'Select One'),
(105, 'details', 'Details'),
(106, 'new_product', 'New Product'),
(107, 'add_new_product', 'Add new product'),
(108, 'barcode', 'Barcode'),
(109, 'qr_code', 'Qr-Code'),
(110, 'product_details', 'Product Details'),
(111, 'manage_your_product', 'Manage your product'),
(112, 'product_edit', 'Product Edit'),
(113, 'edit_your_product', 'Edit your product'),
(114, 'cancel', 'Cancel'),
(115, 'incl_vat', 'Incl. Vat'),
(116, 'money', 'TK'),
(117, 'grand_total', 'Grand Total'),
(118, 'quantity', 'Qnty'),
(119, 'product_report', 'Product Report'),
(120, 'product_sales_and_purchase_report', 'Product sales and purchase report'),
(121, 'previous_stock', 'Previous Stock'),
(122, 'out', 'Out'),
(123, 'in', 'In'),
(124, 'to', 'To'),
(125, 'previous_balance', 'Previous Balance'),
(126, 'customer_address', 'Customer Address'),
(127, 'customer_mobile', 'Customer Mobile'),
(128, 'customer_email', 'Customer Email'),
(129, 'add_new_customer', 'Add new customer'),
(130, 'balance', 'Balance'),
(131, 'mobile', 'Mobile'),
(132, 'address', 'Address'),
(133, 'manage_your_customer', 'Manage your customer'),
(134, 'customer_edit', 'Customer Edit'),
(135, 'paid_customer_list', 'Paid Customer List'),
(136, 'ammount', 'Amount'),
(137, 'customer_ledger', 'Customer Ledger'),
(138, 'manage_customer_ledger', 'Manage Customer Ledger'),
(139, 'customer_information', 'Customer Information'),
(140, 'debit_ammount', 'Debit Amount'),
(141, 'credit_ammount', 'Credit Amount'),
(142, 'balance_ammount', 'Balance Amount'),
(143, 'receipt_no', 'Receipt NO'),
(144, 'description', 'Description'),
(145, 'debit', 'Debit'),
(146, 'credit', 'Credit'),
(147, 'item_information', 'Item Information'),
(148, 'total', 'Total'),
(149, 'please_select_supplier', 'Please Select Supplier'),
(150, 'submit', 'Submit'),
(151, 'submit_and_add_another', 'Submit And Add Another One'),
(152, 'add_new_item', 'Add New Item'),
(153, 'manage_your_purchase', 'Manage your purchase'),
(154, 'purchase_edit', 'Purchase Edit'),
(155, 'purchase_ledger', 'Purchase Ledger'),
(156, 'invoice_information', 'Sale Information'),
(157, 'paid_ammount', 'Paid Amount'),
(158, 'discount', 'Dis./Pcs.'),
(159, 'save_and_paid', 'Save And Paid'),
(160, 'payee_name', 'Payee Name'),
(161, 'manage_your_invoice', 'Manage your Sale'),
(162, 'invoice_edit', 'Sale Edit'),
(163, 'new_pos_invoice', 'New POS Sale'),
(164, 'add_new_pos_invoice', 'Add new pos Sale'),
(165, 'product_id', 'Product ID'),
(166, 'paid_amount', 'Paid Amount'),
(167, 'authorised_by', 'Authorised By'),
(168, 'checked_by', 'Checked By'),
(169, 'received_by', 'Received By'),
(170, 'prepared_by', 'Prepared By'),
(171, 'memo_no', 'Memo No'),
(172, 'website', 'Website'),
(173, 'email', 'Email'),
(174, 'invoice_details', 'Sale Details'),
(175, 'reset', 'Reset'),
(176, 'payment_account', 'Payment Account'),
(177, 'bank_name', 'Bank Name'),
(178, 'cheque_or_pay_order_no', 'Cheque/Pay Order No'),
(179, 'payment_type', 'Payment Type'),
(180, 'payment_from', 'Payment From'),
(181, 'payment_date', 'Payment Date'),
(182, 'add_income', 'Add Income'),
(183, 'cash', 'Cash'),
(184, 'cheque', 'Cheque'),
(185, 'pay_order', 'Pay Order'),
(186, 'payment_to', 'Payment To'),
(187, 'total_outflow_ammount', 'Total Expense Amount'),
(188, 'transections', 'Transections'),
(189, 'accounts_name', 'Accounts Name'),
(190, 'outflow_report', 'Expense Report'),
(191, 'inflow_report', 'Income Report'),
(192, 'all', 'All'),
(193, 'account', 'Account'),
(194, 'from', 'From'),
(195, 'account_summary_report', 'Account Summary Report'),
(196, 'search_by_date', 'Search By Date'),
(197, 'cheque_no', 'Cheque No'),
(198, 'name', 'Name'),
(199, 'closing_account', 'Closing Account'),
(200, 'close_your_account', 'Close your account'),
(201, 'last_day_closing', 'Last Day Closing'),
(202, 'cash_in', 'Cash In'),
(203, 'cash_out', 'Cash Out'),
(204, 'cash_in_hand', 'Cash In Hand'),
(205, 'add_new_bank', 'Add New Bank'),
(206, 'day_closing', 'Day Closing'),
(207, 'account_closing_report', 'Account Closing Report'),
(208, 'last_day_ammount', 'Last Day Amount'),
(209, 'adjustment', 'Adjustment'),
(210, 'pay_type', 'Pay Type'),
(211, 'customer_or_supplier', 'Customer,Supplier Or Others'),
(212, 'transection_id', 'Transections ID'),
(213, 'accounts_summary_report', 'Accounts Summary Report'),
(214, 'bank_list', 'Bank List'),
(215, 'bank_edit', 'Bank Edit'),
(216, 'debit_plus', 'Payment (+)'),
(217, 'credit_minus', 'Payment (-)'),
(218, 'account_name', 'Account Name'),
(219, 'account_type', 'Account Type'),
(220, 'account_real_name', 'Account Real Name'),
(221, 'manage_account', 'Manage Account'),
(222, 'company_name', 'Niha International'),
(223, 'edit_your_company_information', 'Edit your company information'),
(224, 'company_edit', 'Company Edit'),
(225, 'admin', 'Admin'),
(226, 'user', 'User'),
(227, 'password', 'Password'),
(228, 'last_name', 'Last Name'),
(229, 'first_name', 'First Name'),
(230, 'add_new_user_information', 'Add new user information'),
(231, 'user_type', 'User Type'),
(232, 'user_edit', 'User Edit'),
(233, 'rtr', 'RTR'),
(234, 'ltr', 'LTR'),
(235, 'ltr_or_rtr', 'LTR/RTR'),
(236, 'footer_text', 'Footer Text'),
(237, 'favicon', 'Favicon'),
(238, 'logo', 'Logo'),
(239, 'update_setting', 'Update Setting'),
(240, 'update_your_web_setting', 'Update your web setting'),
(241, 'login', 'Login'),
(242, 'your_strong_password', 'Your strong password'),
(243, 'your_unique_email', 'Your unique email'),
(244, 'please_enter_your_login_information', 'Please enter your login information.'),
(245, 'update_profile', 'Update Profile'),
(246, 'your_profile', 'Your Profile'),
(247, 're_type_password', 'Re-Type Password'),
(248, 'new_password', 'New Password'),
(249, 'old_password', 'Old Password'),
(250, 'new_information', 'New Information'),
(251, 'old_information', 'Old Information'),
(252, 'change_your_information', 'Change your information'),
(253, 'change_your_profile', 'Change your profile'),
(254, 'profile', 'Profile'),
(255, 'wrong_username_or_password', 'Wrong User Name Or Password !'),
(256, 'successfully_updated', 'Successfully Updated.'),
(257, 'blank_field_does_not_accept', 'Blank Field Does Not Accept !'),
(258, 'successfully_changed_password', 'Successfully changed password.'),
(259, 'you_are_not_authorised_person', 'You are not authorised person !'),
(260, 'password_and_repassword_does_not_match', 'Passwor and re-password does not match !'),
(261, 'new_password_at_least_six_character', 'New Password At Least 6 Character.'),
(262, 'you_put_wrong_email_address', 'You put wrong email address !'),
(263, 'cheque_ammount_asjusted', 'Cheque amount adjusted.'),
(264, 'successfully_payment_paid', 'Successfully Payment Paid.'),
(265, 'successfully_added', 'Successfully Added.'),
(266, 'successfully_updated_2_closing_ammount_not_changeale', 'Successfully Updated -2. Note: Closing Amount Not Changeable.'),
(267, 'successfully_payment_received', 'Successfully Payment Received.'),
(268, 'already_inserted', 'Already Inserted !'),
(269, 'successfully_delete', 'Successfully Delete.'),
(270, 'successfully_created', 'Successfully Created.'),
(271, 'logo_not_uploaded', 'Logo not uploaded !'),
(272, 'favicon_not_uploaded', 'Favicon not uploaded !'),
(273, 'supplier_mobile', 'Supplier Mobile'),
(274, 'supplier_address', 'Supplier Address'),
(275, 'supplier_details', 'Supplier Details'),
(276, 'add_new_supplier', 'Add New Supplier'),
(277, 'manage_suppiler', 'Supplier List'),
(278, 'manage_your_supplier', 'Manage your supplier'),
(279, 'manage_supplier_ledger', 'Manage supplier ledger'),
(280, 'invoice_id', 'Invoice ID'),
(281, 'deposite_id', 'Deposite ID'),
(282, 'supplier_actual_ledger', 'Supplier Payment Ledger'),
(283, 'supplier_information', 'Supplier Information'),
(284, 'event', 'Event'),
(285, 'add_new_income', 'Add New Income'),
(286, 'add_expese', 'Add Expense'),
(287, 'add_new_expense', 'Add New Expense'),
(288, 'total_inflow_ammount', 'Total Income Amount'),
(289, 'create_new_invoice', 'Create New Sale'),
(290, 'create_pos_invoice', 'Create POS Sale'),
(291, 'total_profit', 'Total Profit'),
(292, 'monthly_progress_report', 'Monthly Progress Report'),
(293, 'total_invoice', 'Total Sale'),
(294, 'account_summary', 'Account Summary'),
(295, 'total_supplier', 'Total Supplier'),
(296, 'total_product', 'Total Product'),
(297, 'total_customer', 'Total Customer'),
(298, 'supplier_edit', 'Supplier Edit'),
(299, 'add_new_invoice', 'Add New Sale'),
(300, 'add_new_purchase', 'Add new purchase'),
(301, 'currency', 'Currency'),
(302, 'currency_position', 'Currency Position'),
(303, 'left', 'Left'),
(304, 'right', 'Right'),
(305, 'add_tax', 'Add TAX'),
(306, 'manage_tax', 'Manage TAX'),
(307, 'add_new_tax', 'Add new TAX'),
(308, 'enter_tax', 'Enter TAX'),
(309, 'already_exists', 'Already Exists !'),
(310, 'successfully_inserted', 'Successfully Inserted.'),
(311, 'tax', 'TAX'),
(312, 'tax_edit', 'TAX Edit'),
(313, 'product_not_added', 'Product not added !'),
(314, 'total_tax', 'Total TAX'),
(315, 'manage_your_supplier_details', 'Manage your supplier details.'),
(316, 'invoice_description', 'Lorem Ipsum is sim ply dummy Lorem Ipsum is simply dummy Lorem Ipsum is simply dummy Lorem Ipsum is simply dummy Lorem Ipsum is simply dummy Lorem Ipsum is sim ply dummy Lorem Ipsum is simply dummy Lorem Ipsum is simply dummy Lorem Ipsum is simply dummy Lorem Ipsum is simply dummy'),
(317, 'thank_you_for_choosing_us', 'Thank you very much for choosing us.'),
(318, 'billing_date', 'Billing Date'),
(319, 'billing_to', 'Billing To'),
(320, 'billing_from', 'Billing From'),
(321, 'you_cant_delete_this_product', 'Sorry !!  You can\'t delete this product.This product already used in calculation system!'),
(322, 'old_customer', 'Old Customer'),
(323, 'new_customer', 'New Customer'),
(324, 'new_supplier', 'New Supplier'),
(325, 'old_supplier', 'Old Supplier'),
(326, 'credit_customer', 'Credit Customer'),
(327, 'account_already_exists', 'This Account Already Exists !'),
(328, 'edit_income', 'Edit Income'),
(329, 'you_are_not_access_this_part', 'You are not authorised person !'),
(330, 'account_edit', 'Account Edit'),
(331, 'due', 'Due'),
(332, 'expense_edit', 'Expense Edit'),
(333, 'please_select_customer', 'Please select customer !'),
(334, 'profit_report', 'Profit Report (Sale Wise)'),
(335, 'total_profit_report', 'Total profit report'),
(336, 'please_enter_valid_captcha', 'Please enter valid captcha.'),
(337, 'category_not_selected', 'Category not selected.'),
(338, 'supplier_not_selected', 'Supplier not selected.'),
(339, 'please_select_product', 'Please select product.'),
(340, 'product_model_already_exist', 'Product model already exist !'),
(341, 'invoice_logo', 'Sale Logo'),
(342, 'available_qnty', 'Av. Qnty.'),
(343, 'you_can_not_buy_greater_than_available_cartoon', 'You can not select grater than available cartoon !'),
(344, 'customer_details', 'Customer details'),
(345, 'manage_customer_details', 'Manage customer details.'),
(346, 'site_key', 'Captcha Site Key'),
(347, 'secret_key', 'Captcha Secret Key'),
(348, 'captcha', 'Captcha'),
(349, 'cartoon_quantity', 'Cartoon Quantity'),
(350, 'total_cartoon', 'Total Cartoon'),
(351, 'cartoon', 'Cartoon'),
(352, 'item_cartoon', 'Item/Cartoon'),
(353, 'product_and_supplier_did_not_match', 'Product and supplier did not match !'),
(354, 'if_you_update_purchase_first_select_supplier_then_product_and_then_quantity', 'If you update purchase,first select supplier then product and then update qnty.'),
(355, 'item', 'Item'),
(356, 'manage_your_credit_customer', 'Manage your credit customer'),
(357, 'total_quantity', 'Total Quantity'),
(358, 'quantity_per_cartoon', 'Quantity per cartoon'),
(359, 'barcode_qrcode_scan_here', 'Barcode or QR-code scan here'),
(360, 'synchronizer_setting', 'Synchronizer Setting'),
(361, 'data_synchronizer', 'Data Synchronizer'),
(362, 'hostname', 'Host name'),
(363, 'username', 'User Name'),
(364, 'ftp_port', 'FTP Port'),
(365, 'ftp_debug', 'FTP Debug'),
(366, 'project_root', 'Project Root'),
(367, 'please_try_again', 'Please try again'),
(368, 'save_successfully', 'Save successfully'),
(369, 'synchronize', 'Synchronize'),
(370, 'internet_connection', 'Internet Connection'),
(371, 'outgoing_file', 'Outgoing File'),
(372, 'incoming_file', 'Incoming File'),
(373, 'ok', 'Ok'),
(374, 'not_available', 'Not Available'),
(375, 'available', 'Available'),
(376, 'download_data_from_server', 'Download data from server'),
(377, 'data_import_to_database', 'Data import to database'),
(378, 'data_upload_to_server', 'Data uplod to server'),
(379, 'please_wait', 'Please Wait'),
(380, 'ooops_something_went_wrong', 'Oooops Something went wrong !'),
(381, 'upload_successfully', 'Upload successfully'),
(382, 'unable_to_upload_file_please_check_configuration', 'Unable to upload file please check configuration'),
(383, 'please_configure_synchronizer_settings', 'Please configure synchronizer settings'),
(384, 'download_successfully', 'Download successfully'),
(385, 'unable_to_download_file_please_check_configuration', 'Unable to download file please check configuration'),
(386, 'data_import_first', 'Data import past'),
(387, 'data_import_successfully', 'Data import successfully'),
(388, 'unable_to_import_data_please_check_config_or_sql_file', 'Unable to import data please check config or sql file'),
(389, 'total_sale_ctn', 'Total Sale Ctn'),
(390, 'in_qnty', 'In Qnty.'),
(391, 'out_qnty', 'Out Qnty.'),
(392, 'stock_report_supplier_wise', 'Stock Report (Supplier Wise)'),
(393, 'all_stock_report_supplier_wise', 'Stock Report (Suppler Wise)'),
(394, 'select_supplier', 'Select Supplier'),
(395, 'stock_report_product_wise', 'Stock Report (Product Wise)'),
(396, 'phone', 'Phone'),
(397, 'select_product', 'Select Product'),
(398, 'in_quantity', 'In Qnty.'),
(399, 'out_quantity', 'Out Qnty.'),
(400, 'in_taka', 'In TK.'),
(401, 'out_taka', 'Out TK.'),
(402, 'commission', 'Commission'),
(403, 'generate_commission', 'Generate Commssion'),
(404, 'commission_rate', 'Commission Rate'),
(405, 'total_ctn', 'Total Ctn.'),
(406, 'per_pcs_commission', 'Per PCS Commission'),
(407, 'total_commission', 'Total Commission'),
(408, 'enter', 'Enter'),
(409, 'please_add_walking_customer_for_default_customer', 'Please add \'Walking Customer\' for default customer.'),
(410, 'supplier_ammount', 'Supplier Amount'),
(411, 'my_sale_ammount', 'My Sale Amount'),
(412, 'signature_pic', 'Signature Picture'),
(413, 'branch', 'Branch'),
(414, 'ac_no', 'A/C Number'),
(415, 'ac_name', 'A/C Name'),
(416, 'bank_transaction', 'Bank Transaction'),
(417, 'bank', 'Bank'),
(418, 'withdraw_deposite_id', 'Withdraw / Deposite ID'),
(419, 'bank_ledger', 'Bank Ledger'),
(420, 'note_name', 'Note Name'),
(421, 'pcs', 'Pcs.'),
(422, '1', '1'),
(423, '2', '2'),
(424, '5', '5'),
(425, '10', '10'),
(426, '20', '20'),
(427, '50', '50'),
(428, '100', '100'),
(429, '500', '500'),
(430, '1000', '1000'),
(431, 'total_discount', 'Total Discount'),
(432, 'product_not_found', 'Product not found !'),
(433, 'this_is_not_credit_customer', 'This is not credit customer !'),
(434, 'personal_loan', 'Personal Loan'),
(435, 'add_person', 'Add Person'),
(436, 'add_loan', 'Add Loan'),
(437, 'add_payment', 'Add Payment'),
(438, 'manage_person', 'Manage Person'),
(439, 'personal_edit', 'Person Edit'),
(440, 'person_ledger', 'Person Ledger'),
(441, 'backup_restore', 'Backup '),
(442, 'database_backup', 'Database backup'),
(443, 'file_information', 'File information'),
(444, 'filename', 'Filename'),
(445, 'size', 'Size'),
(446, 'backup_date', 'Backup date'),
(447, 'backup_now', 'Backup now'),
(448, 'restore_now', 'Restore now'),
(449, 'are_you_sure', 'Are you sure ?'),
(450, 'download', 'Download'),
(451, 'backup_and_restore', 'Backup'),
(452, 'backup_successfully', 'Backup successfully'),
(453, 'delete_successfully', 'successfully Deleted'),
(454, 'stock_ctn', 'Stock/Qnt'),
(455, 'unit', 'Unit'),
(456, 'meter_m', 'Meter (M)'),
(457, 'piece_pc', 'Piece (Pc)'),
(458, 'kilogram_kg', 'Kilogram (Kg)'),
(459, 'stock_cartoon', 'Stock Cartoon'),
(460, 'add_product_csv', 'DUPL-Products'),
(461, 'import_product_csv', 'Import product (CSV)'),
(462, 'close', 'Close'),
(463, 'download_example_file', 'Download example file.'),
(464, 'upload_csv_file', 'Upload CSV File'),
(465, 'csv_file_informaion', 'CSV File Information'),
(466, 'out_of_stock', 'Out Of Stock'),
(467, 'others', 'Others'),
(468, 'full_paid', 'Full Paid'),
(469, 'successfully_saved', 'Your Data Successfully Saved'),
(470, 'manage_loan', 'Manage Loan'),
(471, 'receipt', 'Receipt'),
(472, 'payment', 'Payment'),
(473, 'cashflow', 'Daily Cash Flow'),
(474, 'signature', 'Signature'),
(475, 'supplier_reports', 'Supplier Reports'),
(476, 'generate', 'Generate'),
(477, 'todays_overview', 'Todays Overview'),
(478, 'last_sales', 'Last Sales'),
(479, 'manage_transaction', 'Manage Transaction'),
(480, 'daily_summary', 'Daily Summary'),
(481, 'daily_cash_flow', 'Daily Cash Flow'),
(482, 'custom_report', 'Custom Report'),
(483, 'transaction', 'Transaction'),
(484, 'receipt_amount', 'Receipt Amount'),
(485, 'transaction_details_datewise', 'Transaction Details Datewise'),
(486, 'cash_closing', 'Cash Closing'),
(487, 'you_can_not_buy_greater_than_available_qnty', 'You can not buy greater than available qnty.'),
(488, 'supplier_id', 'Supplier ID'),
(489, 'category_id', 'Category ID'),
(490, 'select_report', 'Select Report'),
(491, 'supplier_summary', 'Supplier summary'),
(492, 'sales_payment_actual', 'Sales payment actual'),
(493, 'today_already_closed', 'Today already closed.'),
(494, 'root_account', 'Root Account'),
(495, 'office', 'Office'),
(496, 'loan', 'Loan'),
(497, 'transaction_mood', 'Transaction Mood'),
(498, 'select_account', 'Select Account'),
(499, 'add_receipt', 'Add Receipt'),
(500, 'update_transaction', 'Update Transaction'),
(501, 'no_stock_found', 'No Stock Found !'),
(502, 'admin_login_area', 'Admin Login Area'),
(503, 'print_qr_code', 'Print QR Code'),
(504, 'discount_type', 'Discount Type'),
(505, 'discount_percentage', 'Discount'),
(506, 'fixed_dis', 'Fixed Dis.'),
(507, 'return', 'New Return'),
(508, 'stock_return_list', 'Manage Customer Return'),
(509, 'wastage_return_list', 'Manage Wastage Return'),
(510, 'return_invoice', 'Sale Return'),
(511, 'sold_qty', 'Sold Qty'),
(512, 'ret_quantity', 'Return Qty'),
(513, 'deduction', 'Deduction'),
(514, 'check_return', 'Check Return'),
(515, 'reason', 'Reason'),
(516, 'usablilties', 'Usability'),
(517, 'adjs_with_stck', 'Adjust With Stock'),
(518, 'return_to_supplier', 'Return To Supplier'),
(519, 'wastage', 'Wastage'),
(520, 'to_deduction', 'Total Deduction '),
(521, 'nt_return', 'Net Return Amount'),
(522, 'return_list', 'Return List'),
(523, 'add_return', 'Add Return'),
(524, 'per_qty', 'Purchase Qty'),
(525, 'return_supplier', 'Supplier Return'),
(526, 'stock_purchase', 'Stock Purchase Price'),
(527, 'stock_sale', 'Stock Sale Price'),
(528, 'supplier_return', 'Supplier Return Report'),
(529, 'purchase_id', 'Purchase ID'),
(530, 'return_id', 'Return ID'),
(531, 'supplier_return_list', 'Manage Supplier Return'),
(532, 'c_r_slist', 'Stock Return Stock'),
(533, 'wastage_list', 'Wastage List'),
(534, 'please_input_correct_invoice_id', 'Please Input a Correct Sale ID'),
(535, 'please_input_correct_purchase_id', 'Please Input Your Correct  Purchase ID'),
(536, 'add_more', 'Add More'),
(537, 'prouct_details', 'Product Details'),
(538, 'prouct_detail', 'Product Details'),
(539, 'stock_return', 'Stock Return'),
(540, 'choose_transaction', 'Select Transaction'),
(541, 'transection_category', 'Select  Category'),
(542, 'transaction_categry', 'Select Category'),
(543, 'search_supplier', 'Search Supplier'),
(544, 'customer_id', 'Customer ID'),
(545, 'search_customer', 'Search Customer Invoice'),
(546, 'serial_no', 'SN'),
(547, 'item_discount', 'Item Discount'),
(548, 'invoice_discount', 'Sale Discount'),
(549, 'add_unit', 'Add Unit'),
(550, 'manage_unit', 'Manage Unit'),
(551, 'add_new_unit', 'Add New Unit'),
(552, 'unit_name', 'Unit Name'),
(553, 'payment_amount', 'Payment Amount'),
(554, 'manage_your_unit', 'Manage Your Unit'),
(555, 'unit_id', 'Unit ID'),
(556, 'unit_edit', 'Unit Edit'),
(557, 'vat', 'Vat'),
(558, 'sales_report_category_wise', 'Sales Report (Category Wise)'),
(559, 'purchase_report_category_wise', 'Purchase Report (Category Wise)'),
(560, 'category_wise_purchase_report', 'Category wise purchase report'),
(561, 'category_wise_sales_report', 'Category wise sales report'),
(562, 'best_sale_product', 'Best Sale Product'),
(563, 'all_best_sales_product', 'All Best Sales Products'),
(564, 'todays_customer_receipt', 'Daily Customer Report'),
(565, 'not_found', 'Record not found'),
(566, 'collection', 'Collection'),
(567, 'increment', 'Increment'),
(568, 'accounts_tree_view', 'Accounts Tree View'),
(569, 'debit_voucher', 'Manage Payment Voucher'),
(570, 'voucher_no', 'Voucher No'),
(571, 'credit_account_head', 'Credit Account Head'),
(572, 'remark', 'Remark'),
(573, 'code', 'Code'),
(574, 'amount', 'Amount'),
(575, 'approved', 'Approved'),
(576, 'debit_account_head', 'Debit Account Head'),
(577, 'credit_voucher', 'Manage Receipt Voucher'),
(578, 'find', 'Find'),
(579, 'transaction_date', 'Transaction Date'),
(580, 'voucher_type', 'Voucher Type'),
(581, 'particulars', 'Particulars'),
(582, 'with_details', 'With Details'),
(583, 'general_ledger', 'General Ledger'),
(584, 'general_ledger_of', 'General ledger of'),
(585, 'pre_balance', 'Pre Balance'),
(586, 'current_balance', 'Current Balance'),
(587, 'to_date', 'To Date'),
(588, 'from_date', 'From Date'),
(589, 'trial_balance', 'Trial Balance'),
(590, 'authorized_signature', 'Authorized Signature'),
(591, 'chairman', 'Chairman'),
(592, 'total_income', 'Total Income'),
(593, 'statement_of_comprehensive_income', 'Statement of Comprehensive Income'),
(594, 'profit_loss', 'Profit & Loss Statement'),
(595, 'cash_flow_report', 'Cash Flow Report'),
(596, 'cash_flow_statement', 'Cash Flow Statement'),
(597, 'amount_in_dollar', 'Amount In Dollar'),
(598, 'opening_cash_and_equivalent', 'Opening Cash and Equivalent'),
(599, 'coa_print', 'Coa Print'),
(600, 'cash_flow', 'Cash Flow'),
(601, 'cash_book', 'Cash Book'),
(602, 'bank_book', 'Bank Book'),
(603, 'c_o_a', 'Chart of Account'),
(604, 'journal_voucher', 'Journal Voucher'),
(605, 'contra_voucher', 'Manage Contra Voucher'),
(606, 'voucher_approval', 'Vouchar Approval'),
(607, 'supplier_payment', 'Supplier Payment'),
(608, 'customer_receive', 'Customer Receive'),
(609, 'gl_head', 'General Head'),
(610, 'account_code', 'Account Head'),
(611, 'opening_balance', 'Opening Balance'),
(612, 'head_of_account', 'Head of Account'),
(613, 'inventory_ledger', 'Inventory Ledger'),
(614, 'newpassword', 'New Password'),
(615, 'password_recovery', 'Password Recovery'),
(616, 'forgot_password', 'Forgot Password ??'),
(617, 'send', 'Send'),
(618, 'due_report', 'Customer Due Report'),
(619, 'due_amount', 'Due Amount'),
(620, 'download_sample_file', 'Download Sample File'),
(621, 'customer_csv_upload', 'Customer Csv Upload'),
(622, 'csv_supplier', 'Csv Upload Supplier'),
(623, 'csv_upload_supplier', 'Csv Upload Supplier'),
(624, 'previous', 'Previous'),
(625, 'net_total', 'Net Total'),
(626, 'currency_list', 'Currency List'),
(627, 'currency_name', 'Currency Name'),
(628, 'currency_icon', 'Currency Symbol'),
(629, 'add_currency', 'Add Currency'),
(630, 'role_permission', 'Role Permission'),
(631, 'role_list', 'Role List'),
(632, 'user_assign_role', 'User Assign Role'),
(633, 'permission', 'Permission'),
(634, 'add_role', 'Add Role'),
(635, 'add_module', 'Add Module'),
(636, 'module_name', 'Module Name'),
(637, 'office_loan', 'Office Loan'),
(638, 'add_menu', 'Add Menu'),
(639, 'menu_name', 'Menu Name'),
(640, 'sl_no', 'Sl No'),
(641, 'create', 'Create'),
(642, 'read', 'Read'),
(643, 'role_name', 'Role Name'),
(644, 'qty', 'Quantity'),
(645, 'max_rate', 'Max Rate'),
(646, 'min_rate', 'Min Rate'),
(647, 'avg_rate', 'Average Rate'),
(648, 'role_permission_added_successfully', 'Role Permission Successfully Added'),
(649, 'update_successfully', 'Successfully Updated'),
(650, 'role_permission_updated_successfully', 'Role Permission Successfully Updated '),
(651, 'shipping_cost', 'Shipping Cost'),
(652, 'in_word', 'In Word '),
(653, 'shipping_cost_report', 'Shipping Cost Report'),
(654, 'cash_book_report', 'Cash Book Report'),
(655, 'inventory_ledger_report', 'Inventory Ledger Report'),
(656, 'trial_balance_with_opening_as_on', 'Trial Balance With Opening As On'),
(657, 'type', 'Type'),
(658, 'taka_only', 'Taka Only'),
(659, 'item_description', 'Desc'),
(660, 'sold_by', 'Sold By'),
(661, 'user_wise_sales_report', 'Sales Report (Salesman Wise)'),
(662, 'user_name', 'User Name'),
(663, 'total_sold', 'Total Sold'),
(664, 'user_wise_sale_report', 'Sales Report (Salesman Wise)'),
(665, 'barcode_or_qrcode', 'Barcode/QR-code'),
(666, 'category_csv_upload', 'Category Csv  Upload'),
(667, 'unit_csv_upload', 'Unit Csv Upload'),
(668, 'invoice_return_list', 'Sales Return list'),
(669, 'invoice_return', 'Sales Return Report'),
(670, 'tax_report', 'TAX Report'),
(671, 'select_tax', 'Select TAX'),
(672, 'hrm', 'HRM'),
(673, 'employee', 'Employee'),
(674, 'add_employee', 'Add Employee'),
(675, 'manage_employee', 'Manage Employee'),
(676, 'attendance', 'Attendance'),
(677, 'add_attendance', 'Attendance'),
(678, 'manage_attendance', 'Manage Attendance'),
(679, 'payroll', 'Payroll'),
(680, 'add_payroll', 'Payroll'),
(681, 'manage_payroll', 'Manage Payroll'),
(682, 'employee_type', 'Employee Type'),
(683, 'employee_designation', 'Employee Designation'),
(684, 'designation', 'Designation'),
(685, 'add_designation', 'Add Designation'),
(686, 'manage_designation', 'Manage Designation'),
(687, 'designation_update_form', 'Designation Update form'),
(688, 'picture', 'Picture'),
(689, 'country', 'Country'),
(690, 'blood_group', 'Blood Group'),
(691, 'address_line_1', 'Address Line 1'),
(692, 'address_line_2', 'Address Line 2'),
(693, 'zip', 'Zip code'),
(694, 'city', 'City'),
(695, 'hour_rate_or_salary', 'Houre Rate/Salary'),
(696, 'rate_type', 'Rate Type'),
(697, 'hourly', 'Hourly'),
(698, 'salary', 'Salary'),
(699, 'employee_update', 'Employee Update'),
(700, 'checkin', 'Check In'),
(701, 'employee_name', 'Employee Name'),
(702, 'checkout', 'Check Out'),
(703, 'confirm_clock', 'Confirm Clock'),
(704, 'stay', 'Stay Time'),
(705, 'sign_in', 'Sign In'),
(706, 'check_in', 'Check In'),
(707, 'single_checkin', 'Single Check In'),
(708, 'bulk_checkin', 'Bulk Check In'),
(709, 'successfully_checkout', 'Successfully Checkout'),
(710, 'attendance_report', 'Attendance Report'),
(711, 'datewise_report', 'Date Wise Report'),
(712, 'employee_wise_report', 'Employee Wise Report'),
(713, 'date_in_time_report', 'Date In Time Report'),
(714, 'request', 'Request'),
(715, 'sign_out', 'Sign Out'),
(716, 'work_hour', 'Work Hours'),
(717, 's_time', 'Start Time'),
(718, 'e_time', 'In Time'),
(719, 'salary_benefits_type', 'Benefits Type'),
(720, 'salary_benefits', 'Salary Benefits'),
(721, 'beneficial_list', 'Benefit List'),
(722, 'add_beneficial', 'Add Benefits'),
(723, 'add_benefits', 'Add Benefits'),
(724, 'benefits_list', 'Benefit List'),
(725, 'benefit_type', 'Benefit Type'),
(726, 'benefits', 'Benefit'),
(727, 'manage_benefits', 'Manage Benefits'),
(728, 'deduct', 'Deduct'),
(729, 'add', 'Add'),
(730, 'add_salary_setup', 'Add Salary Setup'),
(731, 'manage_salary_setup', 'Manage Salary Setup'),
(732, 'basic', 'Basic'),
(733, 'salary_type', 'Salary Type'),
(734, 'addition', 'Addition'),
(735, 'gross_salary', 'Gross Salary'),
(736, 'set', 'Set'),
(737, 'salary_generate', 'Salary Generate'),
(738, 'manage_salary_generate', 'Manage Salary Generate'),
(739, 'sal_name', 'Salary Name'),
(740, 'gdate', 'Generated Date'),
(741, 'generate_by', 'Generated By'),
(742, 'the_salary_of', 'The Salary of '),
(743, 'already_generated', ' Already Generated'),
(744, 'salary_month', 'Salary Month'),
(745, 'successfully_generated', 'Successfully Generated'),
(746, 'salary_payment', 'Salary Payment'),
(747, 'employee_salary_payment', 'Employee Salary Payment'),
(748, 'total_salary', 'Total Salary'),
(749, 'total_working_minutes', 'Total Working Hours'),
(750, 'working_period', 'Working Period'),
(751, 'paid_by', 'Paid By'),
(752, 'pay_now', 'Pay Now '),
(753, 'confirm', 'Confirm'),
(754, 'successfully_paid', 'Successfully Paid'),
(755, 'add_incometax', 'Add Income TAX'),
(756, 'setup_tax', 'Setup TAX'),
(757, 'start_amount', 'Start Amount'),
(758, 'end_amount', 'End Amount'),
(759, 'tax_rate', 'TAX Rate'),
(760, 'setup', 'Setup'),
(761, 'manage_income_tax', 'Manage Income TAX'),
(762, 'income_tax_updateform', 'Income TAX Update form'),
(763, 'positional_information', 'Positional Information'),
(764, 'personal_information', 'Personal Information'),
(765, 'timezone', 'Time Zone'),
(766, 'sms', 'SMS'),
(767, 'sms_configure', 'SMS Configure'),
(768, 'url', 'URL'),
(769, 'sender_id', 'Sender ID'),
(770, 'api_key', 'Api Key'),
(771, 'gui_pos', 'GUI POS'),
(772, 'manage_service', 'Service List'),
(773, 'service', 'Service'),
(774, 'add_service', 'Add Service'),
(775, 'service_edit', 'Service Edit'),
(776, 'service_csv_upload', 'Service CSV Upload'),
(777, 'service_name', 'Service Name'),
(778, 'charge', 'Charge'),
(779, 'service_invoice', 'New Service'),
(780, 'service_discount', 'Service Discount'),
(781, 'hanging_over', 'ETD'),
(782, 'service_details', 'Service Details'),
(783, 'tax_settings', 'TAX Settings'),
(784, 'default_value', 'Default Value'),
(785, 'number_of_tax', 'Number of TAX'),
(786, 'please_select_employee', 'Please Select Employee'),
(787, 'manage_service_invoice', 'Manage Service'),
(788, 'update_service_invoice', 'Update Service Invoice'),
(789, 'customer_wise_tax_report', 'Customer Wise TAX Report'),
(790, 'service_id', 'Service Id'),
(791, 'invoice_wise_tax_report', 'Invoice Wise TAX Report'),
(792, 'reg_no', 'Reg No'),
(793, 'update_now', 'Update Now'),
(794, 'import', 'Database Import'),
(795, 'add_expense_item', 'Add Expense Item'),
(796, 'manage_expense_item', 'Manage Expense Item'),
(797, 'add_expense', 'Add Expense'),
(798, 'manage_expense', 'Manage Expense'),
(799, 'expense_statement', 'Expense Statement'),
(800, 'expense_type', 'Expense Type'),
(801, 'expense_item_name', 'Expense Item Name'),
(802, 'stock_purchase_price', 'Stock Purchase Price'),
(803, 'purchase_price', 'Avg. Purchase Price'),
(804, 'customer_advance', 'Customer Advance'),
(805, 'advance_type', 'Advance Type'),
(806, 'restore', 'Database Restore'),
(807, 'supplier_advance', 'Supplier Advance'),
(808, 'please_input_correct_invoice_no', 'Please Input Correct Invoice NO'),
(809, 'backup', 'Database Backup'),
(810, 'app_setting', 'App Settings'),
(811, 'local_server_url', 'Local Server Url'),
(812, 'online_server_url', 'Online Server Url'),
(813, 'connet_url', 'Connected Hotspot Ip/url'),
(814, 'update_your_app_setting', 'Update Your App Setting'),
(815, 'select_category', 'Select Category'),
(816, 'mini_invoice', 'Mini Invoice'),
(817, 'purchase_details', 'Purchase Details'),
(818, 'disc', 'Dis %'),
(819, 'serial', 'Serial'),
(820, 'transaction_head', 'Transaction Head'),
(821, 'transaction_type', 'Transaction Type'),
(822, 'return_details', 'Return Details'),
(823, 'return_to_customer', 'Return To Customer'),
(824, 'sales_and_purchase_report_summary', 'Sales And Purchase Report Summary'),
(825, 'add_person_officeloan', 'Add Person (Office Loan)'),
(826, 'add_loan_officeloan', 'Add Loan (Office Loan)'),
(827, 'add_payment_officeloan', 'Add Payment (Office Loan)'),
(828, 'manage_loan_officeloan', 'Manage Loan (Office Loan)'),
(829, 'add_person_personalloan', 'Add Person (Personal Loan)'),
(830, 'add_loan_personalloan', 'Add Loan (Personal Loan)'),
(831, 'add_payment_personalloan', 'Add Payment (Personal Loan)'),
(832, 'manage_loan_personalloan', 'Manage Loan (Personal)'),
(833, 'hrm_management', 'Human Resource'),
(834, 'cash_adjustment', 'Cash Adjustment'),
(835, 'adjustment_type', 'Adjustment Type'),
(836, 'change', 'Change'),
(837, 'sale_by', 'Sale By'),
(838, 'salary_date', 'Salary Date'),
(839, 'earnings', 'Earnings'),
(840, 'total_addition', 'Total Addition'),
(841, 'total_deduction', 'Total Deduction'),
(842, 'net_salary', 'Net Salary'),
(843, 'ref_number', 'Reference Number'),
(844, 'name_of_bank', 'Name Of Bank'),
(845, 'salary_slip', 'Salary Slip'),
(846, 'basic_salary', 'Basic Salary'),
(847, 'return_from_customer', 'Return From Customer'),
(848, 'quotation', 'Quotation'),
(849, 'add_quotation', 'New Quotation'),
(851, 'terms', 'Terms'),
(852, 'send_to_customer', 'Sent To Customer'),
(853, 'quotation_no', 'Quotation No'),
(854, 'quotation_date', 'Quotation Date'),
(855, 'total_service_tax', 'Total Service TAX'),
(856, 'totalservicedicount', 'Total Service Discount'),
(857, 'item_total', 'Item Total'),
(858, 'service_total', 'Service Total'),
(859, 'quot_description', 'Quotation Description'),
(860, 'sub_total', 'Sub Total'),
(861, 'mail_setting', 'Mail Settings'),
(862, 'mail_configuration', 'Mail Configuration'),
(863, 'mail', 'Mail'),
(864, 'protocol', 'Protocol'),
(865, 'smtp_host', 'SMTP Host'),
(866, 'smtp_port', 'SMTP Port'),
(867, 'sender_mail', 'Sender Mail'),
(868, 'mail_type', 'Mail Type'),
(869, 'html', 'HTML'),
(870, 'text', 'TEXT'),
(871, 'expiry_date', 'Expiry Date'),
(872, 'api_secret', 'Api Secret'),
(873, 'please_config_your_mail_setting', NULL),
(874, 'quotation_successfully_added', 'Quotation Successfully Added'),
(875, 'add_to_invoice', 'Add To Invoice'),
(876, 'added_to_invoice', 'Added To Invoice'),
(877, 'closing_balance', 'Closing Balance'),
(878, 'contact', 'Contact'),
(879, 'fax', 'Fax'),
(880, 'state', 'State'),
(881, 'discounts', 'Discount'),
(882, 'address1', 'Address1'),
(883, 'address2', 'Address2'),
(884, 'receive', 'Receive'),
(885, 'purchase_history', 'Purchase History'),
(886, 'cash_payment', 'Cash Payment'),
(887, 'bank_payment', 'Bank Payment'),
(888, 'do_you_want_to_print', 'Do You Want to Print'),
(889, 'yes', 'Yes'),
(890, 'no', 'No'),
(891, 'todays_sale', 'Today\'s Sales'),
(892, 'or', 'OR'),
(893, 'no_result_found', 'No Result Found'),
(894, 'add_service_quotation', 'Add Service Quotation'),
(895, 'add_to_invoice', 'Add To Invoice'),
(896, 'item_quotation', 'Item Quotation'),
(897, 'service_quotation', 'Service Quotation'),
(898, 'return_from', 'Return Form'),
(899, 'customer_return_list', 'Customer Return List'),
(900, 'pdf', 'Pdf'),
(901, 'note', 'Note'),
(902, 'update_debit_voucher', 'Update Debit Voucher'),
(903, 'update_credit_voucher', 'Update Credit voucher'),
(904, 'on', 'On'),
(905, '', ''),
(906, 'total_expenses', 'Total Expense'),
(907, 'already_exist', 'Already Exist'),
(908, 'checked_out', 'Checked Out'),
(909, 'update_salary_setup', 'Update Salary Setup'),
(910, 'employee_signature', 'Employee Signature'),
(911, 'payslip', 'Payslip'),
(912, 'exsisting_role', 'Existing Role'),
(913, 'filter', 'Filter'),
(914, 'testinput', NULL),
(915, 'update_quotation', 'Update Quotation'),
(916, 'quotation_successfully_updated', 'Quotation Successfully Updated'),
(917, 'successfully_approved', 'Successfully Approved'),
(918, 'expiry', 'Expiry'),
(919, 'user_list', 'User List'),
(920, 'assign_roleto_user', 'Assign Role To User'),
(921, 'assign_role_list', 'Assigned Role List'),
(922, 'application_settings', 'Application Settings'),
(923, 'company_list', 'Company List'),
(924, 'edit_company', 'Edit Company'),
(925, 'edit_user', 'Edit User'),
(926, 'edit_currency', 'Edit Currency'),
(927, 'mobile_no', 'Mobile No'),
(928, 'email_address', 'Email Address'),
(929, 'customer_list', 'Customer List'),
(930, 'advance_receipt', 'Advance Receipt'),
(931, 'supplier_list', 'Supplier List'),
(932, 'category_list', 'Category List'),
(933, 'no_record_found', 'No Record Found'),
(934, 'unit_list', 'Unit List'),
(935, 'edit_product', 'Edit Product'),
(936, 'payable_summary', 'Payable Summary'),
(937, 'pad_print', 'Pad Print'),
(938, 'pos_print', 'POS Print'),
(939, 'pos_invoice', 'POS Invoice'),
(940, 'employee_profile', 'Employee Profile'),
(941, 'edit_beneficials', 'Edit Beneficials'),
(942, 'edit_setup_update', 'Edit Salary Setup'),
(943, 'add_office_loan', 'Add Office Loan'),
(944, 'income_tax', 'Income TAX'),
(945, 'quotation_to_sale', 'Quotation To Sale'),
(946, 'quotation_edit', 'Edit Quotation'),
(947, 'edit_profile', 'Edit Profile'),
(948, 'edit_supplier', 'Edit Supplier'),
(949, 'edit_bank', 'Edit Bank'),
(950, 'balance_sheet', 'Financial Position'),
(951, 'salary_setup', 'Salary Setup'),
(952, 'account_head', 'Account Head'),
(953, 'add_invoice', 'Add Invoice'),
(954, 'general_ledger_report', 'General Ledger Report'),
(955, 'print_setting', 'Printer Settings'),
(956, 'header', 'Header'),
(957, 'footer', 'Footer'),
(958, 'supplier_payment_receipt', 'Payment Receipt'),
(959, 'welcome_back', 'Welcome Back'),
(960, 'overwrite', 'Over Write'),
(961, 'module', 'Module'),
(962, 'purchase_key', 'Purchase Key'),
(963, 'buy_now', 'Buy Now'),
(964, 'module_list', 'Module List'),
(965, 'modules', 'Modules'),
(966, 'install', 'Install'),
(967, 'uninstall', 'Uninstall'),
(968, 'module_added_successfully', 'Module Added Successfully'),
(969, 'no_tables_are_registered_in_config', 'No table registered in config'),
(970, 'tables_are_not_available_in_database', 'Table Are not Available in Database'),
(971, 'addon', 'Add-ons'),
(972, 'generate_qr', 'Generate QR'),
(973, 'latestv', 'Latest Version'),
(974, 'Current Version', 'Current Version'),
(976, 'notesupdate', 'Note: If you want to update software,you Must have immediate previous version'),
(977, 'arabic', NULL),
(978, 'vat_no', 'VAT NO'),
(979, 'new_p_method', 'Add New Payment Method'),
(980, 'dis_val', 'Dis. Value'),
(981, 'vat_val', 'VAT Value'),
(982, 'ttl_val', 'Total VAT'),
(983, 'purchase_discount', 'Purchase Discount'),
(984, 'order_time', 'Order Time'),
(985, 'order_by', 'Order By'),
(986, 'terms_list', 'Sales Terms List'),
(987, 'terms_add', 'Add Sales Terms'),
(988, 'term_condi', 'Terms & Condition'),
(989, 'terms_update', 'Update Seles Terms'),
(990, 'add_payment_method', 'Add Payment Method'),
(991, 'payment_method_list', 'Payment Method List'),
(992, 'payment_method_name', 'Payment Method Name'),
(993, 'batch_no', 'Batch No'),
(994, 'total_with_vat', 'Total With VAT'),
(995, 'invoice_time', 'Invoice Time'),
(996, 'product_vat', 'Product VAT'),
(997, 'service_vat', 'Service VAT'),
(998, 'cr_no', 'CR NO'),
(999, 'service_payment', 'Service Receive'),
(1000, 'vat_tax_setting', 'VAT & TAX Setting'),
(1001, 'qty2', 'Qty'),
(1002, 'batch', 'Batch'),
(1003, 'disc', 'Disc'),
(1004, 'tot_price', 'Tot Price'),
(1005, 'tot_before_dis', 'Total Before Discount'),
(1006, 'tot_with_dis', 'Total with Discount'),
(1007, 'tax_vat', 'TAX Value'),
(1008, 'return_receipt_text', 'Please keep the receipt and bring it in case of return'),
(1009, 'invoice_qr_code', 'Invoice Qr-Code'),
(1010, 'sales_due', 'Today Sales Due'),
(1011, 'purchase_due', 'Today Purchase Due'),
(1012, 'delivery_note', 'Delivery Note'),
(1013, 'financial_year', 'Financial Year'),
(1014, 'close_financial_year', 'Close Financial Year'),
(1015, 'create_financial_year', 'Create Financial Year'),
(1016, 'year_closing', 'Year Closing'),
(1017, 'financial_year_start_date', 'Financial Year Start'),
(1018, 'financial_year_end_date', 'Financial Year End'),
(1019, 'subaccount_list', 'Sub Account List'),
(1020, 'subaccount_add', 'Sub Account Add'),
(1021, 'subtype', 'Sub Type'),
(1022, 'predefined_accounts', 'Predefined Accounts'),
(1023, 'create_debit_voucher', 'New Payment Voucher'),
(1024, 'ledger_comment', 'Ledger Comment'),
(1025, 'reverseHead', 'Reverse Account Name'),
(1026, 'VNo', 'VNo'),
(1027, 'ledgerComment', '	Ledger Comment'),
(1028, 'create_credit_voucher', 'New Receipt Voucher'),
(1029, 'create_contra_voucher', 'New Contra Voucher'),
(1030, 'create_journal_voucher', 'Create Journal Voucher'),
(1031, 'reverse_account_head', 'Reverse Account Head'),
(1032, 'bank_reconciliation', 'Bank Reconciliation'),
(1033, 'day_book', 'Day Book'),
(1034, 'sub_ledger', 'Sub Ledger'),
(1035, 'income_statement_form', 'Income Statement'),
(1036, 'year', 'Year'),
(1037, 'expenditure_statement', 'Expenditure Statement'),
(1038, 'profit_loss_report', 'Profit & Loss Statement'),
(1039, 'fixedasset_schedule', 'Fixed Assets'),
(1040, 'receipt_payment', 'Receipt & Payment Statement'),
(1041, 'cash_basis', 'Cash Basis'),
(1042, 'accrual_basis', 'Accrual Basis'),
(1043, 'bank_reconciliation_report', 'Bank Reconciliation'),
(1044, 'salary_advance_view', 'Salary Advance'),
(1045, 'add_salary_advance', 'Add Salary Advance'),
(1046, 'manage_salary_advance', 'Manage Salary Advance'),
(1047, 'release_amount', 'Release Amount'),
(1048, 'create_date', 'Create Date'),
(1049, 'employee_salary_generate', 'Salary Generate'),
(1050, 'approved_date', 'Approved Date'),
(1051, 'approved_by', 'Approved By'),
(1052, 'salar_month', 'Salary Month'),
(1053, 'employee_salary_chart', 'Employee Salary Chart'),
(1054, 'post', 'Post'),
(1055, 'employee_salary_payment_view', 'Manage Employee Salary'),
(1056, 'pay_by', 'Pay By'),
(1057, 'bank_book_report', 'Bank Book Report Of'),
(1058, 'head_name', 'Head Name'),
(1059, 'head_code', 'Head Code'),
(1060, 'voucher', 'Voucher'),
(1061, 'sub_ledger_of', 'Sub Ledger of'),
(1062, 'fixed_assets_report', 'Fixed Assets Annual Report'),
(1063, 'opening_balance_fixed_assets', 'Opening Balance of Fixed Assets'),
(1064, 'additions', 'Additions'),
(1065, 'closing_balance_fixed_assets', 'Closing Balance of Fixed Assets'),
(1066, 'depreciation_rate', 'Depreciation Rate'),
(1067, 'depreciation_value', 'Depreciation Value'),
(1068, 'opening_balance_accumulated_depreciation', 'Opening Balance of Accumulated Depreciation'),
(1069, 'closing_balance_accumulated_depreciation', 'Closing Balance of Accumulated Depreciation'),
(1070, 'written_down_value', 'Written Down Value'),
(1071, 'unapproved', 'Unapproved'),
(1072, 'check_no', 'Check No'),
(1073, 'Total Liabilities Equity', 'total_liabilities_equity'),
(1074, 'total_equity', 'Total Equity'),
(1075, 'total_liabilities', 'Total Liabilities'),
(1076, 'total_assets', 'Total Assets'),
(1077, 'cashinhand', 'Cash In Hand'),
(1078, 'cash_bank', 'Cash At Bank'),
(1079, 'advance', 'Advance'),
(1080, 'view_employee_payment', 'Employee Payment List'),
(1081, 'ret_item', 'Returned Item'),
(1082, 'replaced_item', 'Replaced Item'),
(1083, 'adj_amount', 'Adjusted Amount'),
(1084, 'is_autoapprove_v', 'Auto Approve Invoice Voucher'),
(1085, 'cost_price', 'Cost Price'),
(1086, 'chalan_no', 'Voucher No'),
(1087, 'already_returned', 'This Invoice is Already Returned '),
(1088, 'manage_cheque', 'Manage Cheque'),
(1089, 'cheque_flow_report', 'Cheque Flow Report'),
(1090, 'payment_method', 'Payment Method'),
(1091, 'supplier_payment', 'Supplier Payment'),
(1092, 'new_supplier_payment', 'New Supplier Payment'),
(1093, 'new_customer_receive', 'New Customer Receive'),
(1094, 'sales_of_service', 'Sales of Service'),
(1095, 'new_service_receive', 'New Service Receive'),
(1096, 'books', 'Books'),
(1097, 'ledgers', 'Ledgers'),
(1098, 'statements', 'Statements'),
(1099, 'accounts_report', 'Accounting Reports'),
(1100, 'settings_preferences', 'Settings & Preferences'),
(1101, 'area_responsibility', 'Area of Responsibility'),
(1102, 'return1', 'Return'),
(1103, 'locationlist', 'Location List'),
(1104, 'add_location', 'Add Location'),
(1105, 'store', 'Store'),
(1106, 'edit_location', 'Edit Location'),
(1107, 'storetypelist', 'Store Type List'),
(1108, 'add_storetype', 'Add Store Type'),
(1109, 'add_floor', 'Add Floor'),
(1110, 'floorlist', 'Floor List'),
(1111, 'add_store', 'Add Store'),
(1112, 'store_list', 'Store List'),
(1113, 'storelist', 'Store List'),
(1114, 'add_store', 'Add Store'),
(1115, 'stock', 'Inventory'),
(1116, 'stockbatchlist', 'Stock Batch List'),
(1117, 'add_stockbatch', 'Add Stock Batch'),
(1118, 'add_openingstock', 'Add Opening Stock'),
(1119, 'openingstockbatchlist', 'Opening Stock List'),
(1120, 'new_stockdisposalnote', 'New Stock Disposal Note'),
(1121, 'manage_stockdisposalnote', 'Manage Stock Disposal Note'),
(1122, 'manage_Stock_adjustment', 'Manage Stock'),
(1123, 'new_Stock_adjustment', 'New Stock'),
(1124, 'new_store_transfer', 'New Store Transfer'),
(1125, 'manage_store_transfer', 'Manage Store Transfer'),
(1126, 'new_purchase_order', 'New Purchase Order'),
(1127, 'manage_purchase_order', 'Manage Purchase Order'),
(1128, 'new_grn', 'New GRN'),
(1129, 'manage_grn', 'Manage GRN'),
(1130, 'new_gdn', 'New GDN'),
(1131, 'manage_gdn', 'Manage GDN'),
(1132, 'live_stock_report', 'Live Stock Report'),
(1133, 'live_stock_report', 'Live Stock Report'),
(1134, 'addcalender_events', 'Add Calender Events'),
(1135, 'calender_events', 'Calender Events'),
(1136, 'calenderevent_list', 'Calender Event List'),
(1137, 'add_shifts', 'Add Shifts'),
(1138, 'shifts', 'Shifts'),
(1139, 'shift_list', 'Shift List'),
(1140, 'shift_assignment', 'Shift Assignment'),
(1141, 'product_wise_sales_report', 'Sales Report (Product Wise)'),
(1142, 'new_reservation_period', 'New Reservation Period'),
(1143, 'manage_reservation_period', 'Manage Reservation Period'),
(1144, 'reservation', 'Reservation'),
(1145, 'new_reservation', 'New Reservation'),
(1146, 'manage_reservation', 'Manage Reservation'),
(1147, 'view_reservation', 'View Reservation'),
(1148, 'product_wise_purchase_report', 'Purchase Report (Product Wise)'),
(1149, 'tamil', NULL),
(1150, 'stock_audit_report', 'Stock Audit Report'),
(1151, 'add_branch', 'Add Branch'),
(1152, 'branch_list', 'Branch List'),
(1153, 'user_assign_branch', 'User Assign Branch'),
(1154, 'user_assign_store', 'User Assign Store'),
(1155, 'new_pos', 'New POS'),
(1156, 'new_quotation', 'New Sales Order'),
(1157, 'manage_quotation', 'Manage Sales Order'),
(1158, 'add_brand', 'Add Brand'),
(1159, 'brand_list', 'Brand List'),
(1160, 'add_oop', 'Add Origin of Product'),
(1161, 'oop_list', 'Origin of Product List'),
(1162, 'add_subcategory', 'Add Subcategory'),
(1163, 'subcategory_list', 'Subcategory List'),
(1164, 'new_convertion_ratio', 'New Convertion Ratio'),
(1165, 'manage_convertion_ratio', 'Manage Convertion Ratio'),
(1166, 'substock_conversion', 'Substock Conversion'),
(1167, 'conversionratio_form', 'Add Conversion Ratio'),
(1168, 'conversionratio_list', 'Conversion Ratio List'),
(1169, 'dupl_sales', 'DUPL-Sales'),
(1170, 'datauploader', 'Data Uploader'),
(1171, 'dupl_sales', 'DUPL-Sales'),
(1172, 'datauploader', 'Data Uploader'),
(1173, 'new_sales_return', 'New Sales Return'),
(1174, 'manage_sales_return', 'Manage Sales Return'),
(1175, 'new_sales_return', 'New Sales Return'),
(1176, 'manage_sales_return', 'Manage Sales Return'),
(1177, 'new_purchase_return', 'New Purchase Return'),
(1178, 'manage_purchase_return', 'Manage Purchase Return'),
(1179, 'labelprint', 'Barcode Print'),
(1180, 'add_product_group', 'Add Product Group'),
(1181, 'product_grouplist', 'Product Group List'),
(1182, 'sales_order_report', 'Sales Order Report'),
(1183, 'sales_return_report', 'Sales Return Report'),
(1184, 'purchase_order_report', 'Purchase Order Report'),
(1185, 'purchase_return_report', 'Purchase Return Report'),
(1186, 'grn_report', 'GRN Report'),
(1187, 'gdn_report', 'GDN Report'),
(1188, 'gross_profit_report', 'Gross Profit Report'),
(1189, 'gross_profit_category_report', 'Gross Profit Report (Category Wise)'),
(1190, 'serviceorder_invoice', 'New Service Order'),
(1191, 'manage_serviceorder_invoice', 'Manage Service Order'),
(1192, 'service_report', 'Service Report'),
(1193, 'purchase_report_productwise', 'Purchase Report (Product wise)'),
(1194, 'labelprint', 'Barcode Print'),
(1195, 'product_batch_summary_report', 'Product Batch Summary Report'),
(1196, 'service_order_report', 'Service Order Report'),
(1197, 'warehouse_management', 'Warehouse Management'),
(1198, 'add_company', 'Add Company'),
(1199, 'add_company', 'Add Company'),
(1200, 'new_payment_voucher', 'New Payment Voucher'),
(1201, 'manage_payment_voucher', 'Manage Payment Voucher'),
(1202, 'new_receipt_voucher', 'New Receipt Voucher'),
(1203, 'manage_receipt_voucher', 'Manage Receipt Voucher'),
(1204, 'new_contra_voucher', 'New Contra Voucher\r\n'),
(1205, 'manage_contra_voucher', 'Manage Contra Voucher\r\n'),
(1206, 'payment_report', 'Payment Report'),
(1207, 'receipt_report', 'Receipt Report'),
(1208, 'contra_voucher_report', 'Contra Voucher Report'),
(1209, 'stock_alert', 'Stock Alert'),
(1210, 'expiry_alert', 'Expiry Alert');
INSERT INTO `language` (`id`, `phrase`, `english`) VALUES
(1211, 'new_inventory_transection', 'New Inventory Transaction'),
(1212, 'manage_inventory_transection', 'Manage Inventory Transaction'),
(1213, 'alert', 'Alert'),
(1214, 'sale_of_service', 'Sale Of Service'),
(1215, 'brand_form', 'Add Brand'),
(1216, 'subcategory_form', 'Add Subcategory'),
(1217, 'touch_invoice', 'New GUI POS'),
(1218, 'payment_receipt_type_form', 'Add Payment/Receipt Type'),
(1219, 'payment_receipt_type_list', 'Payment/Receipt Type List'),
(1220, 'data_uploader', 'Data Uploader'),
(1221, 'profit_report_invoicewise', 'Profit Report (Invoice Wise)'),
(1222, 'edit_customer', 'Edit Customer');

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(10) NOT NULL,
  `screen` varchar(500) NOT NULL,
  `operation` varchar(11) NOT NULL,
  `pid` int(10) NOT NULL,
  `userid` int(10) NOT NULL,
  `lastupdatedate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `screen`, `operation`, `pid`, `userid`, `lastupdatedate`) VALUES
(1, 'stock', 'insert', 1, 1, '2025-08-31 10:11:59'),
(2, 'stock', 'insert', 2, 1, '2025-08-31 10:23:20'),
(3, 'stock', 'insert', 3, 1, '2025-08-31 10:34:04'),
(4, 'stock', 'delete', 1, 1, '2025-08-31 10:34:35'),
(5, 'stock', 'delete', 2, 1, '2025-08-31 10:34:45'),
(6, 'stock', 'delete', 3, 1, '2025-08-31 10:34:50'),
(7, 'stock', 'insert', 4, 1, '2025-08-31 11:10:30'),
(8, 'sale', 'insert', 1, 1, '2025-08-31 17:28:50'),
(9, 'sale', 'update', 1, 1, '2025-08-31 17:33:29'),
(10, 'sale', 'insert', 2, 1, '2025-08-31 17:34:25'),
(11, 'sale', 'insert', 3, 1, '2025-08-31 17:55:33'),
(12, 'sale', 'insert', 4, 1, '2025-08-31 18:12:35'),
(13, 'sale', 'insert', 5, 1, '2025-08-31 18:17:09'),
(14, 'sale', 'insert', 6, 1, '2025-09-01 03:54:19'),
(15, 'purchase', 'insert', 1, 1, '2025-09-06 14:39:47'),
(16, 'purchase', 'update', 1, 1, '2025-09-06 14:40:31'),
(17, 'purchase', 'insert', 2, 1, '2025-09-06 17:55:15'),
(18, 'sale', 'insert', 7, 1, '2025-09-06 18:16:29'),
(19, 'sale', 'insert', 8, 1, '2025-09-06 18:16:29'),
(20, 'sale', 'insert', 9, 1, '2025-09-13 14:36:17'),
(21, 'purchase', 'insert', 3, 1, '2025-09-14 05:04:05'),
(22, 'sale', 'insert', 10, 1, '2025-09-14 05:05:22'),
(23, 'purchase', 'insert', 4, 1, '2025-09-14 05:08:08'),
(24, 'sale', 'insert', 11, 1, '2025-09-14 05:09:11'),
(25, 'sale', 'update', 11, 1, '2025-09-14 05:10:08'),
(26, 'purchase', 'insert', 5, 1, '2025-09-14 05:11:39'),
(27, 'sale', 'insert', 12, 1, '2025-09-14 05:12:44'),
(28, 'sale', 'insert', 13, 1, '2025-09-14 05:14:29'),
(29, 'sale', 'update', 13, 1, '2025-09-14 05:15:40'),
(30, 'sale', 'update', 13, 1, '2025-09-23 11:22:23'),
(31, 'sale', 'update', 13, 1, '2025-09-23 11:22:48'),
(32, 'sale', 'update', 13, 1, '2025-09-23 11:23:27'),
(33, 'sale', 'update', 13, 1, '2025-09-23 11:23:54'),
(34, 'sale', 'update', 13, 1, '2025-09-23 11:25:20'),
(35, 'sale', 'update', 13, 1, '2025-09-23 11:26:15'),
(36, 'sale', 'update', 13, 1, '2025-09-23 11:26:53'),
(37, 'sale', 'update', 13, 1, '2025-09-23 11:27:21'),
(38, 'sale', 'update', 13, 1, '2025-09-23 11:27:50'),
(39, 'sale', 'update', 13, 1, '2025-09-23 11:28:14'),
(40, 'sale', 'update', 11, 1, '2025-09-23 11:29:18'),
(41, 'sale', 'update', 11, 1, '2025-09-23 11:29:53'),
(42, 'sale', 'update', 11, 1, '2025-09-23 11:30:19'),
(43, 'sale', 'update', 11, 1, '2025-09-23 11:30:33'),
(44, 'sale', 'update', 11, 1, '2025-09-23 11:31:00'),
(45, 'sale', 'update', 11, 1, '2025-09-23 11:31:17'),
(46, 'sale', 'update', 11, 1, '2025-09-23 11:31:40'),
(47, 'sale', 'update', 11, 1, '2025-09-23 11:32:00'),
(48, 'sale', 'insert', 14, 1, '2025-09-23 11:46:17'),
(49, 'sale', 'update', 13, 1, '2025-09-23 11:47:44'),
(50, 'sale', 'update', 11, 1, '2025-09-23 11:48:55'),
(51, 'sale', 'update', 13, 1, '2025-09-23 11:49:31'),
(52, 'sale', 'update', 14, 1, '2025-09-23 11:50:51'),
(53, 'sale', 'update', 14, 1, '2025-09-23 11:51:25'),
(54, 'sale', 'update', 14, 1, '2025-09-23 11:52:00'),
(55, 'sale', 'update', 14, 1, '2025-09-23 11:52:23'),
(56, 'sale', 'update', 14, 1, '2025-09-23 11:52:34'),
(57, 'sale', 'update', 14, 1, '2025-09-23 11:53:17'),
(58, 'sale', 'update', 14, 1, '2025-09-30 09:03:39'),
(59, 'sale', 'update', 14, 1, '2025-09-30 09:04:12'),
(60, 'sale', 'update', 14, 1, '2025-10-07 10:13:57'),
(61, 'sale', 'update', 13, 1, '2025-10-07 10:14:04'),
(62, 'sale', 'update', 12, 1, '2025-10-07 10:14:11'),
(63, 'sale', 'update', 11, 1, '2025-10-07 10:14:18'),
(64, 'sale', 'update', 10, 1, '2025-10-07 10:14:25'),
(65, 'sale', 'update', 9, 1, '2025-10-07 10:14:32'),
(66, 'sale', 'update', 8, 1, '2025-10-07 10:14:37'),
(67, 'sale', 'update', 7, 1, '2025-10-07 10:14:45'),
(68, 'sale', 'update', 6, 1, '2025-10-07 10:14:52'),
(69, 'sale', 'update', 5, 1, '2025-10-07 10:14:58'),
(70, 'sale', 'update', 4, 1, '2025-10-07 10:15:05'),
(71, 'sale', 'update', 3, 1, '2025-10-07 10:15:12'),
(72, 'sale', 'update', 2, 1, '2025-10-07 10:15:19'),
(73, 'purchase', 'delete', 5, 1, '2025-10-07 04:45:59'),
(74, 'purchase', 'delete', 4, 1, '2025-10-07 04:46:05'),
(75, 'purchase', 'delete', 3, 1, '2025-10-07 04:46:11'),
(76, 'purchase', 'delete', 2, 1, '2025-10-07 04:46:18'),
(77, 'purchase', 'delete', 1, 1, '2025-10-07 04:46:25'),
(78, 'stock', 'delete', 4, 1, '2025-10-07 08:58:04'),
(79, 'stock', 'insert', 5, 1, '2025-10-08 13:03:19'),
(80, 'sale', 'insert', 15, 1, '2025-10-08 18:35:23'),
(81, 'stock', 'update', 5, 1, '2025-10-08 13:20:17'),
(82, 'sale', 'insert', 16, 1, '2025-10-08 18:51:05'),
(83, 'sale', 'update', 15, 1, '2025-10-10 17:29:58'),
(84, 'sale', 'update', 16, 1, '2025-10-10 17:30:05'),
(85, 'stock', 'update', 5, 1, '2025-10-10 12:02:00'),
(86, 'stock', 'insert', 6, 1, '2025-10-10 12:15:19'),
(87, 'sale', 'insert', 17, 1, '2025-10-10 17:48:11'),
(88, 'sale', 'insert', 18, 1, '2025-10-10 17:53:23'),
(89, 'sale', 'insert', 19, 1, '2025-10-10 18:06:07'),
(90, 'purchase', 'insert', 6, 1, '2025-10-13 09:44:27'),
(91, 'GRN', 'insert', 1, 1, '2025-10-13 09:48:56'),
(92, 'GRN', 'insert', 2, 1, '2025-10-13 09:52:09'),
(93, 'GRN', 'insert', 3, 1, '2025-10-13 14:28:55'),
(94, 'stock', 'insert', 7, 1, '2025-10-16 04:49:42'),
(95, 'sale', 'insert', 20, 1, '2025-10-16 10:21:52'),
(96, 'stock', 'insert', 8, 1, '2025-10-16 06:35:02'),
(97, 'stock', 'delete', 8, 1, '2025-10-16 06:37:57'),
(98, 'stock', 'delete', 7, 1, '2025-10-16 06:39:22'),
(99, 'stock', 'delete', 6, 1, '2025-10-16 06:39:27'),
(100, 'stock', 'delete', 5, 1, '2025-10-16 06:39:32'),
(101, 'sale', 'update', 20, 1, '2025-10-16 12:09:44'),
(102, 'sale', 'update', 19, 1, '2025-10-16 12:09:58'),
(103, 'sale', 'update', 18, 1, '2025-10-16 12:10:04'),
(104, 'sale', 'update', 17, 1, '2025-10-16 12:10:09'),
(105, 'stock', 'insert', 9, 1, '2025-10-16 06:40:29'),
(106, 'sale', 'insert', 21, 1, '2025-10-16 12:17:23'),
(107, 'sale', 'insert', 22, 1, '2025-10-16 12:37:17'),
(108, 'purchase', 'insert', 7, 1, '2025-10-16 12:44:49'),
(109, 'sale', 'update', 22, 1, '2025-10-16 12:53:18'),
(110, 'stock', 'insert', 10, 1, '2025-10-16 07:50:56'),
(111, 'purchase', 'delete', 7, 1, '2025-10-16 07:53:35'),
(112, 'stock', 'insert', 11, 1, '2025-10-16 07:55:40'),
(113, 'stock', 'insert', 12, 1, '2025-10-16 07:58:35'),
(114, 'stock', 'insert', 13, 1, '2025-10-16 08:02:48'),
(115, 'sale', 'insert', 23, 1, '2025-10-16 13:38:02'),
(116, 'stock', 'insert', 14, 1, '2025-10-16 08:09:13'),
(117, 'stock', 'update', 9, 1, '2025-10-16 08:12:22'),
(118, 'stock', 'update', 11, 1, '2025-10-16 08:17:17'),
(119, 'stock', 'update', 13, 1, '2025-10-16 08:18:37'),
(120, 'purchase', 'insert', 8, 439261, '2025-10-16 15:17:36'),
(121, 'purchase', 'insert', 9, 439261, '2025-10-16 15:18:28'),
(122, 'purchase', 'insert', 10, 439261, '2025-10-16 15:19:12'),
(123, 'purchase', 'insert', 11, 439261, '2025-10-16 15:20:33'),
(124, 'purchase', 'insert', 12, 1, '2025-10-16 15:46:34'),
(125, 'stock', 'insert', 15, 1, '2025-10-16 10:30:27'),
(126, 'stock', 'delete', 15, 1, '2025-10-16 10:31:25'),
(127, 'purchase', 'insert', 13, 1, '2025-10-16 16:07:56'),
(128, 'purchase', 'update', 13, 1, '2025-10-16 16:10:12'),
(129, 'purchase', 'update', 13, 1, '2025-10-16 16:11:49'),
(130, 'purchase', 'update', 13, 1, '2025-10-16 16:12:45'),
(131, 'purchase', 'delete', 13, 1, '2025-10-16 10:44:11'),
(132, 'purchase', 'delete', 12, 1, '2025-10-16 10:44:17'),
(133, 'purchase', 'update', 11, 1, '2025-10-18 13:08:29'),
(134, 'sales_order', 'insert', 1, 1, '2025-10-18 13:13:07'),
(135, 'sales_order', 'update', 1, 1, '2025-10-18 13:19:07'),
(136, 'sales_order', 'update', 1, 1, '2025-10-18 13:20:23'),
(137, 'sale', 'insert', 24, 1, '2025-10-18 13:22:02'),
(138, 'sale', 'update', 24, 1, '2025-10-18 13:24:55'),
(139, 'sales_order', 'insert', 2, 1, '2025-10-18 13:26:35'),
(140, 'sales_order', 'update', 2, 1, '2025-10-18 13:26:57'),
(141, 'sales_order', 'insert', 3, 1, '2025-10-18 13:34:51'),
(142, 'purchase', 'insert', 14, 1, '2025-10-18 13:37:53'),
(143, 'sale', 'insert', 25, 1, '2025-10-18 13:43:07'),
(144, 'sale', 'update', 25, 1, '2025-10-18 13:43:23'),
(145, 'sale', 'insert', 26, 1, '2025-10-18 13:45:58'),
(146, 'sale', 'insert', 27, 1, '2025-10-18 13:48:43'),
(147, 'sale', 'update', 27, 1, '2025-10-18 13:52:50'),
(148, 'sale', 'update', 27, 1, '2025-10-18 13:54:01'),
(149, 'sale', 'update', 27, 1, '2025-10-18 13:56:37'),
(150, 'sale', 'update', 26, 1, '2025-10-18 13:57:59'),
(151, 'sale', 'update', 23, 1, '2025-10-18 13:59:32'),
(152, 'sale', 'update', 22, 1, '2025-10-18 14:01:31'),
(153, 'sale', 'update', 23, 1, '2025-10-18 14:01:56'),
(154, 'sale', 'update', 21, 1, '2025-10-18 14:03:42'),
(155, 'stock', 'insert', 16, 473955, '2025-10-24 00:25:05'),
(156, 'sales_order', 'insert', 4, 1, '2025-10-25 14:25:44'),
(157, 'sale', 'insert', 28, 1, '2025-10-25 14:32:17'),
(158, 'sale', 'update', 28, 1, '2025-10-25 14:33:19'),
(159, 'stock', 'insert', 17, 1, '2025-10-25 09:37:50'),
(160, 'sale', 'insert', 29, 1, '2025-10-25 15:19:25'),
(161, 'sale', 'insert', 30, 1, '2025-10-25 15:22:20'),
(162, 'sale', 'insert', 31, 1, '2025-10-25 15:22:32'),
(163, 'sale', 'insert', 32, 1, '2025-10-25 15:26:55'),
(164, 'sale', 'insert', 33, 392188, '2025-10-25 16:39:28'),
(165, 'sale', 'insert', 34, 392188, '2025-10-25 16:41:54'),
(166, 'sale', 'update', 33, 392188, '2025-10-25 16:45:28'),
(167, 'purchase', 'insert', 15, 392188, '2025-10-25 17:13:53'),
(168, 'GRN', 'insert', 4, 883446, '2025-10-25 17:41:41'),
(169, 'sale', 'update', 15, 1, '2025-10-25 18:03:46'),
(170, 'sale', 'update', 15, 1, '2025-10-25 18:05:17'),
(171, 'grn', 'delete', 4, 883446, '2025-10-25 12:36:49'),
(172, 'purchase', 'delete', 15, 1, '2025-10-25 12:37:15'),
(173, 'sale', 'insert', 35, 1, '2025-10-25 18:16:54'),
(174, 'GDN', 'insert', 1, 883446, '2025-10-25 18:21:15'),
(175, 'GDN', 'insert', 2, 883446, '2025-10-25 18:22:47'),
(176, 'sale', 'update', 35, 1, '2025-10-25 18:27:48'),
(177, 'sale', 'update', 35, 1, '2025-10-25 18:28:41'),
(178, 'gdn', 'delete', 1, 1, '2025-10-25 13:01:09'),
(179, 'gdn', 'delete', 2, 1, '2025-10-25 13:01:16'),
(180, 'sale', 'update', 35, 1, '2025-10-25 18:31:38'),
(181, 'sales_order', 'insert', 5, 1, '2025-10-25 18:46:19'),
(182, 'stock', 'insert', 1, 1, '2025-10-30 12:18:01'),
(183, 'sale', 'insert', 1, 1, '2025-10-30 17:49:33'),
(184, 'sale', 'insert', 2, 1, '2025-10-30 17:51:06'),
(185, 'sale', 'insert', 3, 1, '2025-10-30 17:52:59'),
(186, 'GDN', 'insert', 1, 1, '2025-10-30 18:04:18'),
(187, 'GDN', 'insert', 2, 1, '2025-10-30 18:04:43'),
(188, 'GDN', 'insert', 3, 1, '2025-10-30 18:05:12'),
(189, 'purchase', 'insert', 1, 1, '2025-10-30 18:15:42'),
(190, 'GRN', 'insert', 1, 1, '2025-10-30 18:16:06'),
(191, 'sale', 'insert', 4, 1, '2025-10-30 18:17:07'),
(192, 'purchase', 'insert', 2, 1, '2025-11-02 16:19:54'),
(193, 'purchase', 'insert', 3, 1, '2025-11-02 16:23:33'),
(194, 'purchase', 'update', 3, 1, '2025-11-02 16:25:57'),
(195, 'GRN', 'insert', 2, 1, '2025-11-02 16:27:14'),
(196, 'GRN', 'insert', 3, 1, '2025-11-02 16:28:18'),
(197, 'GRN', 'insert', 4, 1, '2025-11-02 16:30:01'),
(198, 'sale', 'insert', 5, 1, '2025-11-02 16:47:55'),
(199, 'sale', 'insert', 6, 1, '2025-11-02 16:56:08'),
(200, 'GDN', 'insert', 4, 1, '2025-11-02 16:59:08'),
(201, 'GDN', 'insert', 5, 1, '2025-11-02 17:06:41'),
(202, 'GDN', 'insert', 6, 1, '2025-11-02 17:07:54'),
(203, 'gdn', 'delete', 6, 1, '2025-11-02 11:39:00'),
(204, 'GDN', 'insert', 7, 1, '2025-11-02 17:09:26'),
(205, 'gdn', 'delete', 7, 1, '2025-11-02 11:40:06'),
(206, 'gdn', 'delete', 5, 1, '2025-11-02 11:40:12'),
(207, 'grn', 'delete', 4, 1, '2025-11-02 11:41:03'),
(208, 'grn', 'delete', 3, 1, '2025-11-02 11:41:09'),
(209, 'GRN', 'insert', 5, 1, '2025-11-02 17:13:11'),
(210, 'purchase', 'insert', 4, 1, '2025-11-02 17:32:12'),
(211, 'purchase', 'delete', 4, 1, '2025-11-02 12:09:28'),
(212, 'purchase', 'insert', 5, 1, '2025-11-02 17:40:04'),
(213, 'sale', 'insert', 7, 1, '2025-11-02 17:48:56'),
(214, 'sale', 'update', 7, 1, '2025-11-02 17:50:55'),
(215, 'GRN', 'insert', 6, 1, '2025-11-02 17:58:07'),
(216, 'purchase', 'insert', 6, 1, '2025-11-02 18:02:32'),
(217, 'purchase', 'delete', 6, 1, '2025-11-02 12:36:50'),
(218, 'purchase', 'update', 5, 1, '2025-11-02 18:07:08'),
(219, 'purchase', 'insert', 7, 1, '2025-11-02 18:08:10'),
(220, 'purchase', 'delete', 7, 1, '2025-11-02 12:39:54'),
(221, 'purchase', 'insert', 8, 1, '2025-11-02 18:10:22'),
(222, 'sale', 'insert', 8, 1, '2025-11-02 18:14:29'),
(223, 'GRN', 'insert', 7, 1, '2025-11-02 18:17:52'),
(224, 'GDN', 'insert', 8, 1, '2025-11-02 18:19:04'),
(225, 'stock', 'insert', 2, 1, '2025-11-02 12:53:31'),
(226, 'stock', 'insert', 3, 1, '2025-11-02 12:58:35'),
(227, 'stock', 'insert', 4, 1, '2025-11-02 13:05:08'),
(228, 'GDN', 'insert', 9, 1, '2025-11-02 18:36:01'),
(229, 'gdn', 'delete', 9, 1, '2025-11-02 13:06:30'),
(230, 'GDN', 'insert', 10, 1, '2025-11-02 18:39:06'),
(231, 'stock', 'insert', 5, 1, '2025-11-02 13:10:19'),
(232, 'stock', 'insert', 6, 1, '2025-11-02 13:11:01'),
(233, 'gdn', 'delete', 10, 1, '2025-11-02 13:13:30'),
(234, 'purchase', 'update', 8, 1, '2025-11-02 18:46:07'),
(235, 'gdn', 'update', 1, 1, '2025-11-02 18:53:45'),
(236, 'stock', 'insert', 7, 1, '2025-11-02 13:43:20'),
(237, 'stock', 'insert', 8, 1, '2025-11-02 13:44:35'),
(238, 'gdn', 'delete', 8, 1, '2025-11-21 00:46:56'),
(239, 'gdn', 'delete', 4, 1, '2025-11-21 00:47:01'),
(240, 'gdn', 'delete', 3, 1, '2025-11-21 00:47:07'),
(241, 'gdn', 'delete', 3, 1, '2025-11-21 00:47:11'),
(242, 'gdn', 'delete', 2, 1, '2025-11-21 00:47:21'),
(243, 'gdn', 'delete', 1, 1, '2025-11-21 00:47:28'),
(244, 'grn', 'delete', 7, 1, '2025-11-21 00:47:37'),
(245, 'grn', 'delete', 6, 1, '2025-11-21 00:47:43'),
(246, 'grn', 'delete', 5, 1, '2025-11-21 00:47:49'),
(247, 'grn', 'delete', 2, 1, '2025-11-21 00:47:55'),
(248, 'grn', 'delete', 1, 1, '2025-11-21 00:47:59'),
(249, 'stock', 'delete', 8, 1, '2025-11-21 00:48:07'),
(250, 'stock', 'delete', 7, 1, '2025-11-21 00:48:11'),
(251, 'stock', 'delete', 6, 1, '2025-11-21 00:48:15'),
(252, 'stock', 'delete', 5, 1, '2025-11-21 00:48:20'),
(253, 'stock', 'delete', 4, 1, '2025-11-21 00:48:24'),
(254, 'stock', 'delete', 3, 1, '2025-11-21 00:48:28'),
(255, 'stock', 'delete', 2, 1, '2025-11-21 00:48:34'),
(256, 'stock', 'delete', 1, 1, '2025-11-21 00:48:38'),
(257, 'sale', 'update', 8, 1, '2025-11-21 06:20:07'),
(258, 'sale', 'update', 7, 1, '2025-11-21 06:20:12'),
(259, 'sale', 'update', 6, 1, '2025-11-21 06:20:16'),
(260, 'sale', 'update', 5, 1, '2025-11-21 06:20:22'),
(261, 'sale', 'update', 4, 1, '2025-11-21 06:20:27'),
(262, 'sale', 'update', 3, 1, '2025-11-21 06:22:29'),
(263, 'sale', 'update', 2, 1, '2025-11-21 06:22:34'),
(264, 'sale', 'update', 1, 1, '2025-11-21 06:22:39'),
(265, 'purchase', 'delete', 1, 1, '2025-11-21 01:00:00'),
(266, 'purchase', 'delete', 2, 1, '2025-11-21 01:00:05'),
(267, 'purchase', 'delete', 3, 1, '2025-11-21 01:00:10'),
(268, 'purchase', 'delete', 5, 1, '2025-11-21 01:00:15'),
(269, 'purchase', 'delete', 8, 1, '2025-11-21 01:00:21'),
(270, 'stock', 'insert', 9, 2680, '2025-11-22 09:37:51'),
(271, 'stock', 'update', 9, 2680, '2025-11-22 09:40:55'),
(272, 'stock', 'insert', 10, 240776, '2025-11-22 09:49:36'),
(273, 'stock', 'insert', 11, 2680, '2025-11-22 09:59:15'),
(274, 'stock', 'insert', 12, 240776, '2025-11-22 10:04:51'),
(275, 'stock', 'insert', 13, 2680, '2025-11-22 10:08:05'),
(276, 'stock', 'insert', 14, 240776, '2025-11-22 10:11:52'),
(277, 'stock', 'insert', 15, 2680, '2025-11-22 10:14:42'),
(278, 'stock', 'insert', 16, 240776, '2025-11-22 10:16:12'),
(279, 'sale', 'insert', 9, 977501, '2025-11-22 15:52:39'),
(280, 'sale', 'update', 9, 977501, '2025-11-22 15:53:38'),
(281, 'purchase', 'insert', 9, 977501, '2025-11-22 15:54:13'),
(282, 'GRN', 'insert', 8, 654348, '2025-11-22 15:57:24'),
(283, 'purchase', 'insert', 10, 977501, '2025-11-22 16:06:56'),
(284, 'GRN', 'insert', 9, 654348, '2025-11-22 16:10:44'),
(285, 'purchase', 'insert', 11, 602812, '2025-11-22 16:26:11'),
(286, 'purchase', 'insert', 12, 329331, '2025-11-22 16:31:39'),
(287, 'GRN', 'insert', 10, 739262, '2025-11-22 16:35:28'),
(288, 'purchase', 'insert', 13, 329331, '2025-11-22 16:38:00'),
(289, 'GRN', 'insert', 11, 739262, '2025-11-22 16:39:41'),
(290, 'purchase', 'insert', 14, 368862, '2025-11-22 16:52:49'),
(291, 'purchase', 'insert', 15, 602812, '2025-11-22 16:56:34'),
(292, 'purchase', 'insert', 16, 368862, '2025-11-22 16:59:34'),
(293, 'purchase', 'delete', 16, 368862, '2025-11-22 11:37:29'),
(294, 'purchase', 'insert', 17, 368862, '2025-11-22 17:09:17'),
(295, 'GRN', 'insert', 12, 863140, '2025-11-22 17:22:22'),
(296, 'purchase', 'insert', 18, 977501, '2025-11-22 17:29:44'),
(297, 'purchase', 'delete', 18, 977501, '2025-11-22 12:02:34'),
(298, 'sale', 'insert', 10, 977501, '2025-11-22 17:34:22'),
(299, 'GDN', 'insert', 11, 654348, '2025-11-22 17:39:16'),
(300, 'sale', 'insert', 11, 977501, '2025-11-22 17:42:53'),
(301, 'GDN', 'insert', 12, 654348, '2025-11-22 17:44:44'),
(302, 'sale', 'insert', 12, 329331, '2025-11-22 17:47:35'),
(303, 'GDN', 'insert', 13, 739262, '2025-11-22 17:57:13'),
(304, 'sale', 'insert', 13, 329331, '2025-11-22 18:00:59'),
(305, 'GDN', 'insert', 14, 739262, '2025-11-22 18:02:20'),
(306, 'sale', 'insert', 14, 602812, '2025-11-22 18:04:14'),
(307, 'sale', 'insert', 15, 368862, '2025-11-22 18:06:04'),
(308, 'sale', 'insert', 16, 602812, '2025-11-22 18:08:12'),
(309, 'GDN', 'insert', 15, 495733, '2025-11-22 18:09:59'),
(310, 'sale', 'insert', 17, 368862, '2025-11-22 18:11:19'),
(311, 'stock', 'insert', 17, 149551, '2025-11-24 12:15:21'),
(312, 'GDN', 'insert', 16, 654348, '2025-11-24 17:50:23'),
(313, 'GRN', 'insert', 13, 654348, '2025-11-24 17:52:11'),
(314, 'stock', 'insert', 18, 409360, '2025-11-24 12:27:45'),
(315, 'stock', 'update', 18, 409360, '2025-11-24 12:32:18'),
(316, 'GDN', 'insert', 17, 654348, '2025-11-24 18:03:10'),
(317, 'GRN', 'insert', 14, 654348, '2025-11-24 18:03:59'),
(318, 'stock', 'update', 18, 409360, '2025-11-24 12:36:37'),
(319, 'stock', 'update', 18, 409360, '2025-11-24 12:39:10'),
(320, 'stock', 'delete', 18, 409360, '2025-11-24 12:43:06'),
(321, 'grn', 'delete', 14, 654348, '2025-11-24 12:43:28'),
(322, 'gdn', 'delete', 16, 654348, '2025-11-24 12:43:38'),
(323, 'gdn', 'delete', 17, 654348, '2025-11-24 12:48:43'),
(324, 'grn', 'delete', 13, 654348, '2025-11-24 12:49:01'),
(325, 'GDN', 'insert', 18, 654348, '2025-11-24 18:20:23'),
(326, 'GRN', 'insert', 15, 654348, '2025-11-24 18:21:13'),
(327, 'stock', 'insert', 19, 409360, '2025-11-24 12:54:10'),
(328, 'GDN', 'insert', 19, 654348, '2025-11-24 18:36:43'),
(329, 'GRN', 'insert', 16, 654348, '2025-11-24 18:37:37'),
(330, 'stock', 'insert', 20, 149551, '2025-11-24 13:11:44'),
(331, 'GRN', 'insert', 17, 654348, '2025-11-24 18:52:49'),
(332, 'stock', 'insert', 21, 409360, '2025-11-24 13:28:18'),
(333, 'GDN', 'insert', 20, 739262, '2025-11-24 19:02:53'),
(334, 'stock', 'insert', 22, 149551, '2025-11-24 13:44:08'),
(335, 'stock', 'insert', 23, 149551, '2025-11-24 13:56:17'),
(336, 'stock', 'insert', 24, 409360, '2025-11-24 14:02:09'),
(337, 'stock', 'insert', 25, 149551, '2025-11-24 14:05:41'),
(338, 'GRN', 'insert', 18, 495733, '2025-11-24 19:37:32'),
(339, 'GDN', 'insert', 21, 495733, '2025-11-24 19:38:35'),
(340, 'stock', 'insert', 26, 409360, '2025-11-24 14:12:09'),
(341, 'stock', 'insert', 27, 409360, '2025-11-24 14:18:26'),
(342, 'GRN', 'insert', 19, 863140, '2025-11-24 19:50:31'),
(343, 'stock', 'delete', 23, 1, '2025-11-25 03:44:41'),
(344, 'stock', 'delete', 27, 1, '2025-11-25 03:44:47'),
(345, 'stock', 'update', 22, 1, '2025-11-25 03:46:50'),
(346, 'stock', 'update', 26, 1, '2025-11-25 03:47:54'),
(347, 'GDN', 'insert', 22, 1, '2025-11-25 09:26:14'),
(348, 'GRN', 'insert', 20, 1, '2025-11-25 09:27:50'),
(349, 'stock', 'delete', 26, 1, '2025-11-25 04:37:44'),
(350, 'stock', 'delete', 22, 1, '2025-11-25 04:38:17'),
(351, 'gdn', 'delete', 22, 1, '2025-11-25 04:44:18'),
(352, 'gdn', 'delete', 21, 1, '2025-11-25 04:44:25'),
(353, 'grn', 'delete', 20, 1, '2025-11-25 04:44:39'),
(354, 'grn', 'delete', 18, 1, '2025-11-25 04:44:45'),
(355, 'stock', 'delete', 25, 1, '2025-11-25 04:45:03'),
(356, 'stock', 'delete', 24, 1, '2025-11-25 04:45:17'),
(357, 'grn', 'delete', 19, 409360, '2025-11-25 05:12:47'),
(358, 'stock', 'insert', 28, 1, '2025-11-25 07:21:04'),
(359, 'stock', 'insert', 29, 1, '2025-11-25 07:22:32'),
(360, 'stock', 'insert', 30, 1, '2025-11-25 07:24:09'),
(361, 'stock', 'insert', 31, 1, '2025-11-25 07:29:44'),
(362, 'GRN', 'insert', 21, 1, '2025-11-25 13:01:25'),
(363, 'GRN', 'insert', 22, 1, '2025-11-25 13:02:12'),
(364, 'GDN', 'insert', 23, 1, '2025-11-25 13:02:52'),
(365, 'GDN', 'insert', 24, 1, '2025-11-25 13:03:18'),
(366, 'stock', 'update', 29, 1, '2025-11-25 07:39:37'),
(367, 'stock', 'insert', 32, 409360, '2025-11-25 08:30:55'),
(368, 'stock', 'delete', 32, 409360, '2025-11-25 08:37:27'),
(369, 'stock', 'insert', 33, 149551, '2025-11-25 08:50:19'),
(370, 'stock', 'insert', 34, 409360, '2025-11-25 08:52:36'),
(371, 'GDN', 'insert', 25, 739262, '2025-11-25 14:25:43'),
(372, 'gdn', 'delete', 25, 739262, '2025-11-25 08:58:34'),
(373, 'GDN', 'insert', 26, 739262, '2025-11-25 14:29:52'),
(374, 'stock', 'insert', 35, 149551, '2025-11-25 09:02:13'),
(375, 'stock', 'insert', 36, 409360, '2025-11-25 09:08:00'),
(376, 'stock', 'insert', 37, 149551, '2025-11-25 09:10:31'),
(377, 'GDN', 'insert', 27, 495733, '2025-11-25 14:42:23'),
(378, 'stock', 'insert', 38, 409360, '2025-11-25 09:15:27'),
(379, 'stock', 'insert', 39, 149551, '2025-11-25 09:18:39'),
(380, 'stock', 'insert', 40, 149551, '2025-11-25 09:31:14'),
(381, 'stock', 'insert', 41, 409360, '2025-11-25 09:34:39'),
(382, 'stock', 'insert', 42, 409360, '2025-11-25 09:36:22'),
(383, 'stock', 'insert', 43, 149551, '2025-11-25 09:37:55'),
(384, 'stock', 'insert', 44, 409360, '2025-11-25 09:40:13'),
(385, 'stock', 'insert', 45, 149551, '2025-11-25 09:42:41'),
(386, 'stock', 'insert', 46, 409360, '2025-11-25 09:43:46'),
(387, 'stock', 'insert', 47, 149551, '2025-11-25 09:45:06'),
(388, 'stock', 'insert', 48, 409360, '2025-11-25 09:46:33'),
(389, 'stock', 'update', 42, 1, '2025-11-25 10:54:27'),
(390, 'stock', 'update', 42, 1, '2025-11-25 10:59:15'),
(391, 'stock', 'update', 42, 1, '2025-11-25 11:00:15'),
(392, 'stock', 'insert', 49, 1, '2025-11-25 11:06:58'),
(393, 'stock', 'update', 49, 1, '2025-11-25 11:07:25'),
(394, 'stock', 'update', 49, 1, '2025-11-25 11:08:18'),
(395, 'stock', 'update', 49, 1, '2025-11-25 11:09:52'),
(396, 'stock', 'update', 49, 1, '2025-11-25 11:10:35'),
(397, 'stock', 'update', 49, 1, '2025-11-25 11:15:25'),
(398, 'stock', 'update', 33, 1, '2025-11-25 13:42:31'),
(399, 'GDN', 'insert', 28, 1, '2025-11-25 19:14:39'),
(400, 'purchase', 'update', 9, 977501, '2025-11-27 15:22:37'),
(401, 'GDN', 'insert', 29, 654348, '2025-11-27 15:52:27'),
(402, 'gdn', 'delete', 29, 654348, '2025-11-27 10:25:42'),
(403, 'GDN', 'insert', 30, 654348, '2025-11-27 15:56:28'),
(404, 'purchase', 'update', 13, 329331, '2025-11-27 15:59:41'),
(405, 'GDN', 'insert', 31, 739262, '2025-11-27 16:02:00'),
(406, 'purchase', 'update', 11, 602812, '2025-11-27 16:04:50'),
(407, 'stock', 'delete', 49, 1, '2025-11-27 10:36:45'),
(408, 'stock', 'insert', 50, 1, '2025-11-27 10:42:59'),
(409, 'sale', 'update', 10, 977501, '2025-11-27 16:20:57'),
(410, 'GRN', 'insert', 23, 654348, '2025-11-27 16:22:50'),
(411, 'sale', 'update', 13, 329331, '2025-11-27 16:33:26'),
(412, 'GRN', 'insert', 24, 739262, '2025-11-27 16:36:05'),
(413, 'sale', 'update', 14, 602812, '2025-11-27 16:39:16'),
(414, 'stock', 'insert', 51, 1, '2025-11-27 11:11:09'),
(415, 'purchase', 'insert', 19, 1, '2025-11-27 16:58:26'),
(416, 'purchase', 'update', 19, 1, '2025-11-27 16:59:03'),
(417, 'purchase', 'delete', 19, 1, '2025-11-27 11:29:42'),
(418, 'sale', 'insert', 18, 1, '2025-11-27 17:00:30'),
(419, 'sale', 'update', 18, 1, '2025-11-27 17:01:09'),
(420, 'sale', 'update', 18, 1, '2025-11-27 17:01:46'),
(421, 'stock', 'delete', 42, 1, '2025-11-27 11:38:12'),
(422, 'stock', 'insert', 52, 1, '2025-11-27 11:40:41'),
(423, 'stock', 'delete', 52, 1, '2025-11-27 12:24:33'),
(424, 'stock', 'insert', 53, 1, '2025-11-27 12:26:59'),
(425, 'stock', 'delete', 53, 1, '2025-11-27 12:27:20'),
(426, 'stock', 'insert', 54, 1, '2025-11-27 12:27:54'),
(427, 'stock', 'update', 54, 1, '2025-11-27 12:28:39'),
(428, 'stock', 'delete', 54, 1, '2025-11-27 12:30:02'),
(429, 'stock', 'insert', 55, 1, '2025-11-27 12:31:07'),
(430, 'stock', 'update', 55, 1, '2025-11-27 12:35:06'),
(431, 'stock', 'delete', 55, 1, '2025-11-27 12:35:50'),
(432, 'stock', 'insert', 56, 1, '2025-11-27 12:36:26'),
(433, 'sale', 'update', 14, 1, '2025-11-27 18:19:40'),
(434, 'stock', 'delete', 29, 1, '2025-11-27 13:22:33'),
(435, 'stock', 'insert', 57, 1, '2025-11-27 13:24:06'),
(436, 'stock', 'insert', 58, 1, '2025-12-01 01:03:58'),
(437, 'stock', 'update', 58, 1, '2025-12-01 01:05:06'),
(438, 'purchase', 'insert', 20, 1, '2025-12-01 06:44:56'),
(439, 'sale', 'insert', 19, 1, '2025-12-01 06:46:36'),
(440, 'sale', 'insert', 20, 1, '2025-12-07 06:19:24'),
(441, 'sales_order', 'insert', 1, 1, '2025-12-07 09:53:41'),
(442, 'sale', 'insert', 21, 1, '2025-12-07 09:54:46'),
(443, 'purchase', 'insert', 21, 1, '2025-12-10 08:39:59'),
(444, 'purchase', 'delete', 21, 1, '2025-12-10 03:10:48'),
(445, 'purchase', 'insert', 22, 1, '2025-12-10 19:12:12'),
(446, 'purchase', 'delete', 22, 1, '2025-12-10 13:44:01'),
(447, 'sales_return', 'insert', 1, 1, '2025-12-17 12:31:28'),
(448, 'sales_return', 'update', 1, 1, '2025-12-17 12:33:42'),
(449, 'sale', 'update', 1, 1, '2025-12-17 12:33:56'),
(450, 'sale', 'update', 1, 1, '2025-12-17 12:53:18'),
(451, 'sales_return', 'update', 1, 1, '2025-12-17 13:36:30'),
(452, 'sales_return', 'insert', 2, 1, '2025-12-17 13:37:01'),
(453, 'sales_return', 'update', 2, 1, '2025-12-17 13:37:23'),
(454, 'sales_return', 'insert', 3, 1, '2025-12-17 19:07:46'),
(455, 'sales_return', 'update', 3, 1, '2025-12-17 19:09:28'),
(456, 'sale', 'insert', 22, 1, '2025-12-17 19:17:01'),
(457, 'sales_return', 'insert', 4, 1, '2025-12-17 19:18:20'),
(458, 'sale', 'insert', 23, 1, '2025-12-17 19:34:33'),
(459, 'sales_return', 'insert', 5, 1, '2025-12-17 19:42:15'),
(460, 'sales_return', 'update', 5, 1, '2025-12-17 19:42:44'),
(461, 'sale', 'insert', 24, 1, '2025-12-17 19:44:10'),
(462, 'sales_return', 'insert', 6, 1, '2025-12-17 19:45:51'),
(463, 'stock', 'insert', 59, 1, '2025-12-20 22:05:49'),
(464, 'stock', 'delete', 59, 1, '2025-12-20 22:06:16'),
(465, 'sales_order', 'insert', 2, 1, '2025-12-24 17:36:12'),
(466, 'purchase', 'insert', 23, 1, '2025-12-26 11:43:57'),
(467, 'purchase', 'insert', 24, 1, '2025-12-26 11:45:34'),
(468, 'purchase_order', 'insert', 1, 1, '2025-12-26 11:56:58'),
(469, 'purchase_order', 'insert', 2, 1, '2025-12-26 12:30:05'),
(470, 'purchase', 'insert', 25, 240776, '2025-12-26 12:34:56'),
(471, 'purchase', 'delete', 25, 240776, '2025-12-26 07:06:01'),
(472, 'purchase', 'insert', 26, 1, '2025-12-26 12:36:27'),
(473, 'purchase', 'update', 26, 1, '2025-12-26 12:37:12'),
(474, 'purchase_order', 'insert', 3, 1, '2025-12-26 19:24:03'),
(475, 'purchase_order', 'update', 2, 1, '2025-12-26 19:34:59'),
(476, 'sale', 'insert', 25, 1, '2025-12-27 03:48:10'),
(477, 'purchase', 'insert', 27, 1, '2025-12-29 17:43:14'),
(478, 'purchase', 'delete', 27, 1, '2025-12-29 12:16:34'),
(479, 'stock', 'insert', 60, 1, '2025-12-29 12:17:02'),
(480, 'stock', 'delete', 60, 1, '2025-12-29 12:19:43'),
(481, 'sale', 'insert', 26, 1, '2025-12-29 18:22:07'),
(482, 'sale', 'insert', 27, 1, '2025-12-29 18:35:36'),
(483, 'sale', 'insert', 28, 1, '2025-12-29 18:37:59'),
(484, 'sale', 'insert', 29, 1, '2025-12-29 18:39:34'),
(485, 'sale', 'insert', 30, 1, '2025-12-29 18:39:51'),
(486, 'sale', 'update', 29, 1, '2025-12-30 01:30:39'),
(487, 'sale', 'update', 30, 1, '2025-12-30 01:30:45'),
(488, 'purchase_order', 'update', 2, 1, '2025-12-30 05:20:32'),
(489, 'purchase_order', 'update', 2, 1, '2025-12-30 05:25:26'),
(490, 'sale', 'insert', 31, 1, '2025-12-30 05:43:08'),
(491, 'purchase', 'insert', 28, 240776, '2025-12-30 06:51:34'),
(492, 'purchase_order', 'insert', 4, 1, '2025-12-30 06:52:25'),
(493, 'sales_order', 'insert', 3, 1, '2025-12-30 06:55:02'),
(494, 'sales_order', 'update', 2, 1, '2025-12-30 06:55:30'),
(495, 'sale', 'insert', 32, 240776, '2025-12-30 06:56:10'),
(496, 'sales_order', 'insert', 4, 1, '2025-12-30 06:56:42'),
(497, 'sale', 'insert', 33, 1, '2025-12-30 14:07:52'),
(498, 'sale', 'insert', 34, 1, '2025-12-30 14:08:01'),
(499, 'sale', 'insert', 35, 1, '2025-12-30 14:09:36'),
(500, 'sale', 'update', 35, 1, '2025-12-30 14:10:53'),
(501, 'sale', 'update', 34, 1, '2025-12-30 14:10:59'),
(502, 'sale', 'update', 33, 1, '2025-12-30 14:11:05'),
(503, 'purchase_order', 'update', 2, 1, '2026-01-03 19:57:13'),
(504, 'purchase_order', 'update', 2, 1, '2026-01-03 19:57:20'),
(505, 'sales_order', 'update', 4, 1, '2026-01-03 20:13:30'),
(506, 'sales_order', 'update', 4, 1, '2026-01-03 20:13:36'),
(507, 'sale', 'insert', 36, 1, '2026-01-03 20:28:04'),
(508, 'stock', 'insert', 61, 1, '2026-01-03 21:08:47'),
(509, 'purchase_order', 'insert', 5, 1, '2026-01-04 02:51:01'),
(510, 'purchase_order', 'update', 5, 1, '2026-01-04 03:06:36'),
(511, 'purchase', 'insert', 29, 1, '2026-01-04 03:16:25'),
(512, 'GRN', 'insert', 25, 1, '2026-01-04 03:22:31'),
(513, 'purchase', 'insert', 30, 1, '2026-01-04 05:20:04'),
(514, 'sale', 'insert', 37, 1, '2026-01-04 05:21:16'),
(515, 'purchase', 'update', 30, 1, '2026-01-04 05:29:16'),
(516, 'purchase_return', 'insert', 1, 1, '2026-01-04 15:14:44'),
(517, 'purchase_return', 'update', 1, 1, '2026-01-04 15:17:17'),
(518, 'sales_return', 'insert', 7, 1, '2026-01-04 20:47:49'),
(519, 'purchase_return', 'insert', 2, 1, '2026-01-04 15:21:28'),
(520, 'purchase_return', 'update', 2, 1, '2026-01-04 20:53:39'),
(521, 'sales_return', 'update', 7, 1, '2026-01-04 20:55:36'),
(522, 'purchase_return', 'update', 2, 1, '2026-01-04 20:58:21'),
(523, 'sales_order', 'insert', 5, 1, '2026-01-05 14:11:02'),
(524, 'sales_order', 'update', 4, 1, '2026-01-05 14:11:18'),
(525, 'sales_order', 'insert', 6, 1, '2026-01-05 14:27:57'),
(526, 'sale', 'insert', 38, 1, '2026-01-05 14:28:54'),
(527, 'stock', 'insert', 62, 1, '2026-01-05 23:00:44'),
(528, 'stock', 'insert', 63, 1, '2026-01-06 01:02:52'),
(529, 'stock', 'delete', 63, 1, '2026-01-06 01:04:05'),
(530, 'purchase', 'insert', 31, 1, '2026-01-06 07:47:16'),
(531, 'purchase_return', 'insert', 3, 1, '2026-01-06 02:19:01'),
(532, 'sale', 'insert', 39, 1, '2026-01-06 07:55:04'),
(533, 'sales_return', 'insert', 8, 1, '2026-01-06 07:56:09'),
(534, 'purchase', 'insert', 32, 1, '2026-01-06 09:13:51'),
(535, 'purchase_return', 'insert', 4, 1, '2026-01-06 03:46:59'),
(536, 'purchase_return', 'update', 4, 1, '2026-01-06 09:19:42'),
(537, 'sale', 'insert', 40, 1, '2026-01-06 09:21:09'),
(538, 'sales_return', 'insert', 9, 1, '2026-01-06 09:21:50'),
(539, 'stock', 'insert', 64, 1, '2026-01-06 21:06:40'),
(540, 'stock', 'insert', 65, 1, '2026-01-06 21:09:03'),
(541, 'stock', 'insert', 66, 1, '2026-01-06 21:16:31'),
(542, 'stock', 'insert', 67, 1, '2026-01-06 21:27:57'),
(543, 'sale', 'insert', 41, 1, '2026-01-07 02:59:28'),
(545, 'sale', 'update', 41, 1, '2026-01-07 03:03:55'),
(546, 'sale', 'update', 41, 1, '2026-01-07 03:04:37'),
(547, 'sale', 'update', 41, 1, '2026-01-07 03:05:40'),
(548, 'sale', 'insert', 42, 1, '2026-01-07 03:07:24'),
(549, 'sale', 'update', 42, 1, '2026-01-07 03:07:50'),
(550, 'stock', 'insert', 69, 1, '2026-01-06 21:40:42'),
(551, 'sale', 'insert', 43, 1, '2026-01-07 03:11:49'),
(552, 'stock', 'insert', 70, 1, '2026-01-06 21:44:33'),
(553, 'sale', 'insert', 44, 1, '2026-01-07 03:18:16'),
(554, 'purchase_order', 'insert', 6, 1, '2026-01-07 03:28:52'),
(555, 'purchase', 'insert', 33, 1, '2026-01-07 03:29:32'),
(556, 'purchase_return', 'insert', 5, 1, '2026-01-06 22:01:57'),
(557, 'sales_order', 'insert', 7, 1, '2026-01-07 03:49:51'),
(558, 'sale', 'insert', 45, 1, '2026-01-07 03:51:52'),
(559, 'sales_return', 'insert', 10, 1, '2026-01-07 03:55:33'),
(560, 'GRN', 'insert', 26, 1, '2026-01-07 04:00:58'),
(561, 'GDN', 'insert', 32, 1, '2026-01-07 04:01:45'),
(562, 'GDN', 'insert', 33, 1, '2026-01-07 04:06:19'),
(563, 'GRN', 'insert', 27, 1, '2026-01-07 04:08:33'),
(564, 'purchase_order', 'insert', 7, 1, '2026-01-17 07:40:34'),
(565, 'sale', 'insert', 46, 1, '2026-01-17 17:00:50'),
(566, 'sales_return', 'insert', 11, 1, '2026-01-17 17:03:06'),
(567, 'sale', 'insert', 47, 1, '2026-01-25 06:37:15'),
(568, 'purchase_order', 'insert', 8, 1, '2026-02-01 07:55:53'),
(569, 'purchase', 'insert', 34, 1, '2026-02-01 07:59:15'),
(570, 'GRN', 'insert', 28, 1, '2026-02-01 08:00:40'),
(571, 'sales_order', 'insert', 8, 1, '2026-02-01 08:01:20'),
(572, 'sale', 'insert', 48, 1, '2026-02-01 08:01:46'),
(573, 'purchase', 'insert', 35, 1, '2026-02-04 15:15:08'),
(574, 'sale', 'insert', 49, 240776, '2026-02-08 14:24:30'),
(575, 'sale', 'insert', 50, 1, '2026-02-08 14:29:51'),
(576, 'purchase', 'insert', 36, 1, '2026-02-10 12:46:37'),
(577, 'sale', 'insert', 51, 1, '2026-02-10 12:47:04'),
(578, 'sale', 'insert', 52, 2680, '2026-02-10 16:41:20'),
(579, 'purchase_order', 'insert', 9, 1, '2026-02-18 13:41:32'),
(580, 'sales_order', 'update', 4, 1, '2026-03-02 13:21:24'),
(581, 'sale', 'insert', 53, 2680, '2026-03-03 10:23:15'),
(582, 'sales_order', 'insert', 9, 1, '2026-03-03 10:31:20'),
(583, 'sale', 'insert', 54, 1, '2026-03-03 10:32:22'),
(584, 'sale', 'update', 54, 1, '2026-03-03 10:33:46'),
(585, 'sale', 'insert', 55, 2680, '2026-03-03 11:51:38'),
(586, 'sale', 'insert', 56, 1, '2026-03-11 11:53:34'),
(587, 'purchase', 'insert', 37, 1, '2026-03-15 08:12:14'),
(588, 'sale', 'insert', 57, 1, '2026-03-16 10:23:08'),
(589, 'sale', 'insert', 58, 1, '2026-03-16 10:24:32'),
(590, 'sale', 'insert', 59, 1, '2026-03-16 10:25:36'),
(591, 'sale', 'insert', 60, 1, '2026-03-16 10:27:34'),
(592, 'sale', 'insert', 61, 1, '2026-03-17 05:55:43'),
(593, 'sale', 'insert', 62, 1, '2026-03-17 11:51:52'),
(594, 'purchase', 'insert', 38, 1, '2026-03-21 01:48:03'),
(595, 'GRN', 'insert', 29, 1, '2026-03-21 02:02:14'),
(596, 'GRN', 'insert', 30, 1, '2026-03-21 02:03:45'),
(597, 'GRN', 'insert', 31, 1, '2026-03-21 02:17:41'),
(598, 'grn', 'update', 31, 1, '2026-03-21 02:18:21'),
(599, 'GRN', 'insert', 32, 1, '2026-03-21 03:20:39'),
(600, 'grn', 'delete', 32, 1, '2026-03-20 22:51:08'),
(601, 'GRN', 'insert', 33, 1, '2026-03-21 03:21:25'),
(602, 'purchase', 'insert', 1, 1, '2026-03-21 05:08:44'),
(603, 'GRN', 'insert', 1, 1, '2026-03-21 05:11:30'),
(604, 'purchase', 'update', 1, 1, '2026-03-21 07:23:42'),
(605, 'grn', 'delete', 1, 1, '2026-03-21 03:26:24'),
(606, 'purchase', 'update', 1, 1, '2026-03-21 07:57:20'),
(607, 'purchase', 'delete', 1, 1, '2026-03-21 03:28:11'),
(608, 'purchase', 'insert', 2, 1, '2026-03-21 07:58:55'),
(609, 'purchase', 'update', 2, 1, '2026-03-21 07:59:10'),
(610, 'purchase', 'update', 2, 1, '2026-03-21 08:00:27'),
(611, 'purchase', 'update', 2, 1, '2026-03-21 08:00:48'),
(612, 'GRN', 'insert', 2, 1, '2026-03-21 08:02:24'),
(613, 'purchase', 'update', 2, 1, '2026-03-21 08:04:31'),
(614, 'purchase', 'update', 2, 1, '2026-03-21 08:06:54'),
(615, 'purchase', 'update', 2, 1, '2026-03-21 08:11:08'),
(616, 'purchase', 'insert', 3, 1, '2026-03-21 08:31:33'),
(617, 'GRN', 'insert', 3, 1, '2026-03-21 08:32:30'),
(618, 'grn', 'update', 3, 1, '2026-03-21 08:32:51'),
(619, 'grn', 'update', 3, 1, '2026-03-21 09:24:54'),
(620, 'grn', 'update', 2, 1, '2026-03-21 09:25:10'),
(621, 'purchase', 'update', 2, 1, '2026-03-21 12:05:44'),
(622, 'GRN', 'insert', 4, 1, '2026-03-21 12:06:27'),
(623, 'grn', 'update', 4, 1, '2026-03-21 12:06:47'),
(624, 'grn', 'update', 4, 1, '2026-03-21 12:07:04'),
(625, 'GRN', 'insert', 5, 1, '2026-03-21 12:08:12'),
(626, 'grn', 'update', 5, 1, '2026-03-21 12:08:55'),
(627, 'grn', 'update', 5, 1, '2026-03-21 12:09:21'),
(628, 'sale', 'insert', 1, 1, '2026-03-21 13:50:37'),
(629, 'GDN', 'insert', 1, 1, '2026-03-21 14:09:03'),
(630, 'gdn', 'delete', 1, 1, '2026-03-21 09:42:00'),
(631, 'GDN', 'insert', 2, 1, '2026-03-21 14:12:22'),
(632, 'gdn', 'delete', 2, 1, '2026-03-21 10:20:47'),
(633, 'GDN', 'insert', 3, 1, '2026-03-21 14:56:50'),
(634, 'GDN', 'insert', 4, 1, '2026-03-21 14:58:26'),
(635, 'gdn', 'update', 4, 1, '2026-03-21 14:58:45'),
(636, 'gdn', 'update', 3, 1, '2026-03-21 14:59:18'),
(637, 'sale', 'update', 1, 1, '2026-03-21 15:02:21'),
(638, 'GDN', 'insert', 5, 1, '2026-03-21 15:07:27'),
(639, 'sales_return', 'insert', 1, 1, '2026-03-21 15:22:44'),
(640, 'purchase', 'update', 3, 1, '2026-03-21 16:05:50'),
(641, 'purchase_order', 'insert', 1, 1, '2026-03-21 16:14:03'),
(642, 'grn', 'delete', 2, 1, '2026-03-21 21:59:41'),
(643, 'grn', 'delete', 3, 1, '2026-03-21 21:59:45'),
(644, 'grn', 'delete', 4, 1, '2026-03-21 21:59:48'),
(645, 'grn', 'delete', 5, 1, '2026-03-21 21:59:52'),
(646, 'GRN', 'insert', 6, 1, '2026-03-22 02:30:13'),
(647, 'GRN', 'insert', 7, 1, '2026-03-22 02:31:33'),
(648, 'grn', 'delete', 7, 1, '2026-03-21 22:03:31'),
(649, 'GRN', 'insert', 8, 1, '2026-03-22 02:33:55'),
(650, 'gdn', 'delete', 5, 1, '2026-03-21 23:10:41'),
(651, 'gdn', 'delete', 3, 1, '2026-03-21 23:11:03'),
(652, 'gdn', 'delete', 4, 1, '2026-03-21 23:11:09'),
(653, 'grn', 'update', 6, 1, '2026-03-22 03:42:07'),
(654, 'grn', 'update', 6, 1, '2026-03-22 03:51:48'),
(655, 'grn', 'update', 6, 1, '2026-03-22 03:52:17'),
(656, 'grn', 'update', 6, 1, '2026-03-22 03:55:23'),
(657, 'grn', 'update', 6, 1, '2026-03-22 03:56:33'),
(658, 'GRN', 'insert', 9, 1, '2026-03-22 03:57:57'),
(659, 'grn', 'update', 9, 1, '2026-03-22 04:06:42'),
(660, 'grn', 'update', 9, 1, '2026-03-22 04:46:53'),
(661, 'grn', 'update', 9, 1, '2026-03-22 05:11:01'),
(662, 'grn', 'update', 9, 1, '2026-03-22 05:11:25'),
(663, 'grn', 'update', 9, 1, '2026-03-22 05:11:59'),
(664, 'grn', 'update', 9, 1, '2026-03-22 05:12:21'),
(665, 'grn', 'update', 9, 1, '2026-03-22 05:13:00'),
(666, 'grn', 'update', 9, 1, '2026-03-22 05:47:41'),
(667, 'grn', 'update', 6, 1, '2026-03-22 05:48:38'),
(668, 'grn', 'update', 9, 1, '2026-03-22 05:51:12'),
(669, 'GRN', 'insert', 10, 1, '2026-03-22 06:22:34'),
(670, 'grn', 'update', 10, 1, '2026-03-22 07:02:13'),
(671, 'grn', 'update', 9, 1, '2026-03-22 08:04:25'),
(672, 'GRN', 'insert', 11, 1, '2026-03-22 08:19:55'),
(673, 'grn', 'update', 11, 1, '2026-03-22 08:22:11'),
(674, 'grn', 'update', 11, 1, '2026-03-22 08:22:26'),
(675, 'grn', 'update', 8, 1, '2026-03-22 08:23:02'),
(676, 'grn', 'update', 8, 1, '2026-03-22 08:23:25'),
(677, 'grn', 'update', 9, 1, '2026-03-22 08:24:59'),
(678, 'grn', 'update', 11, 1, '2026-03-22 08:25:10'),
(679, 'grn', 'update', 11, 1, '2026-03-22 08:32:06'),
(680, 'grn', 'update', 11, 1, '2026-03-22 08:38:17'),
(681, 'grn', 'update', 11, 1, '2026-03-22 08:38:36'),
(682, 'grn', 'update', 8, 1, '2026-03-22 08:50:32'),
(683, 'grn', 'update', 10, 1, '2026-03-22 08:51:16'),
(684, 'grn', 'update', 10, 1, '2026-03-22 08:51:29'),
(685, 'grn', 'update', 10, 1, '2026-03-22 08:51:44'),
(686, 'GDN', 'insert', 6, 1, '2026-03-22 12:56:20'),
(687, 'gdn', 'update', 6, 1, '2026-03-22 13:12:38'),
(688, 'grn', 'update', 11, 1, '2026-03-22 13:54:11'),
(689, 'gdn', 'update', 6, 1, '2026-03-22 14:06:02'),
(690, 'purchase_return', 'insert', 1, 1, '2026-03-22 09:47:00'),
(691, 'GDN', 'insert', 7, 1, '2026-03-22 14:17:41'),
(692, 'gdn', 'update', 7, 1, '2026-03-22 14:26:53'),
(693, 'gdn', 'update', 7, 1, '2026-03-22 14:32:01'),
(694, 'gdn', 'update', 7, 1, '2026-03-22 15:01:14'),
(695, 'gdn', 'update', 6, 1, '2026-03-22 15:02:22'),
(696, 'gdn', 'update', 7, 1, '2026-03-22 15:02:41'),
(697, 'GDN', 'insert', 8, 1, '2026-03-22 15:03:31'),
(698, 'gdn', 'update', 8, 1, '2026-03-22 15:04:01'),
(699, 'gdn', 'update', 7, 1, '2026-03-22 15:04:29'),
(700, 'sale', 'insert', 2, 1, '2026-03-22 15:06:32'),
(701, 'GDN', 'insert', 9, 1, '2026-03-22 15:10:16'),
(702, 'gdn', 'update', 9, 1, '2026-03-22 15:11:55'),
(703, 'service_order', 'insert', 1, 1, '2026-03-22 16:24:38'),
(704, 'service', 'insert', 15, 1, '2026-03-22 16:30:16'),
(705, 'sale', 'insert', 1, 1, '2026-03-23 04:45:28'),
(706, 'sales_order', 'insert', 1, 1, '2026-03-23 05:45:28'),
(707, 'purchase_order', 'insert', 1, 1, '2026-03-23 06:10:52'),
(708, 'purchase', 'insert', 1, 1, '2026-03-23 06:12:19'),
(709, 'sales_order', 'insert', 2, 1, '2026-03-23 06:12:52'),
(710, 'purchase_return', 'insert', 1, 1, '2026-03-23 00:58:05'),
(711, 'sale', 'insert', 2, 1, '2026-03-23 06:29:29'),
(712, 'sales_return', 'insert', 1, 1, '2026-03-23 06:30:02'),
(713, 'GRN', 'insert', 1, 1, '2026-03-23 06:34:52'),
(714, 'grn', 'update', 1, 1, '2026-03-23 06:36:23'),
(715, 'grn', 'update', 1, 1, '2026-03-23 06:37:19'),
(716, 'grn', 'update', 1, 1, '2026-03-23 06:38:14'),
(717, 'grn', 'update', 1, 1, '2026-03-23 06:39:06'),
(718, 'grn', 'update', 1, 1, '2026-03-23 06:40:12'),
(719, 'grn', 'update', 1, 1, '2026-03-23 06:42:21'),
(720, 'grn', 'update', 1, 1, '2026-03-23 06:46:18'),
(721, 'GRN', 'insert', 2, 1, '2026-03-23 06:47:01'),
(722, 'grn', 'update', 2, 1, '2026-03-23 06:48:58'),
(723, 'GRN', 'insert', 3, 1, '2026-03-23 06:49:41'),
(724, 'grn', 'update', 3, 1, '2026-03-23 06:50:24'),
(725, 'GRN', 'insert', 4, 1, '2026-03-23 06:51:17'),
(726, 'GRN', 'insert', 5, 1, '2026-03-23 06:52:48'),
(727, 'GRN', 'insert', 6, 1, '2026-03-23 06:54:23'),
(728, 'GRN', 'insert', 7, 1, '2026-03-23 06:55:14'),
(729, 'GRN', 'insert', 8, 1, '2026-03-23 06:58:22'),
(730, 'GRN', 'insert', 9, 1, '2026-03-23 06:59:48'),
(731, 'GRN', 'insert', 10, 1, '2026-03-23 07:01:02'),
(732, 'GRN', 'insert', 11, 1, '2026-03-23 07:01:39'),
(733, 'GRN', 'insert', 12, 1, '2026-03-23 07:02:22'),
(734, 'GRN', 'insert', 13, 1, '2026-03-23 07:03:02'),
(735, 'grn', 'delete', 13, 1, '2026-03-23 01:33:29'),
(736, 'GDN', 'insert', 1, 1, '2026-03-23 10:22:52'),
(737, 'gdn', 'update', 1, 1, '2026-03-23 10:23:12'),
(738, 'GDN', 'insert', 2, 1, '2026-03-23 10:24:38'),
(739, 'GDN', 'insert', 3, 1, '2026-03-23 10:28:19'),
(740, 'GDN', 'insert', 4, 1, '2026-03-23 10:29:32'),
(741, 'gdn', 'delete', 4, 1, '2026-03-23 05:01:00'),
(742, 'GDN', 'insert', 5, 1, '2026-03-23 10:32:15'),
(743, 'GDN', 'insert', 6, 1, '2026-03-23 10:33:56'),
(744, 'GDN', 'insert', 7, 1, '2026-03-23 10:35:25'),
(745, 'GDN', 'insert', 8, 1, '2026-03-23 10:36:35'),
(746, 'gdn', 'delete', 8, 1, '2026-03-23 05:07:54'),
(747, 'GDN', 'insert', 9, 1, '2026-03-23 10:38:41'),
(748, 'GDN', 'insert', 10, 1, '2026-03-23 10:41:14'),
(749, 'GDN', 'insert', 11, 1, '2026-03-23 10:46:45'),
(750, 'GDN', 'insert', 12, 1, '2026-03-23 10:49:55'),
(751, 'GDN', 'insert', 13, 1, '2026-03-23 10:54:21'),
(752, 'GDN', 'insert', 14, 1, '2026-03-23 10:54:58'),
(753, 'service', 'insert', 1, 1, '2026-03-24 10:04:11'),
(754, 'purchase', 'insert', 2, 1, '2026-03-24 10:33:17'),
(755, 'purchase', 'update', 2, 1, '2026-03-24 10:33:51'),
(756, 'purchase_return', 'insert', 2, 1, '2026-03-24 05:04:37'),
(757, 'sale', 'insert', 3, 1, '2026-03-24 10:36:29'),
(758, 'sales_return', 'insert', 2, 1, '2026-03-24 10:37:01'),
(759, 'sales_return', 'update', 2, 1, '2026-03-24 10:37:49'),
(760, 'sales_return', 'update', 2, 1, '2026-03-24 10:38:24'),
(761, 'sales_return', 'update', 2, 1, '2026-03-24 10:38:48'),
(762, 'sales_return', 'update', 2, 1, '2026-03-24 10:38:58'),
(763, 'sales_order', 'update', 1, 1, '2026-03-24 11:23:05'),
(764, 'purchase', 'insert', 3, 1, '2026-03-24 12:13:25'),
(765, 'purchase', 'insert', 4, 1, '2026-03-24 12:14:19'),
(766, 'purchase', 'update', 4, 1, '2026-03-24 12:14:39'),
(767, 'purchase', 'update', 4, 1, '2026-03-24 12:15:11'),
(768, 'purchase', 'insert', 5, 1, '2026-03-24 12:18:41'),
(769, 'purchase', 'insert', 6, 1, '2026-03-24 12:19:45'),
(770, 'purchase', 'insert', 7, 1, '2026-03-24 12:20:19'),
(771, 'purchase', 'update', 2, 1, '2026-03-24 12:20:47'),
(772, 'purchase', 'update', 1, 1, '2026-03-24 12:21:10'),
(773, 'purchase', 'insert', 8, 1, '2026-03-24 12:22:52'),
(774, 'purchase', 'insert', 9, 1, '2026-03-24 12:23:28'),
(775, 'stock', 'insert', 1, 1, '2026-03-24 07:28:23'),
(776, 'stock', 'insert', 2, 1, '2026-03-24 07:29:31'),
(777, 'stock', 'insert', 3, 1, '2026-03-24 07:32:10'),
(778, 'stock', 'insert', 4, 1, '2026-03-24 07:33:16'),
(779, 'stock', 'insert', 5, 1, '2026-03-24 07:34:23'),
(780, 'service', 'insert', 2, 1, '2026-03-24 13:06:30'),
(781, 'service', 'insert', 3, 1, '2026-03-24 13:06:59'),
(782, 'service', 'insert', 4, 1, '2026-03-24 13:07:32'),
(783, 'service', 'insert', 5, 1, '2026-03-24 13:07:56'),
(784, 'service', 'insert', 6, 1, '2026-03-24 13:08:31'),
(785, 'service', 'insert', 7, 1, '2026-03-24 13:09:26'),
(786, 'service', 'insert', 8, 1, '2026-03-24 13:09:53'),
(787, 'sale', 'insert', 4, 1, '2026-03-24 13:24:30'),
(788, 'sale', 'insert', 5, 1, '2026-03-24 13:25:05'),
(789, 'sale', 'insert', 6, 1, '2026-03-24 13:25:38'),
(790, 'sale', 'insert', 7, 1, '2026-03-24 13:26:09'),
(791, 'sale', 'insert', 8, 1, '2026-03-24 13:26:53'),
(792, 'sale', 'insert', 9, 1, '2026-03-24 13:27:29'),
(793, 'sale', 'insert', 10, 1, '2026-03-24 13:28:32'),
(794, 'purchase_return', 'insert', 3, 1, '2026-03-24 07:59:50'),
(795, 'purchase_return', 'insert', 4, 1, '2026-03-24 08:00:16'),
(796, 'sales_return', 'insert', 3, 1, '2026-03-24 13:31:16'),
(797, 'sales_return', 'insert', 4, 1, '2026-03-24 13:31:42'),
(798, 'GDN', 'insert', 15, 1, '2026-03-24 17:27:54'),
(799, 'sales_order', 'insert', 3, 1, '2026-03-24 17:30:37'),
(800, 'sale', 'insert', 11, 1, '2026-03-24 17:31:26'),
(801, 'GDN', 'insert', 16, 1, '2026-03-24 17:33:20'),
(802, 'stock', 'insert', 6, 1, '2026-03-26 01:04:44'),
(803, 'GRN', 'insert', 14, 1, '2026-03-26 17:04:07'),
(804, 'purchase', 'insert', 10, 1, '2026-03-27 09:14:02'),
(805, 'purchase', 'insert', 11, 1, '2026-03-27 09:14:56'),
(806, 'purchase', 'insert', 12, 1, '2026-03-27 09:15:46'),
(807, 'purchase', 'insert', 13, 1, '2026-03-27 09:17:44'),
(808, 'purchase', 'insert', 14, 1, '2026-03-27 09:21:53'),
(809, 'purchase', 'insert', 15, 1, '2026-03-27 09:23:18'),
(810, 'purchase', 'delete', 15, 2680, '2026-03-27 04:19:15'),
(811, 'purchase', 'delete', 13, 2680, '2026-03-27 04:19:21'),
(812, 'purchase', 'delete', 12, 2680, '2026-03-27 04:19:27'),
(813, 'purchase', 'delete', 10, 2680, '2026-03-27 04:19:47'),
(814, 'purchase', 'insert', 16, 240776, '2026-03-27 09:53:11'),
(815, 'purchase', 'insert', 17, 240776, '2026-03-27 10:01:45'),
(816, 'purchase', 'insert', 18, 240776, '2026-03-27 10:03:04'),
(817, 'purchase', 'insert', 19, 240776, '2026-03-27 10:03:39'),
(818, 'purchase', 'update', 17, 240776, '2026-03-27 10:07:26'),
(819, 'purchase', 'delete', 14, 1, '2026-03-27 07:39:49'),
(820, 'purchase', 'delete', 11, 1, '2026-03-27 07:39:55'),
(821, 'purchase', 'insert', 20, 240776, '2026-03-27 13:11:04'),
(822, 'purchase', 'insert', 21, 240776, '2026-03-27 13:11:50'),
(823, 'purchase', 'insert', 22, 240776, '2026-03-27 13:12:29'),
(824, 'purchase', 'insert', 23, 240776, '2026-03-27 13:16:42'),
(825, 'purchase', 'insert', 24, 240776, '2026-03-27 13:17:39'),
(826, 'purchase', 'insert', 25, 240776, '2026-03-27 13:20:05'),
(827, 'purchase', 'delete', 16, 240776, '2026-03-27 07:51:12'),
(828, 'purchase', 'delete', 18, 240776, '2026-03-27 07:51:39'),
(829, 'purchase', 'delete', 17, 240776, '2026-03-27 07:51:55'),
(830, 'purchase', 'insert', 26, 240776, '2026-03-27 13:22:28'),
(831, 'purchase', 'insert', 27, 240776, '2026-03-27 13:22:59'),
(832, 'purchase', 'insert', 28, 240776, '2026-03-27 13:23:27'),
(833, 'purchase_return', 'insert', 5, 240776, '2026-03-27 07:54:58'),
(834, 'purchase_return', 'insert', 6, 240776, '2026-03-27 07:55:21'),
(835, 'purchase_return', 'insert', 7, 240776, '2026-03-27 07:55:56'),
(836, 'purchase_return', 'update', 7, 240776, '2026-03-27 07:58:43'),
(837, 'purchase_return', 'update', 6, 240776, '2026-03-27 07:58:57'),
(838, 'purchase_return', 'update', 5, 240776, '2026-03-27 07:59:11'),
(839, 'purchase', 'update', 21, 240776, '2026-03-27 13:29:27'),
(840, 'purchase', 'update', 24, 240776, '2026-03-27 13:29:59'),
(841, 'purchase', 'update', 27, 240776, '2026-03-27 13:30:41'),
(842, 'purchase_return', 'insert', 8, 240776, '2026-03-27 08:01:28'),
(843, 'purchase_return', 'insert', 9, 240776, '2026-03-27 08:02:15'),
(844, 'purchase_return', 'insert', 10, 240776, '2026-03-27 08:06:05'),
(845, 'service', 'insert', 9, 240776, '2026-03-27 13:37:05'),
(846, 'service', 'insert', 10, 240776, '2026-03-27 13:37:25'),
(847, 'service', 'insert', 11, 240776, '2026-03-27 13:37:47'),
(848, 'service', 'insert', 12, 240776, '2026-03-27 13:38:10'),
(849, 'service', 'insert', 13, 240776, '2026-03-27 13:38:35'),
(850, 'sale', 'insert', 12, 240776, '2026-03-27 13:39:34'),
(851, 'sale', 'insert', 13, 240776, '2026-03-27 13:40:06'),
(852, 'sale', 'insert', 14, 240776, '2026-03-27 13:40:37'),
(853, 'sale', 'update', 13, 240776, '2026-03-27 13:41:24'),
(854, 'sale', 'update', 12, 240776, '2026-03-27 13:42:02'),
(855, 'sale', 'insert', 15, 240776, '2026-03-27 13:43:03'),
(856, 'sale', 'insert', 16, 240776, '2026-03-27 13:43:33'),
(857, 'sale', 'update', 16, 240776, '2026-03-27 13:44:28'),
(858, 'sale', 'insert', 17, 240776, '2026-03-27 13:48:45'),
(859, 'sale', 'insert', 18, 240776, '2026-03-27 13:49:15'),
(860, 'sale', 'insert', 19, 240776, '2026-03-27 13:49:51'),
(861, 'sale', 'insert', 20, 240776, '2026-03-27 13:50:18'),
(862, 'sales_return', 'insert', 5, 240776, '2026-03-27 13:51:49'),
(863, 'sales_return', 'update', 5, 240776, '2026-03-27 13:52:30'),
(864, 'sales_return', 'insert', 6, 240776, '2026-03-27 13:54:06'),
(865, 'sales_return', 'insert', 7, 240776, '2026-03-27 13:55:11'),
(866, 'sales_return', 'insert', 8, 240776, '2026-03-27 13:55:39'),
(867, 'GRN', 'insert', 15, 240776, '2026-03-27 13:56:30'),
(868, 'GRN', 'insert', 16, 240776, '2026-03-27 13:58:41'),
(869, 'GRN', 'insert', 17, 240776, '2026-03-27 13:59:05'),
(870, 'GRN', 'insert', 18, 240776, '2026-03-27 13:59:23'),
(871, 'GDN', 'insert', 17, 240776, '2026-03-27 13:59:56'),
(872, 'GDN', 'insert', 18, 240776, '2026-03-27 14:00:22'),
(873, 'GDN', 'insert', 19, 240776, '2026-03-27 14:00:51'),
(874, 'GDN', 'insert', 20, 240776, '2026-03-27 14:01:24'),
(875, 'gdn', 'update', 19, 240776, '2026-03-27 14:01:50'),
(876, 'sale', 'update', 11, 1, '2026-03-28 12:29:09'),
(877, 'stock', 'insert', 7, 1, '2026-03-29 00:35:53'),
(878, 'stock', 'delete', 7, 1, '2026-03-29 00:36:12'),
(879, 'stock', 'insert', 8, 1, '2026-03-29 00:37:05'),
(880, 'service_order', 'insert', 1, 1, '2026-03-29 16:00:26'),
(881, 'service_order', 'insert', 2, 1, '2026-03-29 16:01:44'),
(882, 'service_order', 'update', 1, 1, '2026-03-29 16:03:24'),
(883, 'sale', 'update', 11, 1, '2026-03-30 13:30:48'),
(884, 'stock', 'delete', 6, 1, '2026-04-01 00:29:46'),
(885, 'purchase', 'delete', 9, 1, '2026-04-01 01:11:57'),
(886, 'GDN', 'insert', 21, 1, '2026-04-01 06:45:11'),
(887, 'GRN', 'insert', 19, 1, '2026-04-01 06:46:36'),
(888, 'sales_return', 'update', 4, 1, '2026-04-01 06:50:10'),
(889, 'purchase_return', 'update', 3, 1, '2026-04-01 06:50:42'),
(890, 'purchase', 'insert', 29, 1, '2026-04-01 10:41:51'),
(891, 'sale', 'insert', 21, 1, '2026-04-01 10:45:22'),
(892, 'stock', 'insert', 9, 1, '2026-04-01 22:40:23'),
(893, 'GRN', 'insert', 20, 1, '2026-04-02 18:09:16'),
(894, 'GRN', 'insert', 21, 1, '2026-04-02 18:13:24'),
(895, 'service', 'insert', 14, 1, '2026-04-05 13:10:26'),
(896, 'service_order', 'insert', 3, 1, '2026-04-05 13:12:07'),
(897, 'purchase_order', 'insert', 2, 1, '2026-04-05 13:13:46'),
(898, 'purchase_return', 'insert', 11, 1, '2026-04-05 07:53:48'),
(899, 'purchase_return', 'insert', 12, 1, '2026-04-05 09:29:48'),
(900, 'sale', 'insert', 22, 1, '2026-04-06 05:34:25'),
(901, 'sale', 'update', 22, 1, '2026-04-06 05:35:49'),
(902, 'sale', 'insert', 23, 1, '2026-04-06 05:36:39'),
(903, 'sale', 'update', 23, 1, '2026-04-06 05:44:13'),
(904, 'sale', 'insert', 24, 1, '2026-04-06 05:49:58'),
(905, 'sale', 'update', 24, 1, '2026-04-06 06:04:18'),
(906, 'sale', 'insert', 25, 1, '2026-04-06 06:05:16');
INSERT INTO `logs` (`id`, `screen`, `operation`, `pid`, `userid`, `lastupdatedate`) VALUES
(907, 'sale', 'insert', 26, 1, '2026-04-06 06:05:59'),
(908, 'GRN', 'insert', 1, 1, '2026-04-07 06:23:14'),
(909, 'GDN', 'insert', 1, 1, '2026-04-07 06:23:43'),
(910, 'purchase', 'insert', 1, 1, '2026-04-07 06:25:25'),
(911, 'purchase_return', 'insert', 1, 1, '2026-04-07 00:55:47'),
(912, 'sale', 'insert', 1, 1, '2026-04-07 06:26:29'),
(913, 'sales_return', 'insert', 1, 1, '2026-04-07 06:28:12'),
(914, 'GDN', 'insert', 2, 1, '2026-04-07 06:30:24'),
(915, 'sale', 'insert', 2, 1, '2026-04-07 06:37:26'),
(916, 'sales_return', 'insert', 2, 1, '2026-04-07 06:38:03'),
(917, 'purchase', 'insert', 2, 1, '2026-04-07 06:38:34'),
(918, 'purchase_return', 'insert', 2, 1, '2026-04-07 01:08:59'),
(919, 'sales_return', 'update', 2, 1, '2026-04-07 07:00:38'),
(920, 'sales_return', 'insert', 3, 1, '2026-04-07 07:00:54'),
(921, 'stock', 'insert', 1, 1, '2026-04-08 10:32:05'),
(922, 'purchase', 'insert', 3, 1, '2026-04-09 05:35:58'),
(923, 'purchase_return', 'insert', 3, 1, '2026-04-09 00:10:09'),
(924, 'sale', 'insert', 3, 1, '2026-04-09 05:47:08'),
(925, 'sales_return', 'insert', 4, 1, '2026-04-09 05:47:54'),
(926, 'service_order', 'insert', 4, 1, '2026-04-10 16:56:26'),
(927, 'service', 'insert', 15, 1, '2026-04-10 16:57:00'),
(928, 'stock', 'insert', 2, 1, '2026-04-10 17:25:42'),
(929, 'GRN', 'insert', 2, 1, '2026-04-10 17:30:01'),
(930, 'purchase', 'insert', 4, 1, '2026-04-12 13:01:13'),
(931, 'purchase_return', 'insert', 4, 1, '2026-04-12 07:32:02'),
(932, 'purchase', 'update', 4, 1, '2026-04-12 13:02:42'),
(933, 'purchase', 'insert', 5, 1, '2026-04-12 13:04:44'),
(934, 'purchase_return', 'insert', 5, 1, '2026-04-12 07:35:22'),
(935, 'sale', 'insert', 4, 1, '2026-04-12 13:07:19'),
(936, 'sale', 'update', 4, 1, '2026-04-12 13:07:54'),
(937, 'sales_return', 'insert', 5, 1, '2026-04-12 13:08:40'),
(938, 'sales_order', 'insert', 1, 1, '2026-04-12 13:27:07'),
(939, 'purchase', 'insert', 6, 1, '2026-04-14 17:48:56'),
(940, 'purchase', 'update', 6, 1, '2026-04-14 17:49:23'),
(941, 'purchase', 'update', 6, 1, '2026-04-14 17:49:57'),
(942, 'purchase', 'update', 6, 1, '2026-04-14 17:50:26'),
(943, 'sale', 'insert', 5, 1, '2026-04-14 17:51:07'),
(944, 'sale', 'update', 5, 1, '2026-04-14 17:51:57'),
(945, 'sale', 'insert', 6, 1, '2026-04-14 17:52:33'),
(946, 'sale', 'insert', 7, 1, '2026-04-14 17:53:31'),
(947, 'sale', 'update', 7, 1, '2026-04-14 17:54:23'),
(948, 'sale', 'update', 7, 1, '2026-04-14 17:55:46'),
(949, 'sale', 'update', 7, 1, '2026-04-14 17:56:05'),
(950, 'sale', 'insert', 8, 1, '2026-04-14 17:57:33'),
(951, 'sale', 'update', 8, 1, '2026-04-14 17:58:06'),
(952, 'service_order', 'insert', 5, 1, '2026-04-17 11:19:24'),
(953, 'service_order', 'update', 5, 1, '2026-04-17 11:24:36'),
(954, 'service', 'insert', 16, 1, '2026-04-17 11:25:07'),
(955, 'service', 'insert', 17, 1, '2026-04-17 11:29:15'),
(956, 'sale', 'insert', 9, 1, '2026-04-21 10:42:02'),
(957, 'service_order', 'insert', 6, 1, '2026-04-21 10:43:02'),
(958, 'service', 'insert', 18, 1, '2026-04-21 10:44:09'),
(959, 'purchase_order', 'insert', 1, 1, '2026-04-22 04:07:26'),
(960, 'sale', 'insert', 10, 1, '2026-04-22 04:50:41'),
(961, 'stock', 'insert', 1, 1, '2026-05-03 02:11:57'),
(962, 'stock', 'delete', 1, 1, '2026-05-03 04:40:23'),
(963, 'purchase', 'insert', 1, 1, '2026-05-06 04:55:18'),
(964, 'sale', 'insert', 1, 1, '2026-05-06 04:57:34'),
(965, 'purchase_return', 'insert', 1, 1, '2026-05-05 23:29:59'),
(966, 'sales_return', 'insert', 1, 1, '2026-05-06 05:00:59'),
(967, 'sale', 'insert', 2, 1, '2026-05-07 07:15:21'),
(968, 'sales_order', 'insert', 1, 1, '2026-05-07 07:34:32'),
(969, 'purchase', 'insert', 2, 1, '2026-05-12 04:56:46'),
(970, 'purchase', 'insert', 3, 1, '2026-05-12 05:04:38'),
(971, 'purchase', 'delete', 2, 1, '2026-05-11 23:34:53'),
(972, 'sale', 'insert', 3, 1, '2026-05-12 05:06:53'),
(973, 'sale', 'insert', 4, 1, '2026-05-12 07:08:39'),
(974, 'sale', 'update', 4, 1, '2026-05-12 07:17:15'),
(975, 'service_order', 'update', 2, 1, '2026-05-12 08:08:42'),
(976, 'service_order', 'update', 2, 1, '2026-05-12 08:08:50'),
(977, 'purchase', 'insert', 4, 240776, '2026-05-12 09:54:36'),
(978, 'sale', 'insert', 5, 240776, '2026-05-12 09:56:17'),
(979, 'sale', 'insert', 6, 240776, '2026-05-12 09:57:35'),
(980, 'sale', 'insert', 7, 1, '2026-05-12 12:04:42'),
(981, 'sale', 'update', 7, 1, '2026-05-12 12:07:31'),
(982, 'service', 'insert', 19, 1, '2026-05-12 12:12:35'),
(983, 'sale', 'insert', 8, 1, '2026-05-12 12:16:40'),
(984, 'stock', 'insert', 2, 1, '2026-05-12 12:21:48'),
(985, 'purchase', 'insert', 5, 1, '2026-05-12 12:32:11'),
(986, 'sale', 'insert', 9, 1, '2026-05-12 12:32:59'),
(987, 'sale', 'insert', 10, 1, '2026-05-12 12:34:04'),
(988, 'sales_return', 'insert', 2, 1, '2026-05-12 12:34:58'),
(989, 'sales_return', 'update', 2, 1, '2026-05-12 12:37:14'),
(990, 'sales_return', 'insert', 3, 1, '2026-05-12 12:37:54'),
(991, 'purchase', 'insert', 6, 1, '2026-05-12 12:40:08'),
(992, 'purchase_return', 'insert', 2, 1, '2026-05-12 07:10:59'),
(993, 'stock', 'insert', 3, 1, '2026-05-12 12:43:00'),
(994, 'stock', 'insert', 4, 1, '2026-05-12 12:44:21'),
(995, 'stock', 'insert', 5, 1, '2026-05-12 12:45:38'),
(996, 'purchase', 'insert', 7, 1, '2026-05-12 12:47:23'),
(997, 'GRN', 'insert', 1, 1, '2026-05-12 12:47:54'),
(998, 'sale', 'insert', 11, 1, '2026-05-12 12:48:51'),
(999, 'sale', 'update', 11, 1, '2026-05-12 12:50:59'),
(1000, 'GDN', 'insert', 1, 1, '2026-05-12 12:51:22'),
(1001, 'stock', 'insert', 6, 1, '2026-05-12 12:52:45'),
(1002, 'stock', 'update', 6, 1, '2026-05-12 12:53:11'),
(1003, 'purchase_return', 'insert', 3, 1, '2026-05-12 07:23:58'),
(1004, 'GDN', 'insert', 2, 1, '2026-05-12 12:54:36'),
(1005, 'sales_return', 'insert', 4, 1, '2026-05-12 13:00:04'),
(1006, 'GRN', 'insert', 2, 1, '2026-05-12 13:02:45'),
(1007, 'stock', 'insert', 7, 1, '2026-05-12 13:07:23'),
(1008, 'stock', 'insert', 8, 1, '2026-05-12 13:08:05'),
(1009, 'stock', 'delete', 8, 1, '2026-05-12 07:38:23'),
(1010, 'purchase', 'insert', 8, 1, '2026-05-15 10:09:30'),
(1011, 'sale', 'insert', 12, 1, '2026-05-15 10:19:38'),
(1012, 'sale', 'update', 12, 1, '2026-05-15 10:21:45'),
(1013, 'sale', 'update', 12, 1, '2026-05-15 10:29:19'),
(1014, 'purchase_order', 'insert', 1, 1, '2026-05-15 10:30:14'),
(1015, 'purchase', 'insert', 9, 1, '2026-05-15 10:31:15'),
(1016, 'purchase', 'insert', 10, 1, '2026-05-15 10:33:56'),
(1017, 'purchase', 'insert', 11, 1, '2026-05-15 10:43:24'),
(1018, 'sale', 'insert', 13, 1, '2026-05-15 10:44:10'),
(1019, 'sale', 'insert', 14, 1, '2026-05-15 10:55:13'),
(1020, 'stock', 'insert', 9, 1, '2026-05-15 11:21:30'),
(1021, 'stock', 'insert', 10, 1, '2026-05-15 11:22:10'),
(1022, 'stock', 'delete', 10, 1, '2026-05-15 05:53:53'),
(1023, 'stock', 'delete', 9, 1, '2026-05-15 05:53:57'),
(1024, 'stock', 'insert', 11, 1, '2026-05-15 11:24:37'),
(1025, 'stock', 'update', 11, 1, '2026-05-15 11:25:49'),
(1026, 'stock', 'update', 11, 1, '2026-05-15 11:26:22'),
(1027, 'stock', 'update', 11, 1, '2026-05-15 11:27:16'),
(1028, 'stock', 'update', 11, 1, '2026-05-15 11:28:13'),
(1029, 'stock', 'update', 11, 1, '2026-05-15 11:30:04'),
(1030, 'stock', 'update', 11, 1, '2026-05-15 11:35:17'),
(1031, 'stock', 'update', 11, 1, '2026-05-15 11:35:41'),
(1032, 'stock', 'update', 11, 1, '2026-05-15 11:37:44'),
(1033, 'stock', 'update', 11, 1, '2026-05-15 11:38:53'),
(1034, 'stock', 'update', 11, 1, '2026-05-15 11:39:09'),
(1035, 'stock', 'update', 11, 1, '2026-05-15 11:39:44'),
(1036, 'stock', 'insert', 12, 1, '2026-05-15 12:06:05'),
(1037, 'stock', 'update', 12, 1, '2026-05-15 12:20:07'),
(1038, 'stock', 'insert', 13, 1, '2026-05-15 12:31:19'),
(1039, 'sale', 'insert', 15, 1, '2026-05-15 12:35:04'),
(1040, 'sale', 'insert', 16, 1, '2026-05-15 12:37:09'),
(1041, 'sale', 'insert', 17, 1, '2026-05-15 12:43:06'),
(1042, 'sale', 'insert', 18, 1, '2026-05-15 12:43:51'),
(1043, 'sale', 'insert', 19, 1, '2026-05-15 12:46:01'),
(1044, 'sale', 'insert', 20, 1, '2026-05-16 06:05:00'),
(1045, 'sale', 'insert', 21, 240776, '2026-05-16 06:06:01'),
(1046, 'sale', 'insert', 22, 240776, '2026-05-16 06:07:33'),
(1047, 'payment_voucher', 'update', 1, 1, '2026-05-16 02:58:06'),
(1048, 'contra_voucher', 'update', 2, 1, '2026-05-16 03:18:12'),
(1049, 'payment_voucher', 'update', 3, 1, '2026-05-16 04:08:41'),
(1050, 'payment_voucher', 'update', 1, 1, '2026-05-16 09:39:23'),
(1051, 'payment_voucher', 'update', 4, 1, '2026-05-16 04:10:04'),
(1052, 'receipt_voucher', 'update', 5, 1, '2026-05-16 04:19:07'),
(1053, 'payment_voucher', 'update', 6, 1, '2026-05-16 04:24:17'),
(1054, 'payment_voucher', 'update', 6, 1, '2026-05-16 09:54:24'),
(1055, 'receipt_voucher', 'update', 7, 1, '2026-05-16 04:25:13'),
(1056, 'receipt_voucher', 'update', 8, 1, '2026-05-16 04:25:41'),
(1057, 'receipt_voucher', 'update', 8, 1, '2026-05-16 09:55:48'),
(1058, 'contra_voucher', 'update', 9, 1, '2026-05-16 04:27:32'),
(1059, 'contra_voucher', 'update', 9, 1, '2026-05-16 09:57:49'),
(1060, 'contra_voucher', 'update', 10, 1, '2026-05-16 04:28:01'),
(1061, 'payment_voucher', 'update', 11, 1, '2026-05-16 06:30:39'),
(1062, 'payment_voucher', 'update', 12, 1, '2026-05-16 06:31:07'),
(1063, 'receipt_voucher', 'update', 13, 1, '2026-05-16 06:31:28'),
(1064, 'receipt_voucher', 'update', 14, 1, '2026-05-16 06:31:45'),
(1065, 'payment_voucher', 'update', 15, 240776, '2026-05-16 06:39:13'),
(1066, 'payment_voucher', 'update', 16, 240776, '2026-05-16 06:39:30'),
(1067, 'receipt_voucher', 'update', 17, 240776, '2026-05-16 06:43:44'),
(1068, 'receipt_voucher', 'update', 18, 240776, '2026-05-16 06:43:59'),
(1069, 'contra_voucher', 'update', 19, 240776, '2026-05-16 06:49:11'),
(1070, 'contra_voucher', 'update', 20, 240776, '2026-05-16 06:50:23'),
(1071, 'purchase_order', 'insert', 2, 1, '2026-05-16 14:46:32'),
(1072, 'purchase', 'insert', 12, 1, '2026-05-16 14:47:04'),
(1073, 'GRN', 'insert', 3, 1, '2026-05-16 14:48:17'),
(1074, 'GRN', 'insert', 4, 1, '2026-05-16 14:49:33'),
(1075, 'GRN', 'insert', 5, 1, '2026-05-16 14:52:38'),
(1076, 'grn', 'update', 3, 1, '2026-05-16 14:59:01'),
(1077, 'GRN', 'insert', 6, 1, '2026-05-16 15:00:07'),
(1078, 'grn', 'delete', 6, 1, '2026-05-16 09:31:03'),
(1079, 'grn', 'update', 3, 1, '2026-05-16 15:01:16'),
(1080, 'sales_order', 'insert', 2, 1, '2026-05-16 15:04:23'),
(1081, 'sale', 'insert', 23, 1, '2026-05-16 15:05:11'),
(1082, 'GDN', 'insert', 3, 1, '2026-05-16 15:07:20'),
(1083, 'GDN', 'insert', 4, 1, '2026-05-16 15:08:28'),
(1084, 'gdn', 'update', 4, 1, '2026-05-16 15:08:43'),
(1085, 'GDN', 'insert', 5, 1, '2026-05-16 15:09:54'),
(1086, 'gdn', 'delete', 5, 1, '2026-05-16 09:46:36'),
(1087, 'gdn', 'update', 4, 1, '2026-05-16 15:16:54'),
(1088, 'gdn', 'delete', 4, 1, '2026-05-16 09:47:53'),
(1089, 'GDN', 'insert', 6, 1, '2026-05-16 15:18:14'),
(1090, 'gdn', 'update', 6, 1, '2026-05-16 15:19:12'),
(1091, 'GDN', 'insert', 7, 1, '2026-05-16 15:20:24'),
(1092, 'gdn', 'update', 7, 1, '2026-05-16 15:21:18'),
(1093, 'grn', 'update', 5, 1, '2026-05-16 15:21:56'),
(1094, 'grn', 'delete', 5, 1, '2026-05-16 09:53:47'),
(1095, 'grn', 'delete', 4, 1, '2026-05-16 09:53:52'),
(1096, 'GRN', 'insert', 7, 1, '2026-05-16 15:24:44'),
(1097, 'GRN', 'insert', 8, 1, '2026-05-16 15:25:15'),
(1098, 'grn', 'update', 8, 1, '2026-05-16 15:27:08'),
(1099, 'purchase_return', 'insert', 4, 1, '2026-05-16 10:03:01'),
(1100, 'GDN', 'insert', 8, 1, '2026-05-16 15:33:54'),
(1101, 'GDN', 'insert', 9, 1, '2026-05-16 15:34:38'),
(1102, 'sales_return', 'insert', 5, 1, '2026-05-16 15:36:57'),
(1103, 'sales_return', 'update', 5, 1, '2026-05-16 15:37:20'),
(1104, 'sales_return', 'update', 5, 1, '2026-05-16 15:37:36'),
(1105, 'GRN', 'insert', 9, 1, '2026-05-16 15:38:15'),
(1106, 'GRN', 'insert', 10, 1, '2026-05-16 15:38:37'),
(1107, 'grn', 'update', 7, 1, '2026-05-16 15:43:26'),
(1108, 'grn', 'update', 8, 1, '2026-05-16 15:43:49'),
(1109, 'service_order', 'insert', 7, 1, '2026-05-16 19:14:40'),
(1110, 'service_order', 'update', 7, 1, '2026-05-16 19:15:02'),
(1111, 'service', 'insert', 20, 1, '2026-05-16 19:16:29'),
(1112, 'service', 'update', 20, 1, '2026-05-16 19:18:05'),
(1113, 'service', 'insert', 21, 1, '2026-05-16 19:18:35'),
(1114, 'service', 'update', 21, 1, '2026-05-16 19:18:59'),
(1115, 'service_order', 'update', 7, 1, '2026-05-16 19:19:06'),
(1116, 'service_order', 'insert', 8, 1, '2026-05-16 19:21:31'),
(1117, 'service', 'insert', 22, 1, '2026-05-16 19:24:50'),
(1118, 'purchase_order', 'insert', 3, 1, '2026-05-16 19:26:34'),
(1119, 'purchase', 'insert', 13, 1, '2026-05-16 19:27:55'),
(1120, 'service', 'insert', 23, 1, '2026-05-16 19:30:09'),
(1121, 'service', 'insert', 24, 1, '2026-05-16 19:31:35'),
(1122, 'service', 'insert', 25, 1, '2026-05-16 19:33:04'),
(1123, 'service', 'insert', 26, 1, '2026-05-16 19:34:05'),
(1124, 'service', 'update', 26, 1, '2026-05-16 19:37:45'),
(1125, 'service', 'update', 26, 1, '2026-05-16 19:38:25'),
(1126, 'service_order', 'update', 7, 1, '2026-05-16 19:38:57'),
(1127, 'service', 'update', 25, 1, '2026-05-16 19:41:30'),
(1128, 'service', 'update', 23, 1, '2026-05-17 08:50:21'),
(1129, 'service_order', 'update', 2, 1, '2026-05-17 08:51:22'),
(1130, 'service_order', 'update', 2, 1, '2026-05-17 08:52:11'),
(1131, 'service', 'update', 15, 1, '2026-05-17 08:53:34'),
(1132, 'service_order', 'update', 4, 1, '2026-05-17 08:54:47'),
(1133, 'service_order', 'update', 2, 1, '2026-05-17 08:55:59'),
(1134, 'service_order', 'insert', 9, 1, '2026-05-17 08:56:30'),
(1135, 'service', 'insert', 27, 240776, '2026-05-17 08:57:29'),
(1136, 'service', 'insert', 28, 240776, '2026-05-17 08:57:44'),
(1137, 'sales_order', 'insert', 3, 240776, '2026-05-17 08:59:22'),
(1138, 'purchase_order', 'insert', 4, 240776, '2026-05-17 10:01:13'),
(1139, 'purchase_order', 'update', 4, 240776, '2026-05-17 10:03:31'),
(1140, 'purchase_order', 'insert', 5, 2680, '2026-05-17 10:03:45'),
(1141, 'purchase_order', 'update', 5, 2680, '2026-05-17 10:06:41'),
(1142, 'purchase', 'insert', 14, 240776, '2026-05-17 10:06:46'),
(1143, 'purchase', 'insert', 15, 2680, '2026-05-17 10:09:27'),
(1144, 'purchase_order', 'insert', 6, 240776, '2026-05-17 10:09:32'),
(1145, 'purchase', 'delete', 14, 240776, '2026-05-17 04:42:24'),
(1146, 'purchase', 'insert', 16, 240776, '2026-05-17 10:13:18'),
(1147, 'purchase_order', 'insert', 7, 2680, '2026-05-17 10:14:34'),
(1148, 'purchase_order', 'update', 7, 2680, '2026-05-17 10:14:46'),
(1149, 'purchase_order', 'delete', 7, 2680, '2026-05-17 04:45:05'),
(1150, 'purchase', 'insert', 17, 2680, '2026-05-17 10:18:14'),
(1151, 'purchase', 'update', 17, 2680, '2026-05-17 10:19:28'),
(1152, 'purchase', 'insert', 18, 240776, '2026-05-17 10:19:30'),
(1153, 'purchase', 'insert', 19, 2680, '2026-05-17 10:20:27'),
(1154, 'purchase', 'insert', 20, 2680, '2026-05-17 10:21:01'),
(1155, 'purchase', 'insert', 21, 2680, '2026-05-17 10:21:35'),
(1156, 'purchase', 'insert', 22, 240776, '2026-05-17 10:22:02'),
(1157, 'purchase', 'insert', 23, 240776, '2026-05-17 10:22:36'),
(1158, 'purchase', 'insert', 24, 240776, '2026-05-17 10:22:54'),
(1159, 'purchase', 'delete', 21, 2680, '2026-05-17 04:54:06'),
(1160, 'purchase', 'update', 24, 240776, '2026-05-17 10:24:19'),
(1161, 'purchase', 'update', 24, 240776, '2026-05-17 10:24:33'),
(1162, 'purchase', 'insert', 25, 2680, '2026-05-17 10:25:44'),
(1163, 'purchase', 'update', 25, 2680, '2026-05-17 10:26:53'),
(1164, 'purchase', 'update', 23, 240776, '2026-05-17 10:26:56'),
(1165, 'purchase', 'delete', 23, 240776, '2026-05-17 04:57:09'),
(1166, 'purchase', 'delete', 25, 2680, '2026-05-17 04:57:17'),
(1167, 'purchase', 'insert', 26, 2680, '2026-05-17 10:28:32'),
(1168, 'purchase', 'insert', 27, 240776, '2026-05-17 10:33:01'),
(1169, 'purchase', 'insert', 28, 2680, '2026-05-17 10:35:40'),
(1170, 'purchase', 'delete', 28, 2680, '2026-05-17 05:06:08'),
(1171, 'purchase', 'delete', 26, 2680, '2026-05-17 05:06:14'),
(1172, 'purchase', 'delete', 20, 2680, '2026-05-17 05:06:20'),
(1173, 'purchase', 'delete', 19, 2680, '2026-05-17 05:06:25'),
(1174, 'purchase', 'update', 17, 2680, '2026-05-17 10:37:27'),
(1175, 'purchase', 'update', 24, 240776, '2026-05-17 10:37:29'),
(1176, 'purchase', 'update', 17, 2680, '2026-05-17 10:38:46'),
(1177, 'purchase', 'update', 24, 240776, '2026-05-17 10:39:07'),
(1178, 'sale', 'insert', 24, 240776, '2026-05-17 10:40:23'),
(1179, 'purchase', 'update', 1, 2680, '2026-05-17 10:41:01'),
(1180, 'GRN', 'insert', 11, 240776, '2026-05-17 10:45:14'),
(1181, 'purchase', 'update', 17, 2680, '2026-05-17 10:45:53'),
(1182, 'sale', 'update', 18, 240776, '2026-05-17 10:46:06'),
(1183, 'sale', 'update', 18, 240776, '2026-05-17 10:46:29'),
(1184, 'purchase', 'update', 17, 2680, '2026-05-17 10:47:55'),
(1185, 'GRN', 'insert', 12, 2680, '2026-05-17 10:48:16'),
(1186, 'grn', 'delete', 12, 2680, '2026-05-17 05:18:55'),
(1187, 'purchase', 'delete', 17, 2680, '2026-05-17 05:19:01'),
(1188, 'purchase', 'insert', 29, 240776, '2026-05-17 10:52:43'),
(1189, 'purchase_return', 'insert', 5, 240776, '2026-05-17 05:23:47'),
(1190, 'purchase', 'insert', 30, 2680, '2026-05-17 10:54:03'),
(1191, 'purchase_return', 'insert', 6, 2680, '2026-05-17 05:24:50'),
(1192, 'purchase_return', 'update', 6, 2680, '2026-05-17 05:25:54'),
(1193, 'purchase_return', 'update', 5, 240776, '2026-05-17 10:57:34'),
(1194, 'GDN', 'insert', 10, 240776, '2026-05-17 10:59:40'),
(1195, 'gdn', 'delete', 10, 240776, '2026-05-17 05:30:32'),
(1196, 'purchase_return', 'update', 5, 240776, '2026-05-17 05:30:37'),
(1197, 'sales_order', 'insert', 4, 2680, '2026-05-17 11:02:21'),
(1198, 'sales_order', 'insert', 5, 240776, '2026-05-17 11:03:00'),
(1199, 'sales_order', 'update', 4, 2680, '2026-05-17 11:04:42'),
(1200, 'sales_order', 'insert', 6, 240776, '2026-05-17 11:05:02'),
(1201, 'sales_order', 'update', 4, 2680, '2026-05-17 11:05:10'),
(1202, 'sales_order', 'insert', 7, 240776, '2026-05-17 11:05:53'),
(1203, 'sales_order', 'update', 7, 240776, '2026-05-17 11:06:07'),
(1204, 'sales_order', 'update', 7, 240776, '2026-05-17 11:06:23'),
(1205, 'sales_order', 'insert', 8, 2680, '2026-05-17 11:06:36'),
(1206, 'sales_order', 'update', 8, 2680, '2026-05-17 11:06:49'),
(1207, 'sales_order', 'update', 8, 2680, '2026-05-17 11:06:57'),
(1208, 'sales_order', 'update', 7, 240776, '2026-05-17 11:07:33'),
(1209, 'sales_order', 'update', 4, 2680, '2026-05-17 11:07:47'),
(1210, 'sale', 'insert', 25, 240776, '2026-05-17 11:07:52'),
(1211, 'sale', 'insert', 26, 2680, '2026-05-17 11:09:06'),
(1212, 'sale', 'insert', 27, 2680, '2026-05-17 11:20:11'),
(1213, 'sale', 'update', 27, 2680, '2026-05-17 11:21:00'),
(1214, 'sale', 'update', 27, 2680, '2026-05-17 11:30:18'),
(1215, 'sale', 'insert', 28, 2680, '2026-05-17 11:30:58'),
(1216, 'sale', 'insert', 29, 240776, '2026-05-17 11:31:48'),
(1217, 'GDN', 'insert', 11, 2680, '2026-05-17 11:34:39'),
(1218, 'gdn', 'delete', 11, 2680, '2026-05-17 06:07:28'),
(1219, 'sale', 'update', 28, 2680, '2026-05-17 11:37:38'),
(1220, 'sale', 'insert', 30, 2680, '2026-05-17 11:38:26'),
(1221, 'GRN', 'insert', 13, 240776, '2026-05-17 11:38:48'),
(1222, 'GDN', 'insert', 12, 240776, '2026-05-17 11:39:02'),
(1223, 'sale', 'update', 29, 240776, '2026-05-17 11:39:13'),
(1224, 'sale', 'update', 29, 240776, '2026-05-17 11:39:44'),
(1225, 'sale', 'insert', 31, 2680, '2026-05-17 11:41:46'),
(1226, 'sale', 'insert', 32, 240776, '2026-05-17 11:42:38'),
(1227, 'sales_return', 'insert', 6, 2680, '2026-05-17 11:43:13'),
(1228, 'sales_return', 'insert', 7, 240776, '2026-05-17 11:44:05'),
(1229, 'sales_return', 'update', 6, 2680, '2026-05-17 11:44:34'),
(1230, 'purchase_return', 'insert', 7, 240776, '2026-05-17 06:15:54'),
(1231, 'sales_return', 'update', 6, 2680, '2026-05-17 11:47:07'),
(1232, 'sales_return', 'insert', 8, 2680, '2026-05-17 11:47:53'),
(1233, 'GRN', 'insert', 14, 2680, '2026-05-17 11:48:56'),
(1234, 'GRN', 'insert', 15, 240776, '2026-05-17 11:49:12'),
(1235, 'grn', 'delete', 14, 2680, '2026-05-17 06:21:02'),
(1236, 'sales_return', 'update', 8, 2680, '2026-05-17 11:51:31'),
(1237, 'contra_voucher', 'update', 21, 1, '2026-05-17 22:26:40'),
(1238, 'service_order', 'insert', 10, 2680, '2026-05-18 19:14:14'),
(1239, 'service', 'insert', 29, 977501, '2026-05-18 19:14:42'),
(1240, 'service', 'update', 29, 977501, '2026-05-18 19:15:28'),
(1241, 'service', 'insert', 30, 977501, '2026-05-18 19:16:03'),
(1242, 'service', 'update', 30, 977501, '2026-05-18 19:16:13'),
(1243, 'service_order', 'insert', 11, 2680, '2026-05-18 19:16:29'),
(1244, 'service', 'insert', 31, 2680, '2026-05-18 19:16:52'),
(1245, 'service', 'update', 31, 2680, '2026-05-18 19:17:03'),
(1246, 'service_order', 'update', 11, 2680, '2026-05-18 19:17:41'),
(1247, 'service_order', 'update', 11, 2680, '2026-05-18 19:17:47'),
(1248, 'service_order', 'update', 11, 2680, '2026-05-18 19:18:07'),
(1249, 'service', 'insert', 32, 149551, '2026-05-18 19:26:36'),
(1250, 'service', 'update', 32, 149551, '2026-05-18 19:26:50'),
(1251, 'service', 'update', 32, 149551, '2026-05-18 19:27:21'),
(1252, 'service', 'insert', 33, 240776, '2026-05-18 19:28:08'),
(1253, 'service_order', 'insert', 12, 240776, '2026-05-18 19:28:37'),
(1254, 'service_order', 'update', 12, 240776, '2026-05-18 19:28:51'),
(1255, 'service_order', 'update', 12, 240776, '2026-05-18 19:28:57'),
(1256, 'service_order', 'update', 12, 240776, '2026-05-18 19:29:11'),
(1257, 'service', 'insert', 34, 240776, '2026-05-18 19:29:37'),
(1258, 'service', 'insert', 35, 409360, '2026-05-18 19:40:48'),
(1259, 'service', 'update', 35, 409360, '2026-05-18 19:41:07'),
(1260, 'sale', 'insert', 33, 1, '2026-05-20 13:10:09'),
(1261, 'sale', 'insert', 34, 1, '2026-05-20 13:14:35'),
(1262, 'sale', 'update', 33, 1, '2026-05-23 05:47:59'),
(1263, 'sale', 'update', 34, 1, '2026-05-23 05:48:29'),
(1264, 'sale', 'update', 34, 1, '2026-05-23 05:49:35'),
(1265, 'purchase', 'update', 30, 1, '2026-05-23 05:59:28'),
(1266, 'purchase', 'update', 30, 1, '2026-05-23 06:13:52'),
(1267, 'purchase', 'update', 30, 1, '2026-05-23 06:15:00'),
(1268, 'purchase', 'update', 30, 1, '2026-05-23 06:28:18'),
(1269, 'purchase', 'update', 30, 1, '2026-05-23 06:29:02'),
(1270, 'purchase', 'update', 30, 1, '2026-05-23 06:31:48'),
(1271, 'purchase', 'update', 30, 1, '2026-05-23 06:33:22'),
(1272, 'purchase', 'update', 30, 1, '2026-05-23 06:36:51'),
(1273, 'purchase', 'update', 30, 1, '2026-05-23 06:37:16'),
(1274, 'sale', 'update', 34, 1, '2026-05-23 06:41:13'),
(1275, 'sale', 'update', 34, 1, '2026-05-23 07:22:19'),
(1276, 'purchase_return', 'update', 4, 1, '2026-05-23 07:35:22'),
(1277, 'sale', 'update', 34, 1, '2026-05-23 09:14:20'),
(1278, 'sale', 'update', 34, 1, '2026-05-23 09:15:37'),
(1279, 'sale', 'update', 34, 1, '2026-05-23 09:16:13'),
(1280, 'purchase', 'update', 30, 1, '2026-05-23 09:22:10'),
(1281, 'sale', 'insert', 35, 1, '2026-05-24 06:55:51'),
(1282, 'stock', 'insert', 14, 1, '2026-05-27 04:36:52'),
(1283, 'stock', 'insert', 15, 1, '2026-05-27 04:38:38'),
(1284, 'stock', 'insert', 16, 1, '2026-05-27 04:39:55'),
(1285, 'sale', 'insert', 36, 1, '2026-05-27 06:21:02'),
(1286, 'sale', 'insert', 37, 1, '2026-05-27 06:21:05'),
(1287, 'sale', 'insert', 38, 1, '2026-05-27 06:22:36'),
(1288, 'sale', 'insert', 39, 1, '2026-05-27 06:22:36'),
(1289, 'sale', 'insert', 40, 1, '2026-05-27 06:22:37'),
(1290, 'sale', 'insert', 41, 1, '2026-05-27 06:22:37'),
(1291, 'purchase', 'insert', 31, 1, '2026-05-27 15:38:18'),
(1292, 'purchase', 'insert', 32, 1, '2026-05-27 15:40:48'),
(1293, 'purchase', 'insert', 33, 1, '2026-05-28 05:22:33'),
(1294, 'purchase_order', 'update', 4, 1, '2026-05-28 09:25:17'),
(1295, 'GDN', 'insert', 13, 1, '2026-05-28 14:54:50'),
(1296, 'service_order', 'insert', 1, 1, '2026-05-30 12:51:44'),
(1297, 'service_order', 'insert', 2, 1, '2026-05-30 12:56:59'),
(1298, 'service_order', 'insert', 3, 1, '2026-05-30 12:57:42'),
(1299, 'service_order', 'update', 3, 1, '2026-05-30 12:58:02'),
(1300, 'service_order', 'update', 3, 1, '2026-05-30 12:58:08'),
(1301, 'service_order', 'update', 3, 1, '2026-05-30 12:58:14'),
(1302, 'service', 'insert', 1, 1, '2026-05-30 13:00:02'),
(1303, 'service_order', 'update', 3, 1, '2026-05-30 13:01:02'),
(1304, 'service', 'insert', 2, 1, '2026-05-30 13:04:12'),
(1305, 'service', 'insert', 3, 240776, '2026-05-30 13:08:30'),
(1306, 'service', 'update', 3, 240776, '2026-05-30 13:12:06'),
(1307, 'service', 'insert', 4, 240776, '2026-05-30 13:12:30'),
(1308, 'service_order', 'insert', 4, 240776, '2026-05-30 13:30:36'),
(1309, 'service', 'insert', 5, 240776, '2026-05-31 12:36:13'),
(1310, 'purchase', 'insert', 34, 1, '2026-06-02 20:32:46'),
(1311, 'sale', 'insert', 42, 1, '2026-06-02 20:33:22'),
(1312, 'sale', 'update', 42, 1, '2026-06-02 20:35:00'),
(1313, 'purchase_return', 'insert', 8, 1, '2026-06-02 21:11:40'),
(1314, 'sales_return', 'insert', 9, 1, '2026-06-03 02:42:17'),
(1315, 'GRN', 'insert', 16, 1, '2026-06-03 02:43:29'),
(1316, 'GDN', 'insert', 14, 1, '2026-06-03 02:44:01'),
(1317, 'sale', 'insert', 43, 1, '2026-06-04 05:18:45'),
(1318, 'service', 'insert', 6, 1, '2026-06-07 12:13:23'),
(1319, 'stock', 'insert', 25, 1, '2026-06-08 06:39:45'),
(1320, 'stock', 'update', 25, 1, '2026-06-08 06:41:14'),
(1321, 'stock', 'update', 25, 1, '2026-06-08 06:45:13'),
(1322, 'stock', 'insert', 26, 1, '2026-06-08 06:53:07'),
(1323, 'sales_order', 'update', 1, 1, '2026-06-09 04:25:54'),
(1324, 'sale', 'update', 43, 1, '2026-06-09 04:45:02'),
(1325, 'sales_return', 'insert', 10, 1, '2026-06-09 04:45:57'),
(1326, 'purchase', 'update', 34, 1, '2026-06-09 04:48:03'),
(1327, 'purchase_return', 'update', 8, 1, '2026-06-08 23:19:30'),
(1328, 'purchase_return', 'insert', 9, 1, '2026-06-08 23:19:50'),
(1329, 'sale', 'insert', 44, 1, '2026-06-09 05:00:19'),
(1330, 'sale', 'insert', 45, 1, '2026-06-10 05:23:58'),
(1331, 'sale', 'insert', 46, 1, '2026-06-10 05:25:04'),
(1332, 'sale', 'insert', 47, 1, '2026-06-10 05:26:46'),
(1333, 'sale', 'insert', 48, 1, '2026-06-10 05:27:38'),
(1334, 'sale', 'update', 48, 1, '2026-06-10 05:28:55'),
(1335, 'sale', 'update', 47, 1, '2026-06-10 05:29:01'),
(1336, 'sale', 'update', 46, 1, '2026-06-10 05:29:08'),
(1337, 'sale', 'update', 45, 1, '2026-06-10 05:29:13'),
(1338, 'sale', 'insert', 49, 1, '2026-06-11 04:22:49'),
(1339, 'sale', 'insert', 50, 149551, '2026-06-11 04:28:25'),
(1340, 'sale', 'insert', 51, 1, '2026-06-11 15:40:38'),
(1341, 'sale', 'insert', 52, 1, '2026-06-11 15:41:24'),
(1342, 'sale', 'insert', 53, 1, '2026-06-11 15:47:11'),
(1343, 'sale', 'insert', 54, 1, '2026-06-14 10:08:14'),
(1344, 'sale', 'insert', 55, 1, '2026-06-14 13:19:35'),
(1345, 'stock', 'insert', 29, 1, '2026-06-14 13:56:42'),
(1346, 'sale', 'insert', 56, 1, '2026-06-14 13:57:58'),
(1347, 'stock', 'insert', 30, 1, '2026-06-15 11:03:09'),
(1348, 'stock', 'insert', 31, 1, '2026-06-15 11:13:50'),
(1349, 'GDN', 'insert', 15, 1, '2026-06-15 11:37:46'),
(1350, 'GRN', 'insert', 17, 1, '2026-06-15 11:43:27'),
(1351, 'stock', 'insert', 32, 1, '2026-06-15 11:48:20'),
(1352, 'GDN', 'insert', 16, 1, '2026-06-15 11:49:07'),
(1353, 'sale', 'insert', 57, 1, '2026-06-15 11:51:35'),
(1354, 'sale', 'insert', 58, 1, '2026-06-15 12:04:17'),
(1355, 'sale', 'insert', 59, 1, '2026-06-15 12:06:00'),
(1356, 'stock', 'insert', 33, 1, '2026-06-16 11:04:16'),
(1357, 'stock', 'update', 33, 1, '2026-06-16 11:05:09'),
(1358, 'stock', 'delete', 33, 1, '2026-06-16 05:37:14'),
(1359, 'stock', 'insert', 34, 1, '2026-06-16 11:09:00'),
(1360, 'stock', 'insert', 35, 1, '2026-06-16 11:12:11'),
(1361, 'stock', 'insert', 36, 1, '2026-06-16 11:20:24'),
(1362, 'stock', 'update', 36, 1, '2026-06-16 11:20:54'),
(1363, 'stock', 'delete', 34, 1, '2026-06-16 05:54:52'),
(1364, 'stock', 'delete', 35, 1, '2026-06-16 05:55:09'),
(1365, 'stock', 'insert', 37, 1, '2026-06-16 11:26:40'),
(1366, 'stock', 'update', 37, 1, '2026-06-16 11:30:38'),
(1367, 'GDN', 'insert', 17, 1, '2026-06-16 11:32:06'),
(1368, 'stock', 'insert', 38, 1, '2026-06-16 11:33:32'),
(1369, 'stock', 'insert', 39, 1, '2026-06-16 11:33:55'),
(1370, 'stock', 'update', 37, 1, '2026-06-16 11:34:28'),
(1371, 'stock', 'update', 37, 1, '2026-06-16 11:35:37'),
(1372, 'stock', 'update', 37, 1, '2026-06-16 11:36:37'),
(1373, 'stock', 'insert', 40, 1, '2026-06-16 11:39:00'),
(1374, 'gdn', 'update', 17, 1, '2026-06-16 11:40:37'),
(1375, 'stock', 'insert', 41, 1, '2026-06-16 11:43:47'),
(1376, 'GDN', 'insert', 18, 1, '2026-06-16 11:44:22'),
(1377, 'GRN', 'insert', 18, 1, '2026-06-16 11:44:58'),
(1378, 'grn', 'update', 18, 1, '2026-06-16 11:45:38'),
(1379, 'grn', 'update', 18, 1, '2026-06-16 11:45:55'),
(1380, 'sale', 'insert', 60, 1, '2026-06-16 12:26:43'),
(1381, 'purchase', 'insert', 35, 1, '2026-06-16 12:56:54'),
(1382, 'sale', 'insert', 61, 1, '2026-06-16 12:57:27'),
(1383, 'sale', 'update', 61, 1, '2026-06-16 12:57:57'),
(1384, 'purchase_return', 'insert', 10, 1, '2026-06-16 07:31:56'),
(1385, 'sales_return', 'insert', 11, 1, '2026-06-16 13:02:53'),
(1386, 'stock', 'insert', 42, 1, '2026-06-16 13:04:23'),
(1387, 'GRN', 'insert', 19, 1, '2026-06-16 13:05:41'),
(1388, 'GDN', 'insert', 19, 1, '2026-06-16 13:06:19'),
(1389, 'GRN', 'insert', 20, 1, '2026-06-16 13:07:02'),
(1390, 'GDN', 'insert', 20, 1, '2026-06-16 13:07:36'),
(1391, 'stock', 'insert', 43, 1, '2026-06-16 13:17:32'),
(1392, 'stock', 'update', 43, 1, '2026-06-16 13:18:26'),
(1393, 'sale', 'insert', 62, 1, '2026-06-20 06:21:02'),
(1394, 'sale', 'update', 62, 1, '2026-06-20 06:35:48'),
(1395, 'sale', 'insert', 63, 1, '2026-06-20 07:42:37'),
(1396, 'sale', 'update', 63, 1, '2026-06-20 07:44:28'),
(1397, 'sale', 'update', 63, 1, '2026-06-20 07:45:38'),
(1398, 'sale', 'insert', 64, 1, '2026-06-20 08:27:10'),
(1399, 'sale', 'insert', 65, 1, '2026-06-20 09:45:52'),
(1400, 'GRN', 'insert', 21, 1, '2026-06-20 09:57:18'),
(1401, 'payment_voucher', 'update', 22, 1, '2026-06-20 04:29:16'),
(1402, 'receipt_voucher', 'update', 23, 1, '2026-06-20 04:29:30'),
(1403, 'contra_voucher', 'update', 24, 1, '2026-06-20 04:29:53'),
(1404, 'stock', 'insert', 44, 1, '2026-06-20 10:05:10'),
(1405, 'stock', 'insert', 45, 1, '2026-06-20 10:08:33'),
(1406, 'stock', 'insert', 46, 1, '2026-06-20 10:10:13'),
(1407, 'stock', 'delete', 46, 1, '2026-06-20 04:41:05'),
(1408, 'stock', 'insert', 47, 1, '2026-06-20 10:15:52'),
(1409, 'stock', 'update', 47, 1, '2026-06-20 10:16:04'),
(1410, 'sale', 'insert', 66, 1, '2026-06-20 13:43:47'),
(1411, 'sale', 'insert', 67, 1, '2026-06-20 16:20:20'),
(1412, 'sale', 'insert', 68, 1, '2026-06-20 16:20:20'),
(1413, 'sale', 'insert', 69, 1, '2026-06-20 16:22:39'),
(1414, 'sale', 'insert', 70, 1, '2026-06-21 17:04:20'),
(1415, 'sale', 'insert', 71, 1, '2026-06-21 17:07:50'),
(1416, 'sale', 'insert', 72, 409360, '2026-06-21 17:44:20'),
(1417, 'stock', 'insert', 48, 1, '2026-06-22 05:23:46'),
(1418, 'sale', 'insert', 73, 1, '2026-06-22 05:27:31'),
(1419, 'sale', 'update', 73, 1, '2026-06-22 06:17:42'),
(1420, 'sale', 'update', 71, 1, '2026-06-22 06:17:57'),
(1421, 'sale', 'update', 71, 1, '2026-06-22 06:18:03'),
(1422, 'sale', 'insert', 74, 409360, '2026-06-22 06:36:15'),
(1423, 'sale', 'insert', 75, 1, '2026-06-22 07:40:33'),
(1424, 'sale', 'insert', 76, 409360, '2026-06-22 07:48:34'),
(1425, 'sale', 'insert', 77, 409360, '2026-06-22 08:01:27'),
(1426, 'sale', 'insert', 78, 1, '2026-06-22 09:04:49'),
(1427, 'sale', 'insert', 79, 409360, '2026-06-22 09:13:01'),
(1428, 'sale', 'insert', 80, 409360, '2026-06-22 09:13:01'),
(1429, 'stock', 'insert', 49, 1, '2026-06-29 07:02:27'),
(1430, 'stock', 'insert', 50, 1, '2026-06-29 07:02:28'),
(1431, 'stock', 'insert', 51, 1, '2026-07-01 03:38:48'),
(1432, 'stock', 'insert', 52, 1, '2026-07-04 18:15:54'),
(1433, 'stock', 'insert', 53, 1, '2026-07-05 02:53:42'),
(1434, 'stock', 'insert', 54, 1, '2026-07-05 04:01:28'),
(1435, 'stock', 'insert', 55, 1, '2026-07-05 04:01:28'),
(1436, 'stock', 'insert', 56, 1, '2026-07-05 04:01:29'),
(1437, 'sale', 'insert', 81, 1, '2026-07-05 05:09:16'),
(1438, 'sale', 'update', 81, 1, '2026-07-05 05:27:08'),
(1439, 'sale', 'update', 81, 1, '2026-07-05 05:27:53'),
(1440, 'sale', 'update', 81, 1, '2026-07-05 05:29:25'),
(1441, 'sale', 'update', 81, 1, '2026-07-05 05:33:30'),
(1442, 'sale', 'update', 81, 1, '2026-07-05 05:37:40'),
(1443, 'sale', 'update', 81, 1, '2026-07-05 05:45:12'),
(1444, 'sale', 'update', 81, 1, '2026-07-05 05:54:50'),
(1445, 'sale', 'update', 81, 1, '2026-07-05 06:02:21'),
(1446, 'sale', 'update', 81, 1, '2026-07-05 06:11:55'),
(1447, 'sale', 'update', 81, 1, '2026-07-05 06:13:21'),
(1448, 'sale', 'update', 81, 1, '2026-07-05 06:21:00'),
(1449, 'sale', 'update', 81, 1, '2026-07-05 06:30:22'),
(1450, 'sale', 'update', 81, 1, '2026-07-05 06:35:10'),
(1451, 'sale', 'update', 81, 1, '2026-07-05 06:40:11'),
(1452, 'sale', 'update', 81, 1, '2026-07-05 06:56:30'),
(1453, 'sale', 'update', 81, 1, '2026-07-05 06:57:40'),
(1454, 'sale', 'update', 81, 1, '2026-07-05 06:58:15'),
(1455, 'sale', 'update', 81, 1, '2026-07-05 07:13:24'),
(1456, 'sale', 'update', 81, 1, '2026-07-05 07:17:37'),
(1457, 'sale', 'update', 81, 1, '2026-07-05 07:52:23'),
(1458, 'sale', 'update', 81, 1, '2026-07-05 07:54:23'),
(1459, 'sale', 'update', 81, 1, '2026-07-05 08:49:05'),
(1460, 'sale', 'update', 81, 1, '2026-07-06 15:18:16'),
(1461, 'sale', 'update', 81, 1, '2026-07-06 15:18:22'),
(1462, 'sale', 'update', 81, 1, '2026-07-09 04:34:54'),
(1463, 'sale', 'update', 81, 1, '2026-07-11 07:08:24'),
(1464, 'stock', 'insert', 57, 1, '2026-07-11 09:46:12'),
(1465, 'stock', 'insert', 58, 1, '2026-07-11 09:46:40'),
(1466, 'stock', 'insert', 59, 1, '2026-07-11 09:46:56'),
(1467, 'stock', 'insert', 60, 1, '2026-07-11 09:49:26'),
(1468, 'stock', 'insert', 61, 1, '2026-07-11 09:50:04'),
(1469, 'stock', 'insert', 62, 1, '2026-07-11 09:50:28'),
(1470, 'stock', 'insert', 63, 1, '2026-07-11 09:52:27'),
(1471, 'GRN', 'insert', 22, 1, '2026-07-11 09:53:02'),
(1472, 'GDN', 'insert', 21, 1, '2026-07-11 09:53:40'),
(1473, 'sale', 'insert', 82, 1, '2026-07-11 09:53:48'),
(1474, 'purchase', 'insert', 36, 1, '2026-07-11 09:59:14'),
(1475, 'purchase', 'insert', 37, 1, '2026-07-11 10:00:07'),
(1476, 'sales_order', 'insert', 9, 1, '2026-07-11 10:00:49'),
(1477, 'purchase', 'update', 37, 1, '2026-07-11 10:00:50'),
(1478, 'purchase', 'insert', 38, 1, '2026-07-11 10:01:32'),
(1479, 'GRN', 'insert', 23, 1, '2026-07-11 10:02:00'),
(1480, 'purchase_return', 'insert', 11, 1, '2026-07-11 04:34:39'),
(1481, 'GDN', 'insert', 22, 1, '2026-07-11 10:05:52'),
(1482, 'purchase_return', 'insert', 12, 1, '2026-07-11 04:42:46'),
(1483, 'purchase_return', 'insert', 13, 1, '2026-07-11 04:43:59'),
(1484, 'purchase_return', 'update', 13, 1, '2026-07-11 10:14:42'),
(1485, 'GDN', 'insert', 23, 1, '2026-07-11 10:16:07'),
(1486, 'sale', 'insert', 83, 1, '2026-07-11 10:22:13'),
(1487, 'sale', 'insert', 84, 1, '2026-07-11 10:23:14'),
(1488, 'GDN', 'insert', 24, 1, '2026-07-11 10:24:04'),
(1489, 'GDN', 'insert', 25, 1, '2026-07-11 10:24:25'),
(1490, 'sale', 'insert', 85, 1, '2026-07-11 10:30:19'),
(1491, 'sales_return', 'insert', 12, 1, '2026-07-11 10:35:38'),
(1492, 'GRN', 'insert', 24, 1, '2026-07-11 10:36:13'),
(1493, 'sales_return', 'insert', 13, 1, '2026-07-11 10:37:32'),
(1494, 'stock', 'insert', 64, 1, '2026-07-11 11:25:45'),
(1495, 'sale', 'insert', 86, 1, '2026-07-11 11:29:03'),
(1496, 'sale', 'insert', 87, 1, '2026-07-11 15:24:40'),
(1497, 'sale', 'insert', 88, 1, '2026-07-11 15:28:59'),
(1498, 'sale', 'update', 88, 1, '2026-07-11 15:29:15'),
(1499, 'sale', 'update', 88, 1, '2026-07-11 15:29:30'),
(1500, 'sale', 'insert', 89, 1, '2026-07-11 15:30:14'),
(1501, 'sale', 'insert', 90, 1, '2026-07-11 15:40:56'),
(1502, 'sale', 'update', 90, 1, '2026-07-11 16:00:10'),
(1503, 'sale', 'update', 89, 1, '2026-07-11 16:16:05'),
(1504, 'sale', 'insert', 91, 1, '2026-07-11 16:17:11'),
(1505, 'sale', 'update', 91, 1, '2026-07-11 16:17:49'),
(1506, 'sale', 'insert', 92, 1, '2026-07-11 16:22:52'),
(1507, 'sale', 'insert', 93, 1, '2026-07-11 16:29:21'),
(1508, 'sale', 'insert', 94, 1, '2026-07-11 16:33:52'),
(1509, 'sale', 'update', 94, 1, '2026-07-11 16:34:21'),
(1510, 'sale', 'update', 93, 1, '2026-07-11 16:34:35'),
(1511, 'sale', 'insert', 95, 1, '2026-07-11 16:39:28'),
(1512, 'sale', 'update', 95, 1, '2026-07-11 16:39:45'),
(1513, 'sale', 'update', 92, 1, '2026-07-11 16:40:02'),
(1514, 'sale', 'insert', 96, 1, '2026-07-11 16:40:56'),
(1515, 'sale', 'insert', 97, 1, '2026-07-12 04:41:48'),
(1516, 'sale', 'insert', 98, 1, '2026-07-12 06:18:21'),
(1517, 'purchase', 'insert', 39, 1, '2026-07-12 08:23:25'),
(1518, 'sale', 'insert', 99, 1, '2026-07-12 08:24:06'),
(1519, 'sale', 'insert', 100, 1, '2026-07-12 13:05:42'),
(1520, 'sale', 'insert', 101, 1, '2026-07-12 14:20:42'),
(1521, 'sale', 'update', 100, 1, '2026-07-14 18:45:58'),
(1522, 'sale', 'insert', 102, 1, '2026-07-15 12:00:48'),
(1523, 'sale', 'insert', 103, 1, '2026-07-16 10:52:15'),
(1524, 'sale', 'insert', 104, 1, '2026-07-16 11:01:59'),
(1525, 'stock', 'insert', 65, 1, '2026-07-16 11:03:36'),
(1526, 'sale', 'insert', 105, 1, '2026-07-16 11:07:11'),
(1527, 'sale', 'update', 105, 1, '2026-07-17 04:17:40'),
(1528, 'sales_return', 'insert', 14, 1, '2026-07-17 05:40:30'),
(1529, 'stock', 'insert', 66, 190693, '2026-07-17 05:50:22'),
(1530, 'sale', 'insert', 106, 1, '2026-07-17 06:03:55'),
(1531, 'sale', 'insert', 107, 1, '2026-07-17 06:05:56'),
(1532, 'purchase', 'insert', 40, 1, '2026-07-17 06:08:19'),
(1533, 'purchase', 'insert', 41, 1, '2026-07-17 06:09:59'),
(1534, 'purchase', 'insert', 42, 1, '2026-07-17 06:10:58'),
(1535, 'purchase', 'update', 42, 1, '2026-07-17 06:12:13'),
(1536, 'GRN', 'insert', 25, 1, '2026-07-17 06:13:48'),
(1537, 'purchase', 'insert', 43, 1, '2026-07-17 06:14:50'),
(1538, 'GDN', 'insert', 26, 1, '2026-07-17 06:15:12'),
(1539, 'purchase', 'delete', 43, 1, '2026-07-17 00:45:24'),
(1540, 'purchase_return', 'insert', 14, 1, '2026-07-17 00:48:25'),
(1541, 'purchase_return', 'update', 14, 1, '2026-07-17 06:19:17'),
(1542, 'purchase_return', 'update', 14, 1, '2026-07-17 06:19:52'),
(1543, 'purchase_return', 'insert', 15, 1, '2026-07-17 00:51:57'),
(1544, 'purchase_return', 'update', 15, 1, '2026-07-17 00:52:27'),
(1545, 'GRN', 'insert', 26, 1, '2026-07-17 06:23:32'),
(1546, 'GRN', 'insert', 27, 1, '2026-07-17 06:23:56'),
(1547, 'GRN', 'insert', 28, 1, '2026-07-17 06:24:23'),
(1548, 'grn', 'update', 28, 1, '2026-07-17 06:24:44'),
(1549, 'GRN', 'insert', 29, 1, '2026-07-17 06:25:44'),
(1550, 'grn', 'delete', 29, 1, '2026-07-17 00:56:51'),
(1551, 'grn', 'update', 28, 1, '2026-07-17 06:27:19'),
(1552, 'GDN', 'insert', 27, 1, '2026-07-17 06:27:58'),
(1553, 'GDN', 'insert', 28, 1, '2026-07-17 06:28:34'),
(1554, 'GDN', 'insert', 29, 1, '2026-07-17 06:28:58'),
(1555, 'gdn', 'delete', 29, 1, '2026-07-17 00:59:28'),
(1556, 'gdn', 'update', 28, 1, '2026-07-17 06:29:42'),
(1557, 'stock', 'insert', 67, 1, '2026-07-17 06:33:17'),
(1558, 'stock', 'update', 67, 1, '2026-07-17 06:34:00'),
(1559, 'stock', 'delete', 67, 1, '2026-07-17 01:09:23'),
(1560, 'gdn', 'delete', 28, 1, '2026-07-17 02:35:30'),
(1561, 'gdn', 'delete', 27, 1, '2026-07-17 02:35:47'),
(1562, 'gdn', 'delete', 26, 1, '2026-07-17 02:36:06'),
(1563, 'sale', 'update', 107, 1, '2026-07-17 08:17:22'),
(1564, 'sale', 'update', 106, 1, '2026-07-17 08:17:51'),
(1565, 'sale', 'insert', 108, 1, '2026-07-17 13:57:47'),
(1566, 'sale', 'update', 108, 1, '2026-07-17 13:59:44'),
(1567, 'sale', 'insert', 109, 1, '2026-07-17 14:02:41'),
(1568, 'sale', 'update', 109, 1, '2026-07-17 14:03:35'),
(1569, 'sale', 'update', 109, 1, '2026-07-17 14:04:24'),
(1570, 'GDN', 'insert', 30, 1, '2026-07-17 14:06:30'),
(1571, 'sale', 'insert', 110, 1, '2026-07-17 14:07:30'),
(1572, 'sale', 'update', 110, 1, '2026-07-17 14:07:55'),
(1573, 'GDN', 'insert', 31, 1, '2026-07-17 14:08:40'),
(1574, 'GDN', 'insert', 32, 1, '2026-07-17 14:09:04'),
(1575, 'gdn', 'update', 32, 1, '2026-07-17 14:09:25'),
(1576, 'gdn', 'update', 31, 1, '2026-07-17 14:09:56'),
(1577, 'gdn', 'delete', 32, 1, '2026-07-17 08:40:06'),
(1578, 'sales_return', 'insert', 15, 1, '2026-07-17 14:12:32'),
(1579, 'sales_return', 'update', 15, 1, '2026-07-17 14:13:15'),
(1580, 'sales_return', 'update', 15, 1, '2026-07-17 14:13:54'),
(1581, 'sales_return', 'insert', 16, 1, '2026-07-17 14:15:31'),
(1582, 'sales_return', 'update', 16, 1, '2026-07-17 14:16:52'),
(1583, 'sale', 'insert', 111, 1, '2026-07-17 14:20:10'),
(1584, 'sales_return', 'insert', 17, 1, '2026-07-17 14:21:50'),
(1585, 'sales_return', 'update', 17, 1, '2026-07-17 14:23:42'),
(1586, 'sales_return', 'update', 17, 1, '2026-07-17 14:26:10'),
(1587, 'purchase', 'insert', 44, 1, '2026-07-17 14:29:05'),
(1588, 'purchase_return', 'insert', 16, 1, '2026-07-17 08:59:58'),
(1589, 'purchase_return', 'update', 16, 1, '2026-07-17 14:30:47'),
(1590, 'purchase_return', 'update', 16, 1, '2026-07-17 14:32:00'),
(1591, 'purchase_return', 'update', 16, 1, '2026-07-17 14:32:38'),
(1592, 'sale', 'insert', 112, 1, '2026-07-17 14:53:22'),
(1593, 'sales_return', 'insert', 18, 1, '2026-07-17 14:54:15'),
(1594, 'sales_return', 'update', 18, 1, '2026-07-17 14:55:18'),
(1595, 'sales_return', 'update', 18, 1, '2026-07-17 14:56:40'),
(1596, 'sales_return', 'update', 18, 1, '2026-07-17 14:59:07'),
(1597, 'sale', 'insert', 113, 1, '2026-07-17 15:01:02'),
(1598, 'sales_return', 'insert', 19, 1, '2026-07-17 15:02:17'),
(1599, 'sales_return', 'update', 19, 1, '2026-07-17 15:03:17'),
(1600, 'sales_return', 'update', 19, 1, '2026-07-17 15:03:53'),
(1601, 'sales_return', 'update', 19, 1, '2026-07-17 15:04:44'),
(1602, 'purchase', 'insert', 45, 1, '2026-07-17 15:20:41'),
(1603, 'purchase', 'update', 45, 1, '2026-07-17 15:21:31'),
(1604, 'purchase', 'delete', 45, 1, '2026-07-17 09:51:56'),
(1605, 'sale', 'insert', 114, 1, '2026-07-17 15:22:53'),
(1606, 'sale', 'update', 114, 1, '2026-07-17 15:23:26'),
(1608, 'stock', 'insert', 69, 1, '2026-07-18 03:39:51'),
(1609, 'stock', 'insert', 70, 1, '2026-07-18 03:39:51'),
(1610, 'purchase', 'insert', 46, 1, '2026-07-18 04:10:28'),
(1611, 'sales_return', 'insert', 20, 1, '2026-07-18 05:19:38'),
(1612, 'purchase_return', 'update', 12, 1, '2026-07-18 08:38:12'),
(1613, 'sale', 'update', 85, 1, '2026-07-18 09:36:41'),
(1614, 'sale', 'update', 84, 1, '2026-07-18 09:37:51'),
(1615, 'sale', 'update', 97, 1, '2026-07-18 09:38:43'),
(1616, 'sale', 'update', 97, 1, '2026-07-18 09:40:09'),
(1617, 'purchase', 'insert', 47, 1, '2026-07-18 13:12:20'),
(1618, 'sale', 'insert', 115, 1, '2026-07-18 13:15:23'),
(1619, 'purchase', 'update', 47, 1, '2026-07-18 13:23:00'),
(1620, 'purchase', 'insert', 48, 1, '2026-07-18 13:24:09'),
(1621, 'sale', 'insert', 116, 1, '2026-07-18 17:17:51'),
(1622, 'sale', 'insert', 117, 1, '2026-07-18 17:18:30'),
(1623, 'purchase', 'insert', 49, 1, '2026-07-18 17:19:12'),
(1624, 'sale', 'insert', 118, 1, '2026-07-21 02:52:52'),
(1625, 'sale', 'insert', 119, 1, '2026-07-21 02:53:36'),
(1626, 'sale', 'insert', 120, 1, '2026-07-21 03:51:16'),
(1627, 'sale', 'insert', 121, 1, '2026-07-21 03:51:46'),
(1628, 'purchase', 'insert', 50, 1, '2026-07-21 15:29:22'),
(1629, 'stock', 'insert', 71, 1, '2026-07-22 17:01:12'),
(1630, 'stock', 'insert', 72, 1, '2026-07-22 17:01:13'),
(1631, 'stock', 'insert', 73, 1, '2026-07-22 17:01:13'),
(1632, 'stock', 'insert', 74, 1, '2026-07-22 17:01:14'),
(1633, 'stock', 'insert', 75, 1, '2026-07-22 17:01:14'),
(1634, 'stock', 'insert', 76, 1, '2026-07-22 17:01:14'),
(1635, 'stock', 'insert', 77, 1, '2026-07-22 17:01:15'),
(1636, 'stock', 'insert', 78, 1, '2026-07-22 17:01:15'),
(1637, 'stock', 'insert', 79, 1, '2026-07-22 17:01:16'),
(1638, 'stock', 'insert', 80, 1, '2026-07-22 17:01:16'),
(1639, 'stock', 'insert', 81, 1, '2026-07-22 17:01:17'),
(1640, 'stock', 'insert', 82, 1, '2026-07-22 17:01:17'),
(1641, 'stock', 'insert', 83, 1, '2026-07-22 17:01:18'),
(1642, 'stock', 'insert', 84, 1, '2026-07-22 17:01:18'),
(1643, 'stock', 'insert', 85, 1, '2026-07-22 17:01:18'),
(1644, 'stock', 'insert', 86, 1, '2026-07-22 17:01:19'),
(1645, 'stock', 'insert', 87, 1, '2026-07-22 17:01:19'),
(1646, 'stock', 'insert', 88, 1, '2026-07-22 17:01:20'),
(1647, 'stock', 'insert', 89, 1, '2026-07-22 17:01:20'),
(1648, 'stock', 'insert', 90, 1, '2026-07-22 17:01:20'),
(1649, 'stock', 'insert', 91, 1, '2026-07-22 17:01:21'),
(1650, 'stock', 'insert', 92, 1, '2026-07-22 17:01:21'),
(1651, 'stock', 'insert', 93, 1, '2026-07-22 17:01:22'),
(1652, 'stock', 'insert', 94, 1, '2026-07-22 17:01:22'),
(1653, 'stock', 'insert', 95, 1, '2026-07-22 17:01:23'),
(1654, 'stock', 'insert', 96, 1, '2026-07-22 17:01:23'),
(1655, 'stock', 'insert', 97, 1, '2026-07-22 17:01:23'),
(1656, 'stock', 'insert', 98, 1, '2026-07-22 17:01:24'),
(1657, 'stock', 'insert', 99, 1, '2026-07-22 17:01:24'),
(1658, 'stock', 'insert', 100, 1, '2026-07-22 17:01:25'),
(1659, 'stock', 'insert', 101, 1, '2026-07-22 17:01:25'),
(1660, 'stock', 'insert', 102, 1, '2026-07-22 17:01:25'),
(1661, 'stock', 'insert', 103, 1, '2026-07-22 17:01:26'),
(1662, 'stock', 'insert', 104, 1, '2026-07-22 17:01:26'),
(1663, 'stock', 'insert', 105, 1, '2026-07-22 17:01:27'),
(1664, 'stock', 'insert', 106, 1, '2026-07-22 17:01:27'),
(1665, 'stock', 'insert', 107, 1, '2026-07-22 17:01:27'),
(1666, 'stock', 'insert', 108, 1, '2026-07-22 17:01:28'),
(1667, 'stock', 'insert', 109, 1, '2026-07-22 17:01:28'),
(1668, 'stock', 'insert', 110, 1, '2026-07-22 17:01:29'),
(1669, 'stock', 'insert', 111, 1, '2026-07-22 17:01:29'),
(1670, 'stock', 'insert', 112, 1, '2026-07-22 17:01:29'),
(1671, 'stock', 'insert', 113, 1, '2026-07-22 17:01:30'),
(1672, 'stock', 'insert', 114, 1, '2026-07-22 17:01:30'),
(1673, 'stock', 'insert', 115, 1, '2026-07-22 17:01:31'),
(1674, 'stock', 'insert', 116, 1, '2026-07-22 17:01:31'),
(1675, 'stock', 'insert', 117, 1, '2026-07-22 17:01:31'),
(1676, 'stock', 'insert', 118, 1, '2026-07-22 17:01:32'),
(1677, 'stock', 'insert', 119, 1, '2026-07-22 17:01:32'),
(1678, 'stock', 'insert', 120, 1, '2026-07-22 17:01:33'),
(1679, 'stock', 'insert', 121, 1, '2026-07-22 17:01:33'),
(1680, 'stock', 'insert', 122, 1, '2026-07-22 17:01:33'),
(1681, 'stock', 'insert', 123, 1, '2026-07-22 17:01:34'),
(1682, 'stock', 'insert', 124, 1, '2026-07-22 17:01:34'),
(1683, 'stock', 'insert', 125, 1, '2026-07-22 17:01:35'),
(1684, 'stock', 'insert', 126, 1, '2026-07-22 17:01:35'),
(1685, 'stock', 'insert', 127, 1, '2026-07-22 17:01:35'),
(1686, 'stock', 'insert', 128, 1, '2026-07-22 17:01:36'),
(1687, 'stock', 'insert', 129, 1, '2026-07-22 17:01:36'),
(1688, 'stock', 'insert', 130, 1, '2026-07-22 17:01:37'),
(1689, 'stock', 'insert', 131, 1, '2026-07-22 17:01:37'),
(1690, 'stock', 'insert', 132, 1, '2026-07-22 17:01:37'),
(1691, 'stock', 'insert', 133, 1, '2026-07-22 17:01:38'),
(1692, 'stock', 'insert', 134, 1, '2026-07-22 17:01:38'),
(1693, 'stock', 'insert', 135, 1, '2026-07-22 17:01:39'),
(1694, 'stock', 'insert', 136, 1, '2026-07-22 17:01:39'),
(1695, 'stock', 'insert', 137, 1, '2026-07-22 17:01:40'),
(1696, 'stock', 'insert', 138, 1, '2026-07-22 17:01:40'),
(1697, 'stock', 'insert', 139, 1, '2026-07-22 17:01:40'),
(1698, 'stock', 'insert', 140, 1, '2026-07-22 17:01:41'),
(1699, 'stock', 'insert', 141, 1, '2026-07-22 17:01:41'),
(1700, 'stock', 'insert', 142, 1, '2026-07-22 17:01:42'),
(1701, 'stock', 'insert', 143, 1, '2026-07-22 17:01:42'),
(1702, 'stock', 'insert', 144, 1, '2026-07-22 17:01:42'),
(1703, 'stock', 'insert', 145, 1, '2026-07-22 17:01:43'),
(1704, 'stock', 'insert', 146, 1, '2026-07-22 17:01:43'),
(1705, 'stock', 'insert', 147, 1, '2026-07-22 17:01:44'),
(1706, 'stock', 'insert', 148, 1, '2026-07-22 17:01:44'),
(1707, 'stock', 'insert', 149, 1, '2026-07-22 17:01:44'),
(1708, 'stock', 'insert', 150, 1, '2026-07-22 17:01:45'),
(1709, 'stock', 'insert', 151, 1, '2026-07-22 17:01:45'),
(1710, 'stock', 'insert', 152, 1, '2026-07-22 17:01:46'),
(1711, 'stock', 'insert', 1, 1, '2026-07-24 14:21:21'),
(1712, 'stock', 'insert', 2, 1, '2026-07-24 14:21:21'),
(1713, 'purchase_order', 'insert', 1, 1, '2026-07-25 03:55:58'),
(1714, 'purchase_order', 'insert', 2, 588420, '2026-07-25 03:56:45'),
(1715, 'purchase_order', 'delete', 1, 1, '2026-07-24 22:27:07'),
(1716, 'purchase_order', 'insert', 3, 1, '2026-07-25 03:58:27'),
(1717, 'purchase_order', 'update', 3, 1, '2026-07-25 04:02:09'),
(1718, 'purchase_order', 'update', 2, 588420, '2026-07-25 04:02:10'),
(1719, 'purchase', 'insert', 1, 1, '2026-07-25 04:03:09'),
(1720, 'purchase', 'delete', 1, 1, '2026-07-24 22:34:14'),
(1721, 'purchase', 'insert', 2, 588420, '2026-07-25 04:06:10'),
(1722, 'purchase_order', 'update', 2, 1, '2026-07-25 04:10:37'),
(1723, 'purchase_order', 'insert', 4, 588420, '2026-07-25 04:10:41'),
(1724, 'purchase_order', 'update', 2, 1, '2026-07-25 04:11:01'),
(1725, 'purchase_order', 'update', 4, 588420, '2026-07-25 04:11:13'),
(1726, 'purchase_order', 'update', 4, 588420, '2026-07-25 04:11:57'),
(1727, 'purchase_order', 'delete', 4, 588420, '2026-07-24 22:43:09'),
(1728, 'purchase_order', 'delete', 2, 1, '2026-07-24 22:47:28'),
(1729, 'purchase_order', 'delete', 2, 588420, '2026-07-24 22:47:31'),
(1730, 'purchase', 'delete', 2, 588420, '2026-07-24 22:47:44'),
(1731, 'purchase_order', 'delete', 3, 588420, '2026-07-24 22:47:54'),
(1732, 'purchase_order', 'insert', 5, 588420, '2026-07-25 04:18:22'),
(1733, 'purchase_order', 'delete', 5, 588420, '2026-07-24 22:48:52'),
(1734, 'sales_order', 'insert', 1, 588420, '2026-07-25 04:21:17'),
(1735, 'sales_order', 'insert', 2, 1, '2026-07-25 04:23:28'),
(1736, 'sales_order', 'update', 1, 588420, '2026-07-25 04:25:31'),
(1737, 'sales_order', 'update', 1, 588420, '2026-07-25 04:27:33'),
(1738, 'sales_order', 'update', 1, 588420, '2026-07-25 04:29:42'),
(1739, 'sales_order', 'insert', 3, 588420, '2026-07-25 04:30:37'),
(1740, 'purchase_order', 'insert', 6, 588420, '2026-07-25 04:32:21'),
(1741, 'purchase_order', 'update', 6, 588420, '2026-07-25 04:32:48'),
(1742, 'purchase_order', 'delete', 6, 588420, '2026-07-24 23:03:05'),
(1743, 'sales_order', 'update', 1, 588420, '2026-07-25 04:34:11'),
(1744, 'sales_order', 'update', 1, 588420, '2026-07-25 04:34:41'),
(1745, 'sale', 'insert', 1, 588420, '2026-07-25 04:37:59'),
(1746, 'sale', 'insert', 2, 1, '2026-07-25 04:38:39'),
(1747, 'sales_order', 'insert', 4, 588420, '2026-07-25 04:41:46'),
(1748, 'sale', 'update', 2, 1, '2026-07-25 04:42:36'),
(1749, 'sales_order', 'update', 1, 588420, '2026-07-25 04:42:52'),
(1750, 'sales_order', 'update', 3, 588420, '2026-07-25 04:42:59'),
(1751, 'sales_order', 'update', 4, 588420, '2026-07-25 04:43:04'),
(1752, 'sale', 'update', 1, 588420, '2026-07-25 04:43:27'),
(1753, 'sales_order', 'update', 2, 588420, '2026-07-25 04:43:44'),
(1754, 'service_order', 'insert', 1, 588420, '2026-07-25 04:48:22'),
(1755, 'service_order', 'update', 1, 588420, '2026-07-25 05:01:06'),
(1756, 'service', 'insert', 1, 1, '2026-07-25 05:17:33'),
(1757, 'service', 'update', 1, 1, '2026-07-25 05:47:00'),
(1758, 'service', 'insert', 2, 1, '2026-07-25 05:47:47'),
(1759, 'service', 'update', 2, 1, '2026-07-25 05:48:15'),
(1760, 'service_order', 'insert', 2, 1, '2026-07-25 05:50:21'),
(1761, 'service', 'update', 2, 1, '2026-07-25 05:53:25'),
(1762, 'service_order', 'update', 1, 1, '2026-07-25 05:53:54'),
(1763, 'service_order', 'update', 1, 1, '2026-07-25 05:55:30'),
(1764, 'service_order', 'insert', 3, 588420, '2026-07-25 05:58:22'),
(1765, 'service', 'insert', 3, 588420, '2026-07-25 05:59:09'),
(1766, 'service', 'update', 3, 588420, '2026-07-25 05:59:28'),
(1767, 'service', 'insert', 4, 588420, '2026-07-25 05:59:48'),
(1768, 'service', 'update', 4, 588420, '2026-07-25 06:00:32'),
(1769, 'service_order', 'update', 1, 588420, '2026-07-25 06:01:25'),
(1770, 'service_order', 'update', 2, 588420, '2026-07-25 06:01:32'),
(1771, 'service_order', 'update', 3, 588420, '2026-07-25 06:01:52'),
(1772, 'service', 'insert', 5, 588420, '2026-07-25 06:02:40'),
(1773, 'service', 'insert', 6, 588420, '2026-07-25 06:03:38');
INSERT INTO `logs` (`id`, `screen`, `operation`, `pid`, `userid`, `lastupdatedate`) VALUES
(1774, 'service', 'insert', 7, 1, '2026-07-25 06:06:19'),
(1775, 'service', 'update', 5, 588420, '2026-07-25 06:08:29'),
(1776, 'service', 'update', 5, 588420, '2026-07-25 06:09:41'),
(1777, 'payment_voucher', 'update', 1, 588420, '2026-07-25 01:06:53'),
(1778, 'payment_voucher', 'update', 2, 588420, '2026-07-25 01:08:28'),
(1779, 'payment_voucher', 'update', 3, 1, '2026-07-25 01:09:20'),
(1780, 'sale', 'insert', 3, 588420, '2026-07-25 06:49:14'),
(1781, 'payment_voucher', 'update', 4, 1, '2026-07-25 01:21:00'),
(1782, 'payment_voucher', 'update', 1, 588420, '2026-07-25 06:52:30'),
(1783, 'receipt_voucher', 'update', 5, 588420, '2026-07-25 01:23:25'),
(1784, 'receipt_voucher', 'update', 5, 588420, '2026-07-25 06:55:31'),
(1785, 'contra_voucher', 'update', 6, 588420, '2026-07-25 01:46:30'),
(1786, 'contra_voucher', 'update', 6, 588420, '2026-07-25 07:20:26'),
(1787, 'payment_voucher', 'update', 2, 588420, '2026-07-25 07:20:58'),
(1788, 'payment_voucher', 'update', 7, 588420, '2026-07-25 01:55:12'),
(1789, 'receipt_voucher', 'update', 8, 588420, '2026-07-25 01:59:17'),
(1790, 'receipt_voucher', 'update', 8, 588420, '2026-07-25 07:30:01'),
(1791, 'contra_voucher', 'update', 9, 588420, '2026-07-25 02:02:44'),
(1792, 'contra_voucher', 'update', 9, 588420, '2026-07-25 07:35:00'),
(1793, 'contra_voucher', 'update', 10, 588420, '2026-07-25 02:05:40'),
(1794, 'sale', 'insert', 4, 1, '2026-07-25 07:42:11'),
(1795, 'service', 'insert', 8, 1, '2026-07-25 07:49:33'),
(1796, 'service', 'update', 8, 1, '2026-07-25 07:53:19'),
(1797, 'service', 'update', 7, 1, '2026-07-25 07:53:25'),
(1798, 'purchase', 'insert', 3, 1, '2026-07-25 08:30:39'),
(1799, 'sale', 'insert', 5, 1, '2026-07-25 08:31:23'),
(1800, 'sale', 'update', 5, 1, '2026-07-25 08:34:18'),
(1801, 'GDN', 'insert', 1, 1, '2026-07-25 08:43:15'),
(1802, 'stock', 'update', 1, 1, '2026-07-25 08:43:50'),
(1803, 'purchase', 'insert', 4, 1, '2026-07-25 10:40:42'),
(1804, 'purchase', 'insert', 5, 1, '2026-07-25 10:53:14'),
(1805, 'purchase', 'insert', 6, 1, '2026-07-25 10:58:22'),
(1806, 'purchase', 'insert', 7, 1, '2026-07-25 11:11:19'),
(1807, 'purchase', 'insert', 8, 1, '2026-07-25 11:13:35'),
(1808, 'service_order', 'insert', 4, 150050, '2026-07-25 14:45:30'),
(1809, 'purchase', 'insert', 9, 1, '2026-07-25 15:50:26'),
(1810, 'sale', 'insert', 6, 1, '2026-07-25 16:10:46'),
(1811, 'purchase', 'insert', 10, 1, '2026-07-25 16:32:27'),
(1812, 'purchase', 'insert', 11, 1, '2026-07-25 16:39:25'),
(1813, 'sale', 'update', 6, 559079, '2026-07-25 17:45:48'),
(1814, 'purchase', 'insert', 12, 1, '2026-07-26 04:26:34'),
(1815, 'purchase', 'insert', 13, 1, '2026-07-26 04:38:51'),
(1816, 'purchase', 'insert', 14, 1, '2026-07-26 04:41:01'),
(1817, 'purchase', 'insert', 15, 1, '2026-07-26 04:42:35'),
(1818, 'purchase', 'update', 15, 1, '2026-07-26 04:52:34'),
(1819, 'sale', 'insert', 7, 1, '2026-07-26 04:55:10'),
(1820, 'purchase', 'insert', 16, 1, '2026-07-26 05:04:55'),
(1821, 'sale', 'insert', 8, 1, '2026-07-26 05:06:12'),
(1822, 'purchase', 'insert', 17, 1, '2026-07-26 05:13:01'),
(1823, 'sale', 'insert', 9, 1, '2026-07-26 05:13:20'),
(1824, 'purchase_order', 'insert', 7, 1, '2026-07-26 05:15:12'),
(1825, 'purchase', 'insert', 18, 1, '2026-07-26 05:15:51'),
(1826, 'purchase', 'update', 17, 1, '2026-07-26 05:16:01'),
(1827, 'purchase_return', 'insert', 1, 1, '2026-07-25 23:46:26'),
(1828, 'purchase', 'insert', 19, 1, '2026-07-26 05:16:56'),
(1829, 'sales_return', 'insert', 1, 1, '2026-07-26 05:17:00'),
(1830, 'purchase', 'update', 17, 1, '2026-07-26 05:22:04'),
(1831, 'purchase', 'insert', 20, 1, '2026-07-26 05:23:20'),
(1832, 'sale', 'insert', 10, 1, '2026-07-26 05:26:23'),
(1833, 'sale', 'insert', 11, 1, '2026-07-26 05:26:27'),
(1834, 'sale', 'insert', 12, 1, '2026-07-26 05:44:02'),
(1835, 'purchase', 'insert', 21, 1, '2026-07-26 05:47:37'),
(1836, 'sale', 'insert', 13, 1, '2026-07-26 05:48:25'),
(1837, 'sale', 'update', 13, 1, '2026-07-26 05:51:20'),
(1838, 'sale', 'update', 12, 1, '2026-07-26 05:51:47'),
(1839, 'sale', 'update', 10, 1, '2026-07-26 05:52:13'),
(1840, 'sale', 'update', 8, 1, '2026-07-26 05:52:51'),
(1841, 'sale', 'update', 7, 1, '2026-07-26 05:53:23'),
(1842, 'purchase', 'insert', 22, 1, '2026-07-26 06:08:56'),
(1843, 'purchase', 'update', 22, 1, '2026-07-26 06:10:44'),
(1844, 'purchase', 'insert', 23, 1, '2026-07-26 06:12:08'),
(1845, 'purchase', 'insert', 24, 1, '2026-07-26 06:15:46'),
(1846, 'purchase', 'insert', 25, 1, '2026-07-26 06:28:36'),
(1847, 'stock', 'insert', 3, 1, '2026-07-26 06:49:06'),
(1848, 'stock', 'delete', 3, 1, '2026-07-26 01:19:42'),
(1849, 'stock', 'insert', 4, 1, '2026-07-26 06:50:03'),
(1850, 'stock', 'insert', 5, 1, '2026-07-26 06:51:10'),
(1851, 'stock', 'update', 4, 1, '2026-07-26 06:51:40'),
(1852, 'stock', 'update', 5, 1, '2026-07-26 06:52:10'),
(1853, 'stock', 'update', 4, 1, '2026-07-26 06:52:21'),
(1854, 'stock', 'insert', 6, 1, '2026-07-26 06:55:02'),
(1855, 'stock', 'update', 6, 1, '2026-07-26 06:55:58'),
(1856, 'stock', 'insert', 7, 1, '2026-07-26 06:58:48'),
(1857, 'stock', 'insert', 8, 1, '2026-07-26 07:02:03'),
(1858, 'stock', 'insert', 9, 1, '2026-07-26 07:04:57'),
(1859, 'stock', 'update', 9, 1, '2026-07-26 07:05:45'),
(1860, 'stock', 'insert', 10, 1, '2026-07-26 07:06:20'),
(1861, 'stock', 'insert', 11, 1, '2026-07-26 07:10:32'),
(1862, 'GDN', 'insert', 2, 1, '2026-07-26 07:11:46'),
(1863, 'gdn', 'update', 2, 1, '2026-07-26 07:12:38'),
(1864, 'stock', 'insert', 12, 1, '2026-07-26 07:15:20'),
(1865, 'GRN', 'insert', 1, 1, '2026-07-26 07:17:14'),
(1866, 'grn', 'update', 1, 1, '2026-07-26 07:17:38'),
(1867, 'GDN', 'insert', 3, 1, '2026-07-26 07:18:27'),
(1868, 'stock', 'insert', 13, 1, '2026-07-26 07:20:51'),
(1869, 'stock', 'update', 13, 1, '2026-07-26 07:22:45'),
(1870, 'stock', 'delete', 13, 1, '2026-07-26 01:53:05'),
(1871, 'stock', 'insert', 14, 1, '2026-07-26 07:26:25'),
(1872, 'stock', 'update', 14, 1, '2026-07-26 07:28:06'),
(1873, 'stock', 'update', 14, 1, '2026-07-26 07:33:24'),
(1874, 'stock', 'insert', 15, 1, '2026-07-26 07:34:23'),
(1875, 'stock', 'update', 15, 1, '2026-07-26 07:34:43'),
(1876, 'stock', 'delete', 15, 1, '2026-07-26 02:09:02'),
(1877, 'stock', 'update', 14, 1, '2026-07-26 07:40:11'),
(1878, 'stock', 'delete', 14, 1, '2026-07-26 02:10:24'),
(1879, 'stock', 'insert', 16, 1, '2026-07-26 08:23:14'),
(1880, 'GDN', 'insert', 4, 1, '2026-07-26 08:23:46'),
(1881, 'GRN', 'insert', 2, 1, '2026-07-26 08:24:10'),
(1882, 'stock', 'update', 16, 1, '2026-07-26 08:24:46'),
(1883, 'stock', 'insert', 17, 1, '2026-07-26 08:27:32'),
(1884, 'stock', 'insert', 18, 1, '2026-07-26 08:29:20'),
(1885, 'GDN', 'insert', 5, 1, '2026-07-26 08:30:03'),
(1886, 'stock', 'update', 18, 1, '2026-07-26 08:30:46'),
(1887, 'stock', 'update', 18, 1, '2026-07-26 08:31:08'),
(1888, 'stock', 'insert', 19, 1, '2026-07-26 08:32:02'),
(1889, 'GRN', 'insert', 3, 1, '2026-07-26 08:32:36'),
(1890, 'stock', 'insert', 20, 1, '2026-07-26 08:33:56'),
(1891, 'stock', 'insert', 21, 1, '2026-07-26 08:35:29'),
(1892, 'GRN', 'insert', 4, 1, '2026-07-26 08:36:12'),
(1893, 'GDN', 'insert', 6, 1, '2026-07-26 08:36:46'),
(1894, 'purchase', 'insert', 26, 1, '2026-07-26 08:52:29'),
(1895, 'GRN', 'insert', 5, 1, '2026-07-26 08:56:17'),
(1896, 'grn', 'update', 5, 1, '2026-07-26 08:57:10'),
(1897, 'GRN', 'insert', 6, 1, '2026-07-26 08:58:11'),
(1898, 'GRN', 'insert', 7, 1, '2026-07-26 08:59:50'),
(1899, 'GRN', 'insert', 8, 1, '2026-07-26 09:00:48'),
(1900, 'grn', 'delete', 8, 1, '2026-07-26 03:31:01'),
(1901, 'purchase', 'insert', 27, 588420, '2026-07-26 09:03:08'),
(1902, 'purchase', 'update', 27, 588420, '2026-07-26 09:04:02'),
(1903, 'purchase', 'insert', 28, 588420, '2026-07-26 09:06:39'),
(1904, 'GRN', 'insert', 9, 588420, '2026-07-26 09:08:13'),
(1905, 'purchase_order', 'insert', 8, 588420, '2026-07-26 09:11:26'),
(1906, 'purchase', 'insert', 29, 588420, '2026-07-26 09:12:29'),
(1907, 'GRN', 'insert', 10, 588420, '2026-07-26 09:13:06'),
(1908, 'purchase', 'update', 29, 588420, '2026-07-26 09:13:58'),
(1909, 'purchase_order', 'insert', 9, 588420, '2026-07-26 09:16:15'),
(1910, 'purchase', 'insert', 30, 588420, '2026-07-26 09:20:11'),
(1911, 'purchase', 'update', 30, 588420, '2026-07-26 09:21:01'),
(1912, 'purchase', 'update', 30, 588420, '2026-07-26 09:21:50'),
(1913, 'GRN', 'insert', 11, 588420, '2026-07-26 09:23:23'),
(1914, 'sale', 'update', 30, 588420, '2026-07-26 09:23:52'),
(1915, 'sale', 'update', 30, 588420, '2026-07-26 09:25:11'),
(1916, 'grn', 'delete', 11, 588420, '2026-07-26 03:55:22'),
(1917, 'purchase', 'delete', 30, 588420, '2026-07-26 03:55:36'),
(1918, 'purchase', 'insert', 31, 1, '2026-07-26 09:28:06'),
(1919, 'GRN', 'insert', 12, 1, '2026-07-26 09:28:53'),
(1920, 'purchase', 'update', 31, 1, '2026-07-26 09:29:44'),
(1921, 'grn', 'update', 12, 1, '2026-07-26 09:30:08'),
(1922, 'grn', 'update', 12, 1, '2026-07-26 09:30:53'),
(1923, 'grn', 'update', 12, 1, '2026-07-26 09:31:29'),
(1924, 'grn', 'delete', 12, 1, '2026-07-26 04:03:38'),
(1925, 'purchase', 'delete', 31, 1, '2026-07-26 04:03:58'),
(1926, 'purchase', 'insert', 32, 1, '2026-07-26 09:34:50'),
(1927, 'GRN', 'insert', 13, 1, '2026-07-26 09:35:39'),
(1928, 'purchase', 'insert', 33, 1, '2026-07-26 09:42:32'),
(1929, 'purchase_return', 'insert', 2, 1, '2026-07-26 04:14:00'),
(1930, 'GDN', 'insert', 7, 1, '2026-07-26 09:46:32'),
(1931, 'GDN', 'insert', 8, 1, '2026-07-26 09:46:59'),
(1932, 'purchase_return', 'update', 2, 1, '2026-07-26 09:49:06'),
(1933, 'gdn', 'update', 8, 1, '2026-07-26 09:49:48'),
(1934, 'gdn', 'update', 8, 1, '2026-07-26 09:51:53'),
(1935, 'gdn', 'update', 7, 1, '2026-07-26 09:52:00'),
(1936, 'purchase', 'insert', 34, 588420, '2026-07-26 09:56:59'),
(1937, 'purchase_return', 'insert', 3, 588420, '2026-07-26 04:27:40'),
(1938, 'purchase_return', 'update', 3, 588420, '2026-07-26 09:59:59'),
(1939, 'purchase_return', 'update', 3, 588420, '2026-07-26 10:01:04'),
(1940, 'GDN', 'insert', 9, 588420, '2026-07-26 10:03:57'),
(1941, 'purchase', 'insert', 35, 588420, '2026-07-26 10:06:44'),
(1942, 'GRN', 'insert', 14, 588420, '2026-07-26 10:08:55'),
(1943, 'grn', 'delete', 14, 588420, '2026-07-26 04:39:45'),
(1944, 'GRN', 'insert', 15, 588420, '2026-07-26 10:10:06'),
(1945, 'grn', 'delete', 15, 588420, '2026-07-26 04:40:47'),
(1946, 'purchase_return', 'insert', 4, 588420, '2026-07-26 04:41:25'),
(1947, 'GDN', 'insert', 10, 588420, '2026-07-26 10:12:23'),
(1948, 'gdn', 'delete', 10, 588420, '2026-07-26 04:42:59'),
(1949, 'purchase_return', 'update', 4, 588420, '2026-07-26 04:43:23'),
(1950, 'purchase', 'delete', 35, 588420, '2026-07-26 04:43:35'),
(1951, 'purchase', 'insert', 36, 588420, '2026-07-26 10:21:26'),
(1952, 'purchase_return', 'insert', 5, 588420, '2026-07-26 04:52:11'),
(1953, 'purchase_return', 'update', 5, 588420, '2026-07-26 04:53:35'),
(1954, 'purchase', 'delete', 36, 588420, '2026-07-26 04:53:46'),
(1955, 'sales_order', 'insert', 5, 588420, '2026-07-26 10:44:48'),
(1956, 'sales_order', 'update', 5, 588420, '2026-07-26 10:45:12'),
(1957, 'sale', 'insert', 14, 588420, '2026-07-26 10:47:17'),
(1958, 'GDN', 'insert', 11, 588420, '2026-07-26 10:47:55'),
(1959, 'sale', 'insert', 15, 1, '2026-07-26 10:56:38'),
(1960, 'GDN', 'insert', 12, 1, '2026-07-26 11:00:16'),
(1961, 'GDN', 'insert', 13, 1, '2026-07-26 11:01:06'),
(1962, 'GDN', 'insert', 14, 1, '2026-07-26 11:01:45'),
(1963, 'GDN', 'insert', 15, 1, '2026-07-26 11:02:47'),
(1964, 'sale', 'insert', 16, 1, '2026-07-26 11:05:09'),
(1965, 'sale', 'update', 16, 1, '2026-07-26 11:05:41'),
(1966, 'GDN', 'insert', 16, 1, '2026-07-26 11:07:07'),
(1967, 'gdn', 'update', 16, 1, '2026-07-26 11:07:42'),
(1968, 'GDN', 'insert', 17, 1, '2026-07-26 11:08:35'),
(1969, 'gdn', 'delete', 17, 1, '2026-07-26 05:39:19'),
(1970, 'GDN', 'insert', 18, 1, '2026-07-26 11:09:53'),
(1971, 'sale', 'insert', 17, 1, '2026-07-26 11:12:13'),
(1972, 'sale', 'insert', 18, 1, '2026-07-26 11:13:54'),
(1973, 'sale', 'insert', 19, 1, '2026-07-26 11:17:12'),
(1974, 'sale', 'update', 19, 1, '2026-07-26 11:18:02'),
(1975, 'sale', 'update', 19, 1, '2026-07-26 11:19:40'),
(1976, 'GDN', 'insert', 19, 1, '2026-07-26 11:20:41'),
(1977, 'gdn', 'delete', 19, 1, '2026-07-26 05:53:39'),
(1978, 'GDN', 'insert', 20, 1, '2026-07-26 11:27:28'),
(1979, 'sale', 'insert', 20, 1, '2026-07-26 11:30:36'),
(1980, 'sale', 'update', 20, 1, '2026-07-26 11:31:12'),
(1981, 'sale', 'update', 20, 1, '2026-07-26 11:31:32'),
(1982, 'sale', 'update', 19, 1, '2026-07-26 11:31:51'),
(1983, 'sale', 'update', 19, 1, '2026-07-26 11:32:42'),
(1984, 'sale', 'insert', 21, 588420, '2026-07-26 11:35:31'),
(1985, 'sale', 'update', 21, 588420, '2026-07-26 11:36:48'),
(1986, 'GDN', 'insert', 21, 588420, '2026-07-26 11:37:51'),
(1987, 'GDN', 'insert', 22, 588420, '2026-07-26 11:38:21'),
(1988, 'GDN', 'insert', 23, 588420, '2026-07-26 11:38:51'),
(1989, 'GDN', 'insert', 24, 588420, '2026-07-26 11:39:31'),
(1990, 'sale', 'update', 21, 588420, '2026-07-26 11:43:52'),
(1991, 'sales_return', 'insert', 2, 588420, '2026-07-26 11:49:26'),
(1992, 'GRN', 'insert', 16, 588420, '2026-07-26 11:51:32'),
(1993, 'GRN', 'insert', 17, 588420, '2026-07-26 11:52:23'),
(1994, 'GRN', 'insert', 18, 588420, '2026-07-26 11:54:16'),
(1995, 'grn', 'delete', 17, 588420, '2026-07-26 06:29:47'),
(1996, 'grn', 'delete', 18, 588420, '2026-07-26 06:30:17'),
(1997, 'GRN', 'insert', 19, 588420, '2026-07-26 12:00:52'),
(1998, 'sale', 'update', 19, 1, '2026-07-26 12:09:37'),
(1999, 'gdn', 'update', 20, 1, '2026-07-26 12:10:30'),
(2000, 'grn', 'update', 19, 588420, '2026-07-26 12:15:44'),
(2001, 'GRN', 'insert', 20, 588420, '2026-07-26 12:16:29'),
(2002, 'grn', 'update', 19, 588420, '2026-07-26 12:16:55'),
(2003, 'sale', 'insert', 22, 588420, '2026-07-26 12:18:45'),
(2004, 'sales_return', 'insert', 3, 588420, '2026-07-26 12:19:26'),
(2005, 'GDN', 'insert', 25, 588420, '2026-07-26 12:20:13'),
(2006, 'sales_return', 'update', 3, 588420, '2026-07-26 12:20:45'),
(2007, 'sales_return', 'update', 3, 588420, '2026-07-26 12:21:26'),
(2008, 'sales_return', 'update', 3, 588420, '2026-07-26 12:21:49'),
(2009, 'GRN', 'insert', 21, 588420, '2026-07-26 12:22:25'),
(2010, 'sale', 'insert', 23, 588420, '2026-07-26 12:23:50'),
(2011, 'sales_return', 'insert', 4, 588420, '2026-07-26 12:25:32'),
(2012, 'sales_return', 'update', 4, 588420, '2026-07-26 12:26:21'),
(2013, 'sale', 'update', 23, 588420, '2026-07-26 12:26:51'),
(2014, 'purchase', 'insert', 37, 588420, '2026-07-26 12:27:50'),
(2015, 'purchase_return', 'insert', 6, 588420, '2026-07-26 06:58:32'),
(2016, 'purchase_return', 'update', 6, 588420, '2026-07-26 06:58:56'),
(2017, 'purchase', 'delete', 37, 588420, '2026-07-26 06:59:07'),
(2018, 'sale', 'insert', 24, 1, '2026-07-26 12:50:23'),
(2019, 'sale', 'insert', 25, 1, '2026-07-26 12:51:43'),
(2020, 'sale', 'insert', 26, 1, '2026-07-26 12:52:41'),
(2021, 'sale', 'insert', 27, 1, '2026-07-26 12:52:41'),
(2022, 'sale', 'insert', 28, 1, '2026-07-26 12:52:42'),
(2023, 'sale', 'update', 24, 1, '2026-07-26 12:54:16'),
(2024, 'sale', 'update', 28, 1, '2026-07-26 12:54:26'),
(2025, 'sale', 'update', 27, 1, '2026-07-26 12:54:33'),
(2026, 'sale', 'update', 26, 1, '2026-07-26 12:54:40'),
(2027, 'sale', 'update', 25, 1, '2026-07-26 12:54:59'),
(2028, 'sale', 'insert', 29, 1, '2026-07-26 12:57:16'),
(2029, 'sale', 'insert', 30, 1, '2026-07-26 12:57:19'),
(2030, 'sale', 'insert', 31, 1, '2026-07-26 12:59:52'),
(2031, 'sale', 'insert', 32, 1, '2026-07-26 12:59:53'),
(2032, 'sale', 'insert', 33, 1, '2026-07-26 12:59:53'),
(2033, 'sale', 'insert', 34, 1, '2026-07-26 12:59:54'),
(2034, 'sale', 'insert', 35, 1, '2026-07-26 13:01:12'),
(2035, 'sale', 'update', 35, 1, '2026-07-26 13:01:42'),
(2036, 'sale', 'update', 34, 1, '2026-07-26 13:03:17'),
(2037, 'sale', 'update', 33, 1, '2026-07-26 13:03:27'),
(2038, 'sale', 'update', 32, 1, '2026-07-26 13:03:38'),
(2039, 'sale', 'update', 31, 1, '2026-07-26 13:03:45'),
(2040, 'sale', 'update', 30, 1, '2026-07-26 13:03:53'),
(2041, 'sale', 'insert', 36, 1, '2026-07-26 13:07:33'),
(2042, 'sale', 'update', 36, 1, '2026-07-26 13:10:44'),
(2043, 'sale', 'insert', 37, 1, '2026-07-26 13:11:51'),
(2044, 'sale', 'insert', 38, 1, '2026-07-26 13:11:52'),
(2045, 'sale', 'update', 38, 1, '2026-07-26 13:14:01'),
(2046, 'sale', 'update', 37, 1, '2026-07-26 13:14:08'),
(2047, 'sale', 'insert', 39, 1, '2026-07-26 13:15:19'),
(2048, 'sale', 'insert', 40, 1, '2026-07-26 13:29:15'),
(2049, 'sales_order', 'insert', 6, 1, '2026-07-26 16:35:29'),
(2050, 'sales_order', 'insert', 7, 1, '2026-07-26 16:39:45'),
(2051, 'sales_return', 'insert', 5, 1, '2026-07-26 16:41:50'),
(2052, 'service_order', 'insert', 5, 1, '2026-07-26 16:44:50'),
(2053, 'service', 'insert', 9, 1, '2026-07-26 16:46:10'),
(2054, 'GRN', 'insert', 22, 1, '2026-07-26 16:55:31'),
(2055, 'payment_voucher', 'update', 11, 1, '2026-07-26 11:37:56'),
(2056, 'receipt_voucher', 'update', 12, 1, '2026-07-26 11:39:12'),
(2057, 'contra_voucher', 'update', 13, 1, '2026-07-26 11:40:02'),
(2058, 'sale', 'update', 40, 1, '2026-07-29 11:22:14'),
(2059, 'purchase_order', 'insert', 10, 1, '2026-08-14 14:35:26'),
(2060, 'purchase', 'insert', 38, 1, '2026-08-14 14:36:06'),
(2061, 'purchase', 'delete', 38, 1, '2026-08-14 09:06:38'),
(2062, 'purchase_order', 'update', 10, 1, '2026-08-14 14:36:57'),
(2063, 'purchase_order', 'update', 10, 1, '2026-08-14 14:37:13'),
(2064, 'purchase_order', 'update', 10, 1, '2026-08-14 14:37:20'),
(2065, 'purchase_order', 'update', 10, 1, '2026-08-14 14:38:43'),
(2066, 'purchase_order', 'delete', 10, 1, '2026-08-14 09:10:15'),
(2067, 'sales_order', 'insert', 8, 1, '2026-08-14 15:41:22'),
(2068, 'sales_order', 'update', 8, 1, '2026-08-14 15:42:15'),
(2069, 'sales_order', 'insert', 9, 1, '2026-08-15 03:44:41'),
(2070, 'sales_order', 'insert', 10, 1, '2026-08-15 03:46:02'),
(2071, 'service_order', 'insert', 6, 1, '2026-08-15 03:46:43'),
(2072, 'sales_order', 'insert', 11, 1, '2026-08-15 03:49:52'),
(2073, 'service_order', 'insert', 7, 1, '2026-08-15 03:50:34'),
(2074, 'stock', 'insert', 22, 1, '2026-08-15 12:31:01'),
(2075, 'stock', 'delete', 22, 1, '2026-08-15 07:01:09'),
(2076, 'service_order', 'insert', 8, 1, '2026-08-15 12:36:30'),
(2077, 'sale', 'insert', 41, 210, '2026-08-15 12:46:18'),
(2078, 'sale', 'insert', 42, 559079, '2026-08-15 12:54:43'),
(2079, 'stock', 'insert', 23, 1, '2026-08-15 13:09:39'),
(2080, 'sale', 'insert', 43, 1, '2026-08-15 13:10:26'),
(2081, 'sale', 'insert', 44, 1, '2026-08-15 13:12:25'),
(2082, 'service_order', 'insert', 9, 1, '2026-08-15 13:14:00'),
(2083, 'service', 'insert', 10, 1, '2026-08-15 13:15:26'),
(2084, 'sale', 'insert', 45, 1, '2026-08-16 05:31:40'),
(2085, 'contra_voucher', 'update', 14, 1, '2026-08-17 09:44:12'),
(2086, 'contra_voucher', 'update', 14, 1, '2026-08-17 15:15:07'),
(2087, 'contra_voucher', 'update', 15, 1, '2026-08-17 09:45:31'),
(2088, 'contra_voucher', 'update', 15, 1, '2026-08-17 15:15:40'),
(2089, 'contra_voucher', 'update', 16, 1, '2026-08-17 09:46:03'),
(2090, 'receipt_voucher', 'update', 17, 1, '2026-08-17 09:47:09'),
(2091, 'contra_voucher', 'update', 16, 1, '2026-08-17 15:18:09'),
(2092, 'sale', 'insert', 46, 1, '2026-08-18 01:50:48'),
(2093, 'sale', 'insert', 47, 1, '2026-08-18 01:51:13'),
(2094, 'sale', 'insert', 48, 1, '2026-08-18 02:03:14'),
(2095, 'sale', 'insert', 49, 1, '2026-08-18 18:56:02'),
(2096, 'sale', 'update', 49, 1, '2026-08-18 18:57:01'),
(2097, 'sale', 'update', 49, 1, '2026-08-18 18:57:33'),
(2098, 'sale', 'update', 49, 1, '2026-08-18 18:57:51'),
(2099, 'sale', 'insert', 50, 1, '2026-08-19 02:10:20'),
(2100, 'sale', 'insert', 51, 1, '2026-08-20 06:39:49'),
(2101, 'sale', 'update', 51, 1, '2026-08-20 06:40:26'),
(2102, 'sale', 'update', 40, 1, '2026-08-21 02:08:58'),
(2103, 'sale', 'update', 39, 1, '2026-08-21 02:09:05'),
(2104, 'sale', 'insert', 52, 1, '2026-09-03 08:27:26'),
(2105, 'sale', 'insert', 53, 1, '2026-09-03 08:47:24'),
(2106, 'sale', 'update', 53, 1, '2026-09-03 09:11:28'),
(2107, 'sale', 'insert', 54, 1, '2026-09-04 13:46:16'),
(2108, 'service', 'insert', 11, 1, '2026-09-05 18:21:44'),
(2109, 'service_order', 'insert', 10, 1, '2026-09-05 18:23:53'),
(2110, 'sale', 'insert', 55, 1, '2026-09-06 14:01:45'),
(2111, 'stock', 'insert', 26, 1, '2026-09-10 06:13:10'),
(2112, 'sale', 'insert', 56, 588420, '2026-09-10 07:03:10'),
(2113, 'stock', 'insert', 27, 1, '2026-09-12 04:27:18'),
(2114, 'stock', 'update', 27, 1, '2026-09-12 04:28:08'),
(2115, 'stock', 'insert', 28, 1, '2026-09-12 04:28:44'),
(2116, 'stock', 'delete', 1, 1, '2026-09-11 23:05:49'),
(2117, 'sale', 'insert', 57, 1, '2026-09-14 03:17:40'),
(2118, 'sale', 'insert', 58, 1, '2026-09-14 03:19:04'),
(2119, 'sale', 'insert', 59, 1, '2026-09-14 03:21:32'),
(2120, 'stock', 'insert', 29, 1, '2026-09-14 03:24:15'),
(2121, 'sale', 'insert', 60, 1, '2026-09-14 03:25:31'),
(2122, 'sale', 'update', 60, 1, '2026-09-14 03:26:17'),
(2123, 'sale', 'insert', 61, 1, '2026-09-14 03:26:44'),
(2124, 'sale', 'update', 61, 1, '2026-09-14 03:27:12'),
(2125, 'sale', 'insert', 62, 1, '2026-09-14 03:28:32'),
(2126, 'sale', 'insert', 63, 1, '2026-09-14 03:29:22'),
(2127, 'service', 'insert', 12, 1, '2026-09-14 03:40:27'),
(2128, 'service', 'insert', 13, 1, '2026-09-14 03:40:51'),
(2129, 'stock', 'insert', 30, 1, '2026-09-14 04:04:57'),
(2130, 'sale', 'insert', 64, 588420, '2026-09-18 09:54:27'),
(2131, 'purchase', 'delete', 41, 1, '2026-09-19 05:54:12'),
(2132, 'purchase', 'delete', 42, 1, '2026-09-19 05:54:22'),
(2133, 'purchase', 'insert', 43, 1, '2026-09-19 11:26:54'),
(2134, 'purchase', 'insert', 44, 1, '2026-09-19 11:57:35'),
(2135, 'purchase', 'insert', 45, 1, '2026-09-22 09:33:24'),
(2136, 'stock', 'insert', 1, 1, '2026-09-28 03:47:38'),
(2137, 'stock', 'insert', 2, 1, '2026-09-28 03:47:38'),
(2138, 'stock', 'insert', 3, 1, '2026-09-28 03:50:41'),
(2139, 'stock', 'insert', 4, 1, '2026-09-28 03:50:42'),
(2140, 'stock', 'insert', 5, 1, '2026-09-28 03:50:42'),
(2141, 'service_order', 'insert', 1, 1, '2026-09-30 10:49:08');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2026_08_26_135111_create_sec_branch_re_and_sec_store_re_tables', 1);

-- --------------------------------------------------------

--
-- Table structure for table `module`
--

CREATE TABLE `module` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `directory` varchar(100) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `module`
--

INSERT INTO `module` (`id`, `name`, `description`, `image`, `directory`, `status`) VALUES
(1, 'store', NULL, NULL, NULL, 1),
(2, 'service', NULL, NULL, NULL, 1),
(3, 'product', NULL, NULL, NULL, 1),
(4, 'supplier', NULL, NULL, NULL, 1),
(5, 'customer', NULL, NULL, NULL, 1),
(6, 'stock', NULL, NULL, NULL, 1),
(8, 'report', NULL, NULL, NULL, 1),
(9, 'purchase', NULL, NULL, NULL, 1),
(10, 'invoice', NULL, NULL, NULL, 1),
(11, 'sale_of_service', NULL, NULL, NULL, 1),
(12, 'hrm_management', NULL, NULL, NULL, 1),
(15, 'settings_preferences', NULL, NULL, NULL, 1),
(20, 'payment', NULL, NULL, 'payment', 1),
(21, 'branch', NULL, NULL, NULL, 1),
(22, 'datauploader', NULL, NULL, NULL, 1),
(24, 'warehouse_management', NULL, NULL, 'warehouse_management', 1),
(25, 'voucher', NULL, NULL, NULL, 1),
(26, 'alert', NULL, NULL, 'alert', 1);

-- --------------------------------------------------------

--
-- Table structure for table `module_re`
--

CREATE TABLE `module_re` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `module_re`
--

INSERT INTO `module_re` (`id`, `name`, `status`) VALUES
(1, 'dashboard', 1),
(2, 'branch', 1),
(3, 'store', 1),
(4, 'product', 1),
(5, 'service', 1),
(6, 'settings', 1),
(7, 'customer', 1),
(8, 'purchase', 1);

-- --------------------------------------------------------

--
-- Table structure for table `payment_receipt_type`
--

CREATE TABLE `payment_receipt_type` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `type` enum('Payment','Receipt','Common') NOT NULL DEFAULT 'Common',
  `detail` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_type`
--

CREATE TABLE `payment_type` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `nature` varchar(50) NOT NULL DEFAULT 'Cash',
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_type`
--

INSERT INTO `payment_type` (`id`, `name`, `nature`, `status`) VALUES
(1, 'Cash', 'Cash Nature', 1);

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phystock_details`
--

CREATE TABLE `phystock_details` (
  `id` int(11) NOT NULL,
  `product` int(11) NOT NULL,
  `batch` int(11) NOT NULL,
  `store` int(11) NOT NULL,
  `stock` varbinary(500) NOT NULL,
  `type` varchar(255) NOT NULL,
  `pid` int(14) NOT NULL,
  `date` date NOT NULL,
  `conversion_id` int(20) NOT NULL,
  `unit` int(20) NOT NULL,
  `pid2` int(100) NOT NULL,
  `invoicetype` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `print_setting`
--

CREATE TABLE `print_setting` (
  `id` int(11) NOT NULL,
  `header` int(11) NOT NULL,
  `footer` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `print_setting`
--

INSERT INTO `print_setting` (`id`, `header`, `footer`) VALUES
(1, 200, 100);

-- --------------------------------------------------------

--
-- Table structure for table `product_brand`
--

CREATE TABLE `product_brand` (
  `brand_id` int(11) NOT NULL,
  `brand_name` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_category`
--

CREATE TABLE `product_category` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_group`
--

CREATE TABLE `product_group` (
  `id` int(11) NOT NULL,
  `groupcode` varchar(100) NOT NULL,
  `name` varchar(1000) NOT NULL,
  `status` int(11) NOT NULL,
  `invoice_group` int(20) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_group_details`
--

CREATE TABLE `product_group_details` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `product` int(11) NOT NULL,
  `unit` int(11) NOT NULL,
  `parent` tinyint(1) NOT NULL,
  `qty` varbinary(1000) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_information`
--

CREATE TABLE `product_information` (
  `id` int(11) NOT NULL,
  `product_id` varchar(100) NOT NULL,
  `category_id` varchar(255) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `price` varbinary(1000) DEFAULT NULL,
  `unit` int(50) DEFAULT NULL,
  `tax` float DEFAULT NULL COMMENT 'Tax in %',
  `serial_no` varchar(200) DEFAULT NULL,
  `product_vat` float DEFAULT NULL,
  `product_model` varchar(100) DEFAULT NULL,
  `product_details` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `tax0` text DEFAULT NULL,
  `tax1` text DEFAULT NULL,
  `store` int(11) NOT NULL,
  `cost_price` varbinary(1000) NOT NULL,
  `subcategory_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `oop_id` varchar(100) DEFAULT NULL,
  `supplier_id` bigint(20) DEFAULT NULL,
  `product_type` varchar(50) DEFAULT NULL,
  `defaultsaleprice` varchar(255) NOT NULL,
  `ad` varchar(255) NOT NULL DEFAULT '',
  `bd` varchar(255) NOT NULL DEFAULT '',
  `addigit` int(20) NOT NULL DEFAULT 0,
  `batchtype` int(11) NOT NULL,
  `printname` varchar(2000) NOT NULL DEFAULT '',
  `stock` int(20) NOT NULL DEFAULT 1,
  `max_stock_level` decimal(18,2) NOT NULL DEFAULT 0.00,
  `min_stock_level` decimal(18,2) NOT NULL DEFAULT 0.00,
  `reorder_stock_level` decimal(18,2) NOT NULL DEFAULT 0.00,
  `reserve_stock_level` decimal(18,2) NOT NULL DEFAULT 0.00,
  `product_image` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_oop`
--

CREATE TABLE `product_oop` (
  `oop_id` int(11) NOT NULL,
  `oop_name` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_purchase`
--

CREATE TABLE `product_purchase` (
  `id` int(11) NOT NULL,
  `purchase_id` bigint(20) NOT NULL,
  `chalan_no` varchar(100) NOT NULL,
  `supplier_id` bigint(20) NOT NULL,
  `grand_total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(10,2) DEFAULT 0.00,
  `due_amount` decimal(10,2) DEFAULT 0.00,
  `total_discount` decimal(10,2) DEFAULT NULL,
  `invoice_discount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'sum of product discount',
  `total_vat_amnt` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'sum of product vat',
  `purchase_date` varchar(50) DEFAULT NULL,
  `purchase_details` text DEFAULT NULL,
  `status` int(11) NOT NULL,
  `bank_id` varchar(30) DEFAULT NULL,
  `payment_type` int(11) NOT NULL,
  `is_credit` tinyint(4) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_purchase_details`
--

CREATE TABLE `product_purchase_details` (
  `id` int(11) NOT NULL,
  `purchase_detail_id` varchar(100) DEFAULT NULL,
  `purchase_id` bigint(20) DEFAULT NULL,
  `product_id` varchar(30) DEFAULT NULL,
  `quantity` decimal(10,2) DEFAULT NULL,
  `rate` decimal(10,2) DEFAULT NULL,
  `batch_id` varchar(30) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `discount` float DEFAULT NULL COMMENT 'discount percentage',
  `discount_amnt` decimal(10,2) NOT NULL DEFAULT 0.00,
  `vat_amnt_per` decimal(10,2) NOT NULL DEFAULT 0.00,
  `vat_amnt` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_return`
--

CREATE TABLE `product_return` (
  `return_id` varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `product_id` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `invoice_id` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `purchase_id` varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `date_purchase` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `date_return` varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `byy_qty` float NOT NULL,
  `ret_qty` float NOT NULL,
  `customer_id` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `supplier_id` varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `product_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `deduction` float NOT NULL,
  `total_deduct` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_tax` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_ret_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `reason` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `usablity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_service`
--

CREATE TABLE `product_service` (
  `service_id` int(11) NOT NULL,
  `service_code` varchar(50) DEFAULT NULL,
  `service_name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `charge` decimal(10,2) NOT NULL DEFAULT 0.00,
  `service_vat` float DEFAULT NULL,
  `tax0` text DEFAULT NULL,
  `tax1` text DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_subcategory`
--

CREATE TABLE `product_subcategory` (
  `subcategory_id` int(11) NOT NULL,
  `subcategory_name` varchar(255) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase`
--

CREATE TABLE `purchase` (
  `id` int(11) NOT NULL,
  `purchase_id` varbinary(1000) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `chalan_no` varbinary(100) NOT NULL,
  `date` varchar(255) NOT NULL,
  `details` varchar(2000) NOT NULL,
  `discount` varbinary(2000) NOT NULL,
  `total_discount_ammount` varbinary(2000) NOT NULL,
  `total_vat_amnt` varbinary(2000) NOT NULL,
  `grandTotal` varbinary(2000) NOT NULL,
  `total` varbinary(2000) NOT NULL,
  `payment_type` int(11) NOT NULL,
  `createddate` datetime NOT NULL,
  `lastupdateddate` datetime NOT NULL,
  `type2` varbinary(1000) NOT NULL,
  `status` tinyint(1) DEFAULT 0,
  `userid` int(10) NOT NULL,
  `already` int(10) DEFAULT NULL,
  `branch` int(100) NOT NULL,
  `incidenttype` int(100) NOT NULL,
  `invoicetype` varchar(300) NOT NULL,
  `purchase_order_no` int(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_details`
--

CREATE TABLE `purchase_details` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `product` int(11) NOT NULL,
  `store` int(11) NOT NULL,
  `quantity` varbinary(500) NOT NULL,
  `product_rate` varbinary(500) NOT NULL,
  `discount` varbinary(500) NOT NULL,
  `discount_value` varbinary(500) NOT NULL,
  `vat_percent` varbinary(500) NOT NULL,
  `vat_value` varbinary(500) NOT NULL,
  `total_price` varbinary(500) NOT NULL,
  `total_discount` varbinary(500) NOT NULL,
  `all_discount` varbinary(500) NOT NULL,
  `type2` varbinary(500) NOT NULL,
  `conversion_id` int(20) NOT NULL,
  `unit` int(20) NOT NULL,
  `batch` int(12) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order`
--

CREATE TABLE `purchase_order` (
  `id` int(11) NOT NULL,
  `purchase_id` varbinary(1000) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `chalan_no` varbinary(100) NOT NULL,
  `date` varchar(255) NOT NULL,
  `details` varchar(2000) NOT NULL,
  `discount` varbinary(2000) NOT NULL,
  `total_discount_ammount` varbinary(2000) NOT NULL,
  `total_vat_amnt` varbinary(2000) NOT NULL,
  `grandTotal` varbinary(2000) NOT NULL,
  `total` varbinary(2000) NOT NULL,
  `createddate` datetime NOT NULL,
  `lastupdateddate` datetime NOT NULL,
  `type2` varbinary(1000) NOT NULL,
  `status` tinyint(1) DEFAULT 0,
  `userid` int(10) NOT NULL,
  `already` int(10) DEFAULT NULL,
  `branch` int(100) NOT NULL,
  `incidenttype` int(100) NOT NULL,
  `invoicetype` varchar(300) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_details`
--

CREATE TABLE `purchase_order_details` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `product` int(11) NOT NULL,
  `store` int(11) NOT NULL,
  `quantity` varbinary(500) NOT NULL,
  `product_rate` varbinary(500) NOT NULL,
  `discount` varbinary(500) NOT NULL,
  `discount_value` varbinary(500) NOT NULL,
  `vat_percent` varbinary(500) NOT NULL,
  `vat_value` varbinary(500) NOT NULL,
  `total_price` varbinary(500) NOT NULL,
  `total_discount` varbinary(500) NOT NULL,
  `all_discount` varbinary(500) NOT NULL,
  `type2` varbinary(500) NOT NULL,
  `conversion_id` int(20) NOT NULL,
  `unit` int(20) NOT NULL,
  `batch` int(12) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_return`
--

CREATE TABLE `purchase_return` (
  `id` int(11) NOT NULL,
  `purchase_return_id` varbinary(2000) NOT NULL,
  `supplier_id` int(20) NOT NULL,
  `rdate` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL,
  `details` varchar(2000) NOT NULL,
  `discount` varbinary(2000) NOT NULL,
  `total_discount_ammount` varbinary(2000) NOT NULL,
  `total_vat_amnt` varbinary(2000) NOT NULL,
  `grandTotal` varbinary(2000) NOT NULL,
  `total` varbinary(2000) NOT NULL,
  `payment_type` int(11) NOT NULL,
  `createddate` datetime NOT NULL,
  `lastupdateddate` datetime NOT NULL,
  `type2` varbinary(2000) NOT NULL,
  `status` tinyint(1) DEFAULT 0,
  `userid` int(10) NOT NULL,
  `incidenttype` int(100) NOT NULL,
  `already` int(10) NOT NULL,
  `branch` int(100) NOT NULL,
  `invoicetype` varchar(1000) NOT NULL,
  `purchase_id` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_return_details`
--

CREATE TABLE `purchase_return_details` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `product` int(11) NOT NULL,
  `store` int(11) NOT NULL,
  `quantity` varbinary(500) NOT NULL,
  `product_rate` varbinary(500) NOT NULL,
  `discount` varbinary(500) NOT NULL,
  `discount_value` varbinary(500) NOT NULL,
  `vat_percent` varbinary(500) NOT NULL,
  `vat_value` varbinary(500) NOT NULL,
  `rqty` varbinary(500) NOT NULL,
  `rstore` int(11) NOT NULL,
  `rdeduction` varbinary(500) NOT NULL,
  `total_price` varbinary(500) NOT NULL,
  `total_discount` varbinary(500) NOT NULL,
  `all_discount` varbinary(500) NOT NULL,
  `type2` varbinary(1000) NOT NULL,
  `conversion_id` int(20) NOT NULL,
  `unit` int(20) NOT NULL,
  `batch` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_permission`
--

CREATE TABLE `role_permission` (
  `id` int(11) NOT NULL,
  `fk_module_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `create` tinyint(1) DEFAULT NULL,
  `read` tinyint(1) DEFAULT NULL,
  `update` tinyint(1) DEFAULT NULL,
  `delete` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `role_permission`
--

INSERT INTO `role_permission` (`id`, `fk_module_id`, `role_id`, `create`, `read`, `update`, `delete`) VALUES
(1937, 156, 4, 0, 0, 0, 0),
(1938, 157, 4, 0, 0, 0, 0),
(1939, 97, 4, 0, 0, 0, 0),
(1940, 98, 4, 0, 1, 0, 0),
(1941, 10, 4, 0, 0, 0, 0),
(1942, 11, 4, 0, 0, 0, 0),
(1943, 12, 4, 0, 0, 0, 0),
(1944, 13, 4, 0, 0, 0, 0),
(1945, 14, 4, 0, 0, 0, 0),
(1946, 15, 4, 0, 0, 0, 0),
(1947, 16, 4, 0, 1, 0, 0),
(1948, 17, 4, 0, 0, 0, 0),
(1949, 18, 4, 0, 0, 0, 0),
(1950, 4, 4, 1, 1, 1, 1),
(1951, 5, 4, 1, 1, 1, 1),
(1952, 164, 4, 0, 0, 0, 0),
(1953, 165, 4, 0, 0, 0, 0),
(1954, 170, 4, 0, 0, 0, 0),
(1955, 171, 4, 0, 0, 0, 0),
(1956, 172, 4, 0, 0, 0, 0),
(1957, 173, 4, 0, 0, 0, 0),
(1958, 23, 4, 0, 0, 0, 0),
(1959, 32, 4, 1, 1, 1, 1),
(1960, 34, 4, 1, 1, 1, 1),
(1961, 35, 4, 0, 0, 0, 0),
(1962, 36, 4, 0, 0, 0, 0),
(1963, 37, 4, 0, 0, 0, 0),
(1964, 39, 4, 0, 0, 0, 0),
(1965, 174, 4, 1, 1, 1, 1),
(1966, 175, 4, 1, 1, 1, 1),
(1967, 21, 4, 1, 1, 1, 1),
(1968, 22, 4, 1, 1, 1, 1),
(1969, 1, 4, 1, 1, 1, 1),
(1970, 2, 4, 1, 1, 1, 1),
(1971, 99, 4, 1, 1, 1, 1),
(1972, 100, 4, 1, 1, 1, 1),
(1973, 70, 4, 0, 0, 0, 0),
(1974, 71, 4, 0, 0, 0, 0),
(1975, 72, 4, 0, 0, 0, 0),
(1976, 73, 4, 0, 0, 0, 0),
(1977, 65, 4, 0, 0, 0, 0),
(1978, 102, 4, 0, 0, 0, 0),
(1979, 103, 4, 0, 0, 0, 0),
(1980, 104, 4, 0, 0, 0, 0),
(1981, 105, 4, 0, 0, 0, 0),
(1982, 106, 4, 0, 0, 0, 0),
(1983, 107, 4, 0, 0, 0, 0),
(1984, 108, 4, 0, 0, 0, 0),
(1985, 109, 4, 0, 0, 0, 0),
(1986, 110, 4, 0, 0, 0, 0),
(1987, 111, 4, 0, 0, 0, 0),
(1988, 112, 4, 0, 0, 0, 0),
(1989, 113, 4, 0, 0, 0, 0),
(1990, 115, 4, 0, 0, 0, 0),
(1991, 116, 4, 0, 0, 0, 0),
(1992, 117, 4, 0, 0, 0, 0),
(1993, 124, 4, 0, 0, 0, 0),
(1994, 125, 4, 0, 0, 0, 0),
(1995, 126, 4, 0, 0, 0, 0),
(1996, 156, 5, 0, 1, 0, 0),
(1997, 157, 5, 0, 0, 0, 0),
(1998, 97, 5, 0, 0, 0, 0),
(1999, 98, 5, 0, 0, 0, 0),
(2000, 10, 5, 0, 0, 0, 0),
(2001, 11, 5, 0, 1, 0, 0),
(2002, 12, 5, 0, 0, 0, 0),
(2003, 13, 5, 0, 0, 0, 0),
(2004, 14, 5, 0, 0, 0, 0),
(2005, 15, 5, 0, 0, 0, 0),
(2006, 16, 5, 0, 1, 0, 0),
(2007, 17, 5, 0, 0, 0, 0),
(2008, 18, 5, 0, 1, 0, 0),
(2009, 4, 5, 0, 0, 0, 0),
(2010, 5, 5, 0, 1, 0, 0),
(2011, 164, 5, 1, 1, 1, 1),
(2012, 165, 5, 1, 1, 1, 1),
(2013, 170, 5, 1, 1, 1, 1),
(2014, 171, 5, 1, 1, 1, 1),
(2015, 172, 5, 1, 1, 1, 1),
(2016, 173, 5, 1, 1, 1, 1),
(2017, 23, 5, 1, 1, 1, 1),
(2018, 32, 5, 0, 0, 0, 0),
(2019, 34, 5, 0, 0, 0, 0),
(2020, 35, 5, 0, 0, 0, 0),
(2021, 36, 5, 0, 0, 0, 0),
(2022, 37, 5, 0, 0, 0, 0),
(2023, 39, 5, 0, 0, 0, 0),
(2024, 174, 5, 0, 0, 0, 0),
(2025, 175, 5, 1, 1, 1, 1),
(2026, 21, 5, 0, 0, 0, 0),
(2027, 22, 5, 0, 1, 0, 0),
(2028, 1, 5, 0, 0, 0, 0),
(2029, 2, 5, 0, 1, 0, 0),
(2030, 99, 5, 0, 0, 0, 0),
(2031, 100, 5, 0, 0, 0, 0),
(2032, 70, 5, 0, 0, 0, 0),
(2033, 71, 5, 0, 0, 0, 0),
(2034, 72, 5, 0, 0, 0, 0),
(2035, 73, 5, 0, 0, 0, 0),
(2036, 65, 5, 0, 0, 0, 0),
(2037, 102, 5, 0, 0, 0, 0),
(2038, 103, 5, 0, 0, 0, 0),
(2039, 104, 5, 0, 0, 0, 0),
(2040, 105, 5, 0, 0, 0, 0),
(2041, 106, 5, 0, 0, 0, 0),
(2042, 107, 5, 0, 0, 0, 0),
(2043, 108, 5, 0, 0, 0, 0),
(2044, 109, 5, 0, 0, 0, 0),
(2045, 110, 5, 0, 0, 0, 0),
(2046, 111, 5, 0, 0, 0, 0),
(2047, 112, 5, 0, 0, 0, 0),
(2048, 113, 5, 0, 0, 0, 0),
(2049, 115, 5, 0, 0, 0, 0),
(2050, 116, 5, 0, 0, 0, 0),
(2051, 117, 5, 0, 0, 0, 0),
(2052, 124, 5, 0, 0, 0, 0),
(2053, 125, 5, 0, 0, 0, 0),
(2054, 126, 5, 0, 0, 0, 0),
(2055, 156, 6, 0, 1, 0, 0),
(2056, 157, 6, 0, 0, 0, 0),
(2057, 97, 6, 0, 0, 0, 0),
(2058, 98, 6, 0, 1, 0, 0),
(2059, 10, 6, 0, 0, 0, 0),
(2060, 11, 6, 0, 0, 0, 0),
(2061, 12, 6, 0, 0, 0, 0),
(2062, 13, 6, 0, 0, 0, 0),
(2063, 14, 6, 0, 0, 0, 0),
(2064, 15, 6, 0, 0, 0, 0),
(2065, 16, 6, 0, 1, 0, 0),
(2066, 17, 6, 0, 0, 0, 0),
(2067, 18, 6, 0, 1, 0, 0),
(2068, 4, 6, 0, 0, 0, 0),
(2069, 5, 6, 0, 1, 0, 0),
(2070, 164, 6, 0, 0, 0, 0),
(2071, 165, 6, 0, 1, 0, 0),
(2072, 170, 6, 0, 0, 0, 0),
(2073, 171, 6, 0, 1, 0, 0),
(2074, 172, 6, 0, 0, 0, 0),
(2075, 173, 6, 0, 1, 0, 0),
(2076, 23, 6, 1, 1, 1, 1),
(2077, 32, 6, 1, 1, 1, 1),
(2078, 34, 6, 1, 1, 1, 1),
(2079, 35, 6, 1, 1, 1, 1),
(2080, 36, 6, 1, 1, 1, 1),
(2081, 37, 6, 1, 1, 1, 1),
(2082, 39, 6, 1, 1, 1, 1),
(2083, 174, 6, 1, 1, 1, 1),
(2084, 175, 6, 1, 1, 1, 1),
(2085, 21, 6, 0, 0, 0, 0),
(2086, 22, 6, 0, 1, 0, 0),
(2087, 1, 6, 0, 0, 0, 0),
(2088, 2, 6, 0, 1, 0, 0),
(2089, 99, 6, 0, 0, 0, 0),
(2090, 100, 6, 0, 1, 0, 0),
(2091, 70, 6, 0, 0, 0, 0),
(2092, 71, 6, 0, 0, 0, 0),
(2093, 72, 6, 0, 0, 0, 0),
(2094, 73, 6, 0, 1, 0, 0),
(2095, 65, 6, 0, 0, 0, 0),
(2096, 102, 6, 0, 0, 0, 0),
(2097, 103, 6, 0, 0, 0, 0),
(2098, 104, 6, 0, 0, 0, 0),
(2099, 105, 6, 0, 0, 0, 0),
(2100, 106, 6, 0, 0, 0, 0),
(2101, 107, 6, 0, 0, 0, 0),
(2102, 108, 6, 0, 0, 0, 0),
(2103, 109, 6, 0, 0, 0, 0),
(2104, 110, 6, 0, 0, 0, 0),
(2105, 111, 6, 0, 0, 0, 0),
(2106, 112, 6, 0, 0, 0, 0),
(2107, 113, 6, 0, 0, 0, 0),
(2108, 115, 6, 0, 0, 0, 0),
(2109, 116, 6, 0, 0, 0, 0),
(2110, 117, 6, 0, 0, 0, 0),
(2111, 124, 6, 0, 0, 0, 0),
(2112, 125, 6, 0, 0, 0, 0),
(2113, 126, 6, 0, 1, 0, 0),
(2173, 156, 7, 0, 1, 0, 0),
(2174, 157, 7, 0, 0, 0, 0),
(2175, 97, 7, 1, 1, 1, 1),
(2176, 98, 7, 1, 1, 1, 1),
(2177, 10, 7, 1, 1, 1, 1),
(2178, 11, 7, 1, 1, 1, 1),
(2179, 12, 7, 1, 1, 1, 1),
(2180, 13, 7, 1, 1, 1, 1),
(2181, 14, 7, 1, 1, 1, 1),
(2182, 15, 7, 1, 1, 1, 1),
(2183, 16, 7, 1, 1, 1, 1),
(2184, 17, 7, 1, 1, 1, 1),
(2185, 18, 7, 1, 1, 1, 1),
(2186, 4, 7, 1, 1, 1, 1),
(2187, 5, 7, 1, 1, 1, 1),
(2188, 164, 7, 1, 1, 1, 1),
(2189, 165, 7, 1, 1, 1, 1),
(2190, 170, 7, 1, 1, 1, 1),
(2191, 171, 7, 1, 1, 1, 1),
(2192, 172, 7, 1, 1, 1, 1),
(2193, 173, 7, 1, 1, 1, 1),
(2194, 23, 7, 1, 1, 1, 1),
(2195, 32, 7, 1, 1, 1, 1),
(2196, 34, 7, 1, 1, 1, 1),
(2197, 35, 7, 1, 1, 1, 1),
(2198, 36, 7, 1, 1, 1, 1),
(2199, 37, 7, 1, 1, 1, 1),
(2200, 39, 7, 1, 1, 1, 1),
(2201, 174, 7, 1, 1, 1, 1),
(2202, 175, 7, 1, 1, 1, 1),
(2203, 188, 7, 0, 0, 0, 0),
(2204, 189, 7, 0, 0, 0, 0),
(2205, 21, 7, 1, 1, 1, 1),
(2206, 22, 7, 1, 1, 1, 1),
(2207, 1, 7, 1, 1, 1, 1),
(2208, 2, 7, 1, 1, 1, 1),
(2209, 194, 7, 1, 1, 1, 1),
(2210, 99, 7, 1, 1, 1, 1),
(2211, 100, 7, 1, 1, 1, 1),
(2212, 70, 7, 1, 1, 1, 1),
(2213, 71, 7, 1, 1, 1, 1),
(2214, 72, 7, 1, 1, 1, 1),
(2215, 73, 7, 1, 1, 1, 1),
(2216, 65, 7, 0, 0, 0, 0),
(2217, 102, 7, 0, 0, 0, 0),
(2218, 103, 7, 0, 0, 0, 0),
(2219, 104, 7, 0, 0, 0, 0),
(2220, 105, 7, 0, 0, 0, 0),
(2221, 106, 7, 0, 0, 0, 0),
(2222, 107, 7, 0, 0, 0, 0),
(2223, 108, 7, 0, 0, 0, 0),
(2224, 109, 7, 0, 0, 0, 0),
(2225, 110, 7, 0, 0, 0, 0),
(2226, 111, 7, 0, 0, 0, 0),
(2227, 112, 7, 0, 0, 0, 0),
(2228, 113, 7, 0, 0, 0, 0),
(2229, 115, 7, 0, 0, 0, 0),
(2230, 116, 7, 0, 0, 0, 0),
(2231, 117, 7, 0, 0, 0, 0),
(2232, 124, 7, 0, 0, 0, 0),
(2233, 192, 7, 0, 0, 0, 0),
(2234, 193, 7, 0, 0, 0, 0),
(2235, 125, 7, 1, 1, 1, 1),
(2236, 126, 7, 1, 1, 1, 1),
(2237, 190, 7, 0, 0, 0, 0),
(2238, 191, 7, 0, 0, 0, 0),
(4251, 156, 8, 0, 1, 0, 0),
(4252, 157, 8, 0, 0, 0, 0),
(4253, 97, 8, 1, 1, 1, 1),
(4254, 98, 8, 1, 1, 1, 1),
(4255, 10, 8, 1, 1, 1, 1),
(4256, 11, 8, 1, 1, 1, 1),
(4257, 12, 8, 1, 1, 1, 1),
(4258, 13, 8, 1, 1, 1, 1),
(4259, 14, 8, 1, 1, 1, 1),
(4260, 15, 8, 1, 1, 1, 1),
(4261, 16, 8, 1, 1, 1, 1),
(4262, 197, 8, 1, 1, 1, 1),
(4263, 198, 8, 1, 1, 1, 1),
(4264, 199, 8, 1, 1, 1, 1),
(4265, 200, 8, 1, 1, 1, 1),
(4266, 203, 8, 1, 1, 1, 1),
(4267, 204, 8, 1, 1, 1, 1),
(4268, 17, 8, 1, 1, 1, 1),
(4269, 18, 8, 1, 1, 1, 1),
(4270, 4, 8, 1, 1, 1, 1),
(4271, 5, 8, 1, 1, 1, 1),
(4272, 164, 8, 1, 1, 1, 1),
(4273, 165, 8, 1, 1, 1, 1),
(4274, 170, 8, 1, 1, 1, 1),
(4275, 171, 8, 1, 1, 1, 1),
(4276, 172, 8, 1, 1, 1, 1),
(4277, 173, 8, 1, 1, 1, 1),
(4278, 205, 8, 1, 1, 1, 1),
(4279, 206, 8, 1, 1, 1, 1),
(4280, 23, 8, 1, 1, 1, 1),
(4281, 32, 8, 1, 1, 1, 1),
(4282, 34, 8, 1, 1, 1, 1),
(4283, 35, 8, 1, 1, 1, 1),
(4284, 36, 8, 1, 1, 1, 1),
(4285, 37, 8, 1, 1, 1, 1),
(4286, 39, 8, 1, 1, 1, 1),
(4287, 174, 8, 1, 1, 1, 1),
(4288, 175, 8, 1, 1, 1, 1),
(4289, 188, 8, 1, 1, 1, 1),
(4290, 189, 8, 1, 1, 1, 1),
(4291, 21, 8, 1, 1, 1, 1),
(4292, 22, 8, 1, 1, 1, 1),
(4293, 1, 8, 1, 1, 1, 1),
(4294, 2, 8, 1, 1, 1, 1),
(4295, 194, 8, 1, 1, 1, 1),
(4296, 195, 8, 1, 1, 1, 1),
(4297, 196, 8, 1, 1, 1, 1),
(4298, 99, 8, 1, 1, 1, 1),
(4299, 100, 8, 1, 1, 1, 1),
(4300, 70, 8, 1, 1, 1, 1),
(4301, 71, 8, 1, 1, 1, 1),
(4302, 72, 8, 1, 1, 1, 1),
(4303, 73, 8, 1, 1, 1, 1),
(4304, 65, 8, 1, 1, 1, 1),
(4305, 102, 8, 1, 1, 1, 1),
(4306, 103, 8, 1, 1, 1, 1),
(4307, 104, 8, 1, 1, 1, 1),
(4308, 105, 8, 0, 0, 0, 0),
(4309, 106, 8, 0, 0, 0, 0),
(4310, 107, 8, 1, 1, 1, 1),
(4311, 108, 8, 0, 0, 0, 0),
(4312, 109, 8, 0, 0, 0, 0),
(4313, 110, 8, 1, 1, 1, 1),
(4314, 111, 8, 1, 1, 1, 1),
(4315, 112, 8, 1, 1, 1, 1),
(4316, 113, 8, 0, 0, 0, 0),
(4317, 115, 8, 1, 1, 1, 1),
(4318, 116, 8, 0, 0, 0, 0),
(4319, 117, 8, 0, 0, 0, 0),
(4320, 124, 8, 1, 1, 1, 1),
(4321, 192, 8, 1, 1, 1, 1),
(4322, 193, 8, 1, 1, 1, 1),
(4323, 125, 8, 1, 1, 1, 1),
(4324, 126, 8, 1, 1, 1, 1),
(4325, 190, 8, 0, 0, 0, 0),
(4326, 191, 8, 0, 1, 0, 0),
(4327, 207, 8, 0, 0, 0, 0),
(4328, 156, 18, 0, 1, 0, 0),
(4329, 157, 18, 0, 0, 0, 0),
(4330, 97, 18, 0, 0, 0, 0),
(4331, 98, 18, 0, 1, 0, 0),
(4332, 10, 18, 0, 0, 0, 0),
(4333, 11, 18, 0, 1, 0, 0),
(4334, 12, 18, 0, 0, 0, 0),
(4335, 13, 18, 0, 1, 0, 0),
(4336, 14, 18, 0, 0, 0, 0),
(4337, 15, 18, 0, 0, 0, 0),
(4338, 16, 18, 0, 1, 0, 0),
(4339, 197, 18, 0, 0, 0, 0),
(4340, 198, 18, 0, 1, 0, 0),
(4341, 199, 18, 0, 0, 0, 0),
(4342, 200, 18, 0, 1, 0, 0),
(4343, 203, 18, 0, 0, 0, 0),
(4344, 204, 18, 0, 1, 0, 0),
(4345, 17, 18, 1, 1, 1, 1),
(4346, 18, 18, 1, 1, 1, 1),
(4347, 4, 18, 1, 1, 1, 1),
(4348, 5, 18, 1, 1, 1, 1),
(4349, 164, 18, 0, 0, 0, 0),
(4350, 165, 18, 0, 0, 0, 0),
(4351, 170, 18, 0, 0, 0, 0),
(4352, 171, 18, 0, 0, 0, 0),
(4353, 172, 18, 0, 0, 0, 0),
(4354, 173, 18, 0, 0, 0, 0),
(4355, 205, 18, 1, 1, 1, 1),
(4356, 206, 18, 1, 1, 1, 1),
(4357, 23, 18, 0, 0, 0, 0),
(4358, 32, 18, 1, 1, 1, 1),
(4359, 34, 18, 1, 1, 1, 1),
(4360, 35, 18, 1, 1, 1, 1),
(4361, 36, 18, 1, 1, 1, 1),
(4362, 37, 18, 1, 1, 1, 1),
(4363, 39, 18, 1, 1, 1, 1),
(4364, 174, 18, 1, 1, 1, 1),
(4365, 175, 18, 1, 1, 1, 1),
(4366, 188, 18, 1, 1, 1, 1),
(4367, 189, 18, 0, 0, 0, 0),
(4368, 21, 18, 1, 1, 1, 1),
(4369, 22, 18, 1, 1, 1, 1),
(4370, 1, 18, 1, 1, 1, 1),
(4371, 2, 18, 1, 1, 1, 1),
(4372, 194, 18, 1, 1, 1, 1),
(4373, 195, 18, 1, 1, 1, 1),
(4374, 196, 18, 1, 1, 1, 1),
(4375, 99, 18, 1, 1, 1, 1),
(4376, 100, 18, 1, 1, 1, 1),
(4377, 70, 18, 0, 0, 0, 0),
(4378, 71, 18, 0, 0, 0, 0),
(4379, 72, 18, 0, 0, 0, 0),
(4380, 73, 18, 0, 0, 0, 0),
(4381, 65, 18, 0, 0, 0, 0),
(4382, 102, 18, 0, 0, 0, 0),
(4383, 103, 18, 0, 0, 0, 0),
(4384, 104, 18, 0, 0, 0, 0),
(4385, 105, 18, 0, 0, 0, 0),
(4386, 106, 18, 0, 0, 0, 0),
(4387, 107, 18, 0, 0, 0, 0),
(4388, 108, 18, 0, 0, 0, 0),
(4389, 109, 18, 0, 0, 0, 0),
(4390, 110, 18, 0, 0, 0, 0),
(4391, 111, 18, 0, 0, 0, 0),
(4392, 112, 18, 0, 0, 0, 0),
(4393, 113, 18, 0, 0, 0, 0),
(4394, 115, 18, 0, 0, 0, 0),
(4395, 116, 18, 0, 0, 0, 0),
(4396, 117, 18, 0, 0, 0, 0),
(4397, 124, 18, 0, 0, 0, 0),
(4398, 192, 18, 0, 0, 0, 0),
(4399, 193, 18, 0, 0, 0, 0),
(4400, 125, 18, 0, 0, 0, 0),
(4401, 126, 18, 0, 1, 0, 0),
(4402, 190, 18, 0, 0, 0, 0),
(4403, 191, 18, 0, 1, 0, 0),
(4404, 207, 18, 0, 0, 0, 0),
(4405, 156, 19, 0, 1, 0, 0),
(4406, 157, 19, 0, 0, 0, 0),
(4407, 97, 19, 0, 0, 0, 0),
(4408, 98, 19, 0, 0, 0, 0),
(4409, 10, 19, 0, 0, 0, 0),
(4410, 11, 19, 0, 1, 0, 0),
(4411, 12, 19, 0, 0, 0, 0),
(4412, 13, 19, 0, 1, 0, 0),
(4413, 14, 19, 0, 0, 0, 0),
(4414, 15, 19, 0, 0, 0, 0),
(4415, 16, 19, 0, 1, 0, 0),
(4416, 197, 19, 0, 0, 0, 0),
(4417, 198, 19, 0, 1, 0, 0),
(4418, 199, 19, 0, 0, 0, 0),
(4419, 200, 19, 0, 1, 0, 0),
(4420, 203, 19, 0, 0, 0, 0),
(4421, 204, 19, 0, 1, 0, 0),
(4422, 17, 19, 0, 0, 0, 0),
(4423, 18, 19, 0, 1, 0, 0),
(4424, 4, 19, 0, 0, 0, 0),
(4425, 5, 19, 0, 1, 0, 0),
(4426, 164, 19, 1, 1, 1, 1),
(4427, 165, 19, 1, 1, 1, 1),
(4428, 170, 19, 1, 1, 1, 1),
(4429, 171, 19, 1, 1, 1, 1),
(4430, 172, 19, 1, 1, 1, 1),
(4431, 173, 19, 1, 1, 1, 1),
(4432, 205, 19, 1, 1, 1, 1),
(4433, 206, 19, 1, 1, 1, 1),
(4434, 23, 19, 1, 1, 1, 1),
(4435, 32, 19, 0, 0, 0, 0),
(4436, 34, 19, 0, 0, 0, 0),
(4437, 35, 19, 0, 0, 0, 0),
(4438, 36, 19, 0, 0, 0, 0),
(4439, 37, 19, 0, 0, 0, 0),
(4440, 39, 19, 0, 0, 0, 0),
(4441, 174, 19, 0, 0, 0, 0),
(4442, 175, 19, 1, 1, 1, 1),
(4443, 188, 19, 0, 0, 0, 0),
(4444, 189, 19, 0, 0, 0, 0),
(4445, 21, 19, 0, 0, 0, 0),
(4446, 22, 19, 0, 1, 0, 0),
(4447, 1, 19, 0, 0, 0, 0),
(4448, 2, 19, 0, 1, 0, 0),
(4449, 194, 19, 0, 0, 0, 0),
(4450, 195, 19, 0, 0, 0, 0),
(4451, 196, 19, 0, 0, 0, 0),
(4452, 99, 19, 0, 0, 0, 0),
(4453, 100, 19, 0, 0, 0, 0),
(4454, 70, 19, 0, 0, 0, 0),
(4455, 71, 19, 0, 0, 0, 0),
(4456, 72, 19, 0, 0, 0, 0),
(4457, 73, 19, 0, 0, 0, 0),
(4458, 65, 19, 0, 0, 0, 0),
(4459, 102, 19, 0, 0, 0, 0),
(4460, 103, 19, 0, 0, 0, 0),
(4461, 104, 19, 0, 0, 0, 0),
(4462, 105, 19, 0, 0, 0, 0),
(4463, 106, 19, 0, 0, 0, 0),
(4464, 107, 19, 0, 0, 0, 0),
(4465, 108, 19, 0, 0, 0, 0),
(4466, 109, 19, 0, 0, 0, 0),
(4467, 110, 19, 0, 0, 0, 0),
(4468, 111, 19, 0, 0, 0, 0),
(4469, 112, 19, 0, 0, 0, 0),
(4470, 113, 19, 0, 0, 0, 0),
(4471, 115, 19, 0, 0, 0, 0),
(4472, 116, 19, 0, 0, 0, 0),
(4473, 117, 19, 0, 0, 0, 0),
(4474, 124, 19, 0, 0, 0, 0),
(4475, 192, 19, 0, 0, 0, 0),
(4476, 193, 19, 0, 0, 0, 0),
(4477, 125, 19, 0, 0, 0, 0),
(4478, 126, 19, 0, 0, 0, 0),
(4479, 190, 19, 0, 0, 0, 0),
(4480, 191, 19, 0, 1, 0, 0),
(4481, 207, 19, 0, 0, 0, 0),
(4482, 156, 20, 0, 1, 0, 0),
(4483, 157, 20, 0, 0, 0, 0),
(4484, 97, 20, 0, 0, 0, 0),
(4485, 98, 20, 0, 1, 0, 0),
(4486, 10, 20, 0, 0, 0, 0),
(4487, 11, 20, 0, 1, 0, 0),
(4488, 12, 20, 0, 0, 0, 0),
(4489, 13, 20, 0, 1, 0, 0),
(4490, 14, 20, 0, 0, 0, 0),
(4491, 15, 20, 0, 0, 0, 0),
(4492, 16, 20, 0, 1, 0, 0),
(4493, 197, 20, 0, 0, 0, 0),
(4494, 198, 20, 0, 1, 0, 0),
(4495, 199, 20, 0, 0, 0, 0),
(4496, 200, 20, 0, 1, 0, 0),
(4497, 203, 20, 0, 0, 0, 0),
(4498, 204, 20, 0, 1, 0, 0),
(4499, 17, 20, 0, 0, 0, 0),
(4500, 18, 20, 0, 1, 0, 0),
(4501, 4, 20, 0, 0, 0, 0),
(4502, 5, 20, 0, 1, 0, 0),
(4503, 164, 20, 0, 0, 0, 0),
(4504, 165, 20, 0, 1, 0, 0),
(4505, 170, 20, 0, 0, 0, 0),
(4506, 171, 20, 0, 1, 0, 0),
(4507, 172, 20, 0, 0, 0, 0),
(4508, 173, 20, 0, 1, 0, 0),
(4509, 205, 20, 0, 0, 0, 0),
(4510, 206, 20, 0, 0, 0, 0),
(4511, 23, 20, 1, 1, 1, 1),
(4512, 32, 20, 1, 1, 1, 1),
(4513, 34, 20, 1, 1, 1, 1),
(4514, 35, 20, 1, 1, 1, 1),
(4515, 36, 20, 1, 1, 1, 1),
(4516, 37, 20, 1, 1, 1, 1),
(4517, 39, 20, 1, 1, 1, 1),
(4518, 174, 20, 1, 1, 1, 1),
(4519, 175, 20, 1, 1, 1, 1),
(4520, 188, 20, 1, 1, 1, 1),
(4521, 189, 20, 1, 1, 1, 1),
(4522, 21, 20, 0, 0, 0, 0),
(4523, 22, 20, 0, 1, 0, 0),
(4524, 1, 20, 0, 0, 0, 0),
(4525, 2, 20, 0, 1, 0, 0),
(4526, 194, 20, 0, 0, 0, 0),
(4527, 195, 20, 0, 0, 0, 0),
(4528, 196, 20, 0, 0, 0, 0),
(4529, 99, 20, 0, 0, 0, 0),
(4530, 100, 20, 0, 1, 0, 0),
(4531, 70, 20, 0, 0, 0, 0),
(4532, 71, 20, 0, 1, 0, 0),
(4533, 72, 20, 0, 0, 0, 0),
(4534, 73, 20, 0, 1, 0, 0),
(4535, 65, 20, 0, 0, 0, 0),
(4536, 102, 20, 0, 0, 0, 0),
(4537, 103, 20, 0, 0, 0, 0),
(4538, 104, 20, 0, 0, 0, 0),
(4539, 105, 20, 0, 0, 0, 0),
(4540, 106, 20, 0, 0, 0, 0),
(4541, 107, 20, 0, 0, 0, 0),
(4542, 108, 20, 0, 0, 0, 0),
(4543, 109, 20, 0, 0, 0, 0),
(4544, 110, 20, 0, 0, 0, 0),
(4545, 111, 20, 0, 0, 0, 0),
(4546, 112, 20, 0, 0, 0, 0),
(4547, 113, 20, 0, 0, 0, 0),
(4548, 115, 20, 0, 0, 0, 0),
(4549, 116, 20, 0, 0, 0, 0),
(4550, 117, 20, 0, 0, 0, 0),
(4551, 124, 20, 0, 0, 0, 0),
(4552, 192, 20, 0, 0, 0, 0),
(4553, 193, 20, 0, 0, 0, 0),
(4554, 125, 20, 0, 0, 0, 0),
(4555, 126, 20, 0, 1, 0, 0),
(4556, 190, 20, 0, 0, 0, 0),
(4557, 191, 20, 0, 1, 0, 0),
(4558, 207, 20, 0, 0, 0, 0),
(4559, 156, 21, 0, 1, 0, 0),
(4560, 157, 21, 0, 0, 0, 0),
(4561, 97, 21, 1, 1, 1, 1),
(4562, 98, 21, 1, 1, 1, 1),
(4563, 10, 21, 1, 1, 1, 1),
(4564, 11, 21, 1, 1, 1, 1),
(4565, 12, 21, 1, 1, 1, 1),
(4566, 13, 21, 1, 1, 1, 1),
(4567, 14, 21, 1, 1, 1, 1),
(4568, 15, 21, 1, 1, 1, 1),
(4569, 16, 21, 1, 1, 1, 1),
(4570, 197, 21, 1, 1, 1, 1),
(4571, 198, 21, 1, 1, 1, 1),
(4572, 199, 21, 1, 1, 1, 1),
(4573, 200, 21, 1, 1, 1, 1),
(4574, 203, 21, 1, 1, 1, 1),
(4575, 204, 21, 1, 1, 1, 1),
(4576, 17, 21, 1, 1, 1, 1),
(4577, 18, 21, 1, 1, 1, 1),
(4578, 4, 21, 1, 1, 1, 1),
(4579, 5, 21, 1, 1, 1, 1),
(4580, 164, 21, 1, 1, 1, 1),
(4581, 165, 21, 1, 1, 1, 1),
(4582, 170, 21, 1, 1, 1, 1),
(4583, 171, 21, 1, 1, 1, 1),
(4584, 172, 21, 1, 1, 1, 1),
(4585, 173, 21, 1, 1, 1, 1),
(4586, 205, 21, 1, 1, 1, 1),
(4587, 206, 21, 1, 1, 1, 1),
(4588, 23, 21, 1, 1, 1, 1),
(4589, 32, 21, 1, 1, 1, 1),
(4590, 34, 21, 1, 1, 1, 1),
(4591, 35, 21, 1, 1, 1, 1),
(4592, 36, 21, 1, 1, 1, 1),
(4593, 37, 21, 1, 1, 1, 1),
(4594, 39, 21, 1, 1, 1, 1),
(4595, 174, 21, 1, 1, 1, 1),
(4596, 175, 21, 1, 1, 1, 1),
(4597, 188, 21, 1, 1, 1, 1),
(4598, 189, 21, 1, 1, 1, 1),
(4599, 21, 21, 1, 1, 1, 1),
(4600, 22, 21, 1, 1, 1, 1),
(4601, 1, 21, 1, 1, 1, 1),
(4602, 2, 21, 1, 1, 1, 1),
(4603, 194, 21, 1, 1, 1, 1),
(4604, 195, 21, 1, 1, 1, 1),
(4605, 196, 21, 1, 1, 1, 1),
(4606, 99, 21, 0, 0, 0, 0),
(4607, 100, 21, 0, 0, 0, 0),
(4608, 70, 21, 1, 1, 1, 1),
(4609, 71, 21, 1, 1, 1, 1),
(4610, 72, 21, 1, 1, 1, 1),
(4611, 73, 21, 1, 1, 1, 1),
(4612, 65, 21, 0, 0, 0, 0),
(4613, 102, 21, 0, 1, 0, 0),
(4614, 103, 21, 0, 0, 0, 0),
(4615, 104, 21, 0, 0, 0, 0),
(4616, 105, 21, 0, 0, 0, 0),
(4617, 106, 21, 0, 0, 0, 0),
(4618, 107, 21, 0, 0, 0, 0),
(4619, 108, 21, 0, 0, 0, 0),
(4620, 109, 21, 0, 0, 0, 0),
(4621, 110, 21, 0, 0, 0, 0),
(4622, 111, 21, 0, 0, 0, 0),
(4623, 112, 21, 0, 0, 0, 0),
(4624, 113, 21, 0, 0, 0, 0),
(4625, 115, 21, 0, 0, 0, 0),
(4626, 116, 21, 0, 0, 0, 0),
(4627, 117, 21, 0, 0, 0, 0),
(4628, 124, 21, 0, 0, 0, 0),
(4629, 192, 21, 0, 0, 0, 0),
(4630, 193, 21, 0, 0, 0, 0),
(4631, 125, 21, 1, 1, 1, 1),
(4632, 126, 21, 1, 1, 1, 1),
(4633, 190, 21, 0, 0, 0, 0),
(4634, 191, 21, 0, 1, 0, 0),
(4635, 207, 21, 0, 0, 0, 0),
(7319, 156, 29, 0, 1, 0, 0),
(7320, 157, 29, 0, 0, 0, 0),
(7321, 97, 29, 0, 0, 0, 0),
(7322, 98, 29, 0, 0, 0, 0),
(7323, 10, 29, 0, 0, 0, 0),
(7324, 11, 29, 0, 1, 0, 0),
(7325, 12, 29, 0, 0, 0, 0),
(7326, 13, 29, 0, 1, 0, 0),
(7327, 14, 29, 0, 0, 0, 0),
(7328, 16, 29, 0, 1, 0, 0),
(7329, 197, 29, 0, 0, 0, 0),
(7330, 198, 29, 0, 1, 0, 0),
(7331, 203, 29, 0, 0, 0, 0),
(7332, 204, 29, 0, 1, 0, 0),
(7333, 217, 29, 0, 0, 0, 0),
(7334, 218, 29, 0, 1, 0, 0),
(7335, 231, 29, 1, 1, 1, 1),
(7336, 17, 29, 0, 0, 0, 0),
(7337, 18, 29, 0, 1, 0, 0),
(7338, 4, 29, 0, 0, 0, 0),
(7339, 5, 29, 0, 1, 0, 0),
(7340, 205, 29, 1, 1, 1, 1),
(7341, 206, 29, 1, 1, 1, 1),
(7342, 254, 29, 1, 1, 1, 1),
(7343, 255, 29, 1, 1, 1, 1),
(7344, 23, 29, 1, 1, 1, 1),
(7345, 32, 29, 0, 0, 0, 0),
(7346, 34, 29, 0, 0, 0, 0),
(7347, 35, 29, 0, 0, 0, 0),
(7348, 36, 29, 0, 0, 0, 0),
(7349, 37, 29, 0, 0, 0, 0),
(7350, 39, 29, 0, 0, 0, 0),
(7351, 174, 29, 0, 0, 0, 0),
(7352, 175, 29, 1, 1, 1, 1),
(7353, 188, 29, 0, 0, 0, 0),
(7354, 189, 29, 0, 0, 0, 0),
(7355, 219, 29, 0, 0, 0, 0),
(7356, 220, 29, 0, 0, 0, 0),
(7357, 221, 29, 0, 0, 0, 0),
(7358, 222, 29, 0, 0, 0, 0),
(7359, 223, 29, 1, 1, 1, 1),
(7360, 224, 29, 1, 1, 1, 1),
(7361, 225, 29, 0, 0, 0, 0),
(7362, 226, 29, 0, 0, 0, 0),
(7363, 229, 29, 0, 0, 0, 0),
(7364, 230, 29, 0, 0, 0, 0),
(7365, 232, 29, 1, 1, 1, 1),
(7366, 233, 29, 0, 0, 0, 0),
(7367, 249, 29, 0, 0, 0, 0),
(7368, 250, 29, 0, 0, 0, 0),
(7369, 251, 29, 0, 0, 0, 0),
(7370, 21, 29, 0, 0, 0, 0),
(7371, 22, 29, 0, 1, 0, 0),
(7372, 212, 29, 0, 0, 0, 0),
(7373, 213, 29, 0, 0, 0, 0),
(7374, 214, 29, 0, 0, 0, 0),
(7375, 215, 29, 0, 1, 0, 0),
(7376, 1, 29, 0, 0, 0, 0),
(7377, 2, 29, 0, 1, 0, 0),
(7378, 194, 29, 0, 0, 0, 0),
(7379, 195, 29, 0, 0, 0, 0),
(7380, 196, 29, 0, 0, 0, 0),
(7381, 208, 29, 0, 0, 0, 0),
(7382, 209, 29, 0, 1, 0, 0),
(7383, 210, 29, 0, 0, 0, 0),
(7384, 211, 29, 0, 1, 0, 0),
(7385, 257, 29, 0, 0, 0, 0),
(7386, 99, 29, 0, 0, 0, 0),
(7387, 100, 29, 0, 0, 0, 0),
(7388, 227, 29, 0, 0, 0, 0),
(7389, 228, 29, 0, 0, 0, 0),
(7390, 70, 29, 0, 0, 0, 0),
(7391, 71, 29, 0, 0, 0, 0),
(7392, 72, 29, 0, 0, 0, 0),
(7393, 73, 29, 0, 0, 0, 0),
(7394, 65, 29, 0, 0, 0, 0),
(7395, 102, 29, 0, 0, 0, 0),
(7396, 103, 29, 0, 0, 0, 0),
(7397, 104, 29, 0, 0, 0, 0),
(7398, 105, 29, 0, 0, 0, 0),
(7399, 106, 29, 0, 0, 0, 0),
(7400, 107, 29, 0, 0, 0, 0),
(7401, 108, 29, 0, 0, 0, 0),
(7402, 109, 29, 0, 0, 0, 0),
(7403, 110, 29, 0, 0, 0, 0),
(7404, 111, 29, 0, 0, 0, 0),
(7405, 112, 29, 0, 0, 0, 0),
(7406, 113, 29, 0, 0, 0, 0),
(7407, 115, 29, 0, 0, 0, 0),
(7408, 116, 29, 0, 0, 0, 0),
(7409, 117, 29, 0, 0, 0, 0),
(7410, 124, 29, 0, 0, 0, 0),
(7411, 192, 29, 0, 0, 0, 0),
(7412, 193, 29, 0, 0, 0, 0),
(7413, 239, 29, 0, 0, 0, 0),
(7414, 125, 29, 0, 0, 0, 0),
(7415, 126, 29, 0, 0, 0, 0),
(7416, 190, 29, 0, 0, 0, 0),
(7417, 191, 29, 0, 1, 0, 0),
(7418, 256, 29, 0, 0, 0, 0),
(7419, 170, 29, 1, 1, 1, 1),
(7420, 171, 29, 1, 1, 1, 1),
(7421, 172, 29, 1, 1, 1, 1),
(7422, 173, 29, 1, 1, 1, 1),
(7423, 241, 29, 0, 0, 0, 0),
(7424, 242, 29, 0, 0, 0, 0),
(7425, 243, 29, 0, 0, 0, 0),
(7426, 244, 29, 0, 0, 0, 0),
(7427, 245, 29, 0, 0, 0, 0),
(7428, 246, 29, 0, 0, 0, 0),
(7429, 247, 29, 0, 0, 0, 0),
(7430, 248, 29, 0, 0, 0, 0),
(7431, 252, 29, 1, 1, 1, 1),
(7432, 253, 29, 1, 1, 1, 1),
(7433, 156, 30, 0, 1, 0, 0),
(7434, 157, 30, 0, 0, 0, 0),
(7435, 97, 30, 0, 0, 0, 0),
(7436, 98, 30, 0, 1, 0, 0),
(7437, 10, 30, 0, 0, 0, 0),
(7438, 11, 30, 0, 1, 0, 0),
(7439, 12, 30, 0, 0, 0, 0),
(7440, 13, 30, 0, 1, 0, 0),
(7441, 14, 30, 0, 0, 0, 0),
(7442, 16, 30, 0, 1, 0, 0),
(7443, 197, 30, 0, 0, 0, 0),
(7444, 198, 30, 0, 1, 0, 0),
(7445, 203, 30, 0, 0, 0, 0),
(7446, 204, 30, 0, 1, 0, 0),
(7447, 217, 30, 0, 0, 0, 0),
(7448, 218, 30, 0, 1, 0, 0),
(7449, 231, 30, 0, 0, 0, 0),
(7450, 17, 30, 0, 0, 0, 0),
(7451, 18, 30, 0, 1, 0, 0),
(7452, 4, 30, 0, 0, 0, 0),
(7453, 5, 30, 0, 1, 0, 0),
(7454, 205, 30, 0, 0, 0, 0),
(7455, 206, 30, 0, 1, 0, 0),
(7456, 254, 30, 0, 0, 0, 0),
(7457, 255, 30, 0, 1, 0, 0),
(7458, 23, 30, 1, 1, 1, 1),
(7459, 32, 30, 1, 1, 1, 1),
(7460, 34, 30, 1, 1, 1, 1),
(7461, 35, 30, 1, 1, 1, 1),
(7462, 36, 30, 1, 1, 1, 1),
(7463, 37, 30, 1, 1, 1, 1),
(7464, 39, 30, 1, 1, 1, 1),
(7465, 174, 30, 1, 1, 1, 1),
(7466, 175, 30, 1, 1, 1, 1),
(7467, 188, 30, 1, 1, 1, 1),
(7468, 189, 30, 1, 1, 1, 1),
(7469, 219, 30, 1, 1, 1, 1),
(7470, 220, 30, 1, 1, 1, 1),
(7471, 221, 30, 1, 1, 1, 1),
(7472, 222, 30, 1, 1, 1, 1),
(7473, 223, 30, 1, 1, 1, 1),
(7474, 224, 30, 1, 1, 1, 1),
(7475, 225, 30, 1, 1, 1, 1),
(7476, 226, 30, 1, 1, 1, 1),
(7477, 229, 30, 1, 1, 1, 1),
(7478, 230, 30, 1, 1, 1, 1),
(7479, 232, 30, 1, 1, 1, 1),
(7480, 233, 30, 1, 1, 1, 1),
(7481, 249, 30, 1, 1, 1, 1),
(7482, 250, 30, 1, 1, 1, 1),
(7483, 251, 30, 1, 1, 1, 1),
(7484, 21, 30, 0, 0, 0, 0),
(7485, 22, 30, 0, 1, 0, 0),
(7486, 212, 30, 0, 0, 0, 0),
(7487, 213, 30, 0, 1, 0, 0),
(7488, 214, 30, 0, 0, 0, 0),
(7489, 215, 30, 0, 1, 0, 0),
(7490, 1, 30, 0, 0, 0, 0),
(7491, 2, 30, 0, 1, 0, 0),
(7492, 194, 30, 0, 0, 0, 0),
(7493, 195, 30, 0, 0, 0, 0),
(7494, 196, 30, 0, 1, 0, 0),
(7495, 208, 30, 0, 0, 0, 0),
(7496, 209, 30, 0, 1, 0, 0),
(7497, 210, 30, 0, 0, 0, 0),
(7498, 211, 30, 0, 1, 0, 0),
(7499, 257, 30, 0, 0, 0, 0),
(7500, 99, 30, 0, 0, 0, 0),
(7501, 100, 30, 0, 1, 0, 0),
(7502, 227, 30, 0, 0, 0, 0),
(7503, 228, 30, 0, 1, 0, 0),
(7504, 70, 30, 0, 0, 0, 0),
(7505, 71, 30, 0, 1, 0, 0),
(7506, 72, 30, 0, 0, 0, 0),
(7507, 73, 30, 0, 1, 0, 0),
(7508, 65, 30, 0, 0, 0, 0),
(7509, 102, 30, 0, 0, 0, 0),
(7510, 103, 30, 0, 0, 0, 0),
(7511, 104, 30, 0, 0, 0, 0),
(7512, 105, 30, 0, 0, 0, 0),
(7513, 106, 30, 0, 0, 0, 0),
(7514, 107, 30, 0, 0, 0, 0),
(7515, 108, 30, 0, 0, 0, 0),
(7516, 109, 30, 0, 0, 0, 0),
(7517, 110, 30, 0, 0, 0, 0),
(7518, 111, 30, 0, 0, 0, 0),
(7519, 112, 30, 0, 0, 0, 0),
(7520, 113, 30, 0, 0, 0, 0),
(7521, 115, 30, 0, 0, 0, 0),
(7522, 116, 30, 0, 0, 0, 0),
(7523, 117, 30, 0, 0, 0, 0),
(7524, 124, 30, 0, 0, 0, 0),
(7525, 192, 30, 0, 0, 0, 0),
(7526, 193, 30, 0, 0, 0, 0),
(7527, 239, 30, 0, 0, 0, 0),
(7528, 125, 30, 0, 0, 0, 0),
(7529, 126, 30, 0, 1, 0, 0),
(7530, 190, 30, 0, 0, 0, 0),
(7531, 191, 30, 0, 1, 0, 0),
(7532, 256, 30, 0, 0, 0, 0),
(7533, 170, 30, 0, 0, 0, 0),
(7534, 171, 30, 0, 1, 0, 0),
(7535, 172, 30, 0, 0, 0, 0),
(7536, 173, 30, 0, 1, 0, 0),
(7537, 241, 30, 1, 1, 1, 1),
(7538, 242, 30, 1, 1, 1, 1),
(7539, 243, 30, 1, 1, 1, 1),
(7540, 244, 30, 1, 1, 1, 1),
(7541, 245, 30, 1, 1, 1, 1),
(7542, 246, 30, 1, 1, 1, 1),
(7543, 247, 30, 1, 1, 1, 1),
(7544, 248, 30, 1, 1, 1, 1),
(7545, 252, 30, 1, 1, 1, 1),
(7546, 253, 30, 1, 1, 1, 1),
(7547, 156, 31, 0, 1, 0, 0),
(7548, 157, 31, 0, 0, 0, 0),
(7549, 97, 31, 1, 1, 1, 1),
(7550, 98, 31, 1, 1, 1, 1),
(7551, 10, 31, 1, 1, 1, 1),
(7552, 11, 31, 1, 1, 1, 1),
(7553, 12, 31, 1, 1, 1, 1),
(7554, 13, 31, 1, 1, 1, 1),
(7555, 14, 31, 1, 1, 1, 1),
(7556, 16, 31, 1, 1, 1, 1),
(7557, 197, 31, 1, 1, 1, 1),
(7558, 198, 31, 1, 1, 1, 1),
(7559, 203, 31, 1, 1, 1, 1),
(7560, 204, 31, 1, 1, 1, 1),
(7561, 217, 31, 1, 1, 1, 1),
(7562, 218, 31, 1, 1, 1, 1),
(7563, 231, 31, 1, 1, 1, 1),
(7564, 17, 31, 1, 1, 1, 1),
(7565, 18, 31, 1, 1, 1, 1),
(7566, 4, 31, 1, 1, 1, 1),
(7567, 5, 31, 1, 1, 1, 1),
(7568, 205, 31, 1, 1, 1, 1),
(7569, 206, 31, 1, 1, 1, 1),
(7570, 254, 31, 1, 1, 1, 1),
(7571, 255, 31, 1, 1, 1, 1),
(7572, 23, 31, 1, 1, 1, 1),
(7573, 32, 31, 1, 1, 1, 1),
(7574, 34, 31, 1, 1, 1, 1),
(7575, 35, 31, 1, 1, 1, 1),
(7576, 36, 31, 1, 1, 1, 1),
(7577, 37, 31, 1, 1, 1, 1),
(7578, 39, 31, 1, 1, 1, 1),
(7579, 174, 31, 1, 1, 1, 1),
(7580, 175, 31, 1, 1, 1, 1),
(7581, 188, 31, 1, 1, 1, 1),
(7582, 189, 31, 1, 1, 1, 1),
(7583, 219, 31, 1, 1, 1, 1),
(7584, 220, 31, 1, 1, 1, 1),
(7585, 221, 31, 1, 1, 1, 1),
(7586, 222, 31, 1, 1, 1, 1),
(7587, 223, 31, 1, 1, 1, 1),
(7588, 224, 31, 1, 1, 1, 1),
(7589, 225, 31, 1, 1, 1, 1),
(7590, 226, 31, 1, 1, 1, 1),
(7591, 229, 31, 1, 1, 1, 1),
(7592, 230, 31, 1, 1, 1, 1),
(7593, 232, 31, 1, 1, 1, 1),
(7594, 233, 31, 1, 1, 1, 1),
(7595, 249, 31, 1, 1, 1, 1),
(7596, 250, 31, 1, 1, 1, 1),
(7597, 251, 31, 1, 1, 1, 1),
(7598, 21, 31, 1, 1, 1, 1),
(7599, 22, 31, 1, 1, 1, 1),
(7600, 212, 31, 1, 1, 1, 1),
(7601, 213, 31, 1, 1, 1, 1),
(7602, 214, 31, 1, 1, 1, 1),
(7603, 215, 31, 1, 1, 1, 1),
(7604, 1, 31, 1, 1, 1, 1),
(7605, 2, 31, 1, 1, 1, 1),
(7606, 194, 31, 1, 1, 1, 1),
(7607, 195, 31, 1, 1, 1, 1),
(7608, 196, 31, 1, 1, 1, 1),
(7609, 208, 31, 1, 1, 1, 1),
(7610, 209, 31, 1, 1, 1, 1),
(7611, 210, 31, 1, 1, 1, 1),
(7612, 211, 31, 1, 1, 1, 1),
(7613, 257, 31, 1, 1, 1, 1),
(7614, 99, 31, 1, 1, 1, 1),
(7615, 100, 31, 1, 1, 1, 1),
(7616, 227, 31, 1, 1, 1, 1),
(7617, 228, 31, 1, 1, 1, 1),
(7618, 70, 31, 1, 1, 1, 1),
(7619, 71, 31, 1, 1, 1, 1),
(7620, 72, 31, 1, 1, 1, 1),
(7621, 73, 31, 1, 1, 1, 1),
(7622, 65, 31, 0, 0, 0, 0),
(7623, 102, 31, 0, 0, 0, 0),
(7624, 103, 31, 0, 0, 0, 0),
(7625, 104, 31, 0, 0, 0, 0),
(7626, 105, 31, 0, 0, 0, 0),
(7627, 106, 31, 0, 0, 0, 0),
(7628, 107, 31, 0, 0, 0, 0),
(7629, 108, 31, 0, 0, 0, 0),
(7630, 109, 31, 0, 0, 0, 0),
(7631, 110, 31, 0, 0, 0, 0),
(7632, 111, 31, 0, 0, 0, 0),
(7633, 112, 31, 0, 0, 0, 0),
(7634, 113, 31, 0, 0, 0, 0),
(7635, 115, 31, 0, 0, 0, 0),
(7636, 116, 31, 0, 0, 0, 0),
(7637, 117, 31, 0, 0, 0, 0),
(7638, 124, 31, 0, 0, 0, 0),
(7639, 192, 31, 0, 0, 0, 0),
(7640, 193, 31, 0, 0, 0, 0),
(7641, 239, 31, 0, 0, 0, 0),
(7642, 125, 31, 1, 1, 1, 1),
(7643, 126, 31, 1, 1, 1, 1),
(7644, 190, 31, 0, 0, 0, 0),
(7645, 191, 31, 0, 1, 0, 0),
(7646, 256, 31, 1, 1, 1, 1),
(7647, 170, 31, 1, 1, 1, 1),
(7648, 171, 31, 1, 1, 1, 1),
(7649, 172, 31, 1, 1, 1, 1),
(7650, 173, 31, 1, 1, 1, 1),
(7651, 241, 31, 1, 1, 1, 1),
(7652, 242, 31, 1, 1, 1, 1),
(7653, 243, 31, 1, 1, 1, 1),
(7654, 244, 31, 1, 1, 1, 1),
(7655, 245, 31, 1, 1, 1, 1),
(7656, 246, 31, 1, 1, 1, 1),
(7657, 247, 31, 1, 1, 1, 1),
(7658, 248, 31, 1, 1, 1, 1),
(7659, 252, 31, 1, 1, 1, 1),
(7660, 253, 31, 1, 1, 1, 1),
(7661, 156, 34, 1, 1, 1, 0),
(7662, 157, 34, 0, 1, 1, 0),
(7663, 97, 34, 0, 0, 0, 0),
(7664, 98, 34, 0, 0, 0, 0),
(7665, 10, 34, 0, 0, 0, 0),
(7666, 11, 34, 0, 0, 0, 0),
(7667, 12, 34, 0, 0, 0, 0),
(7668, 13, 34, 0, 0, 0, 0),
(7669, 14, 34, 0, 0, 0, 0),
(7670, 16, 34, 0, 0, 0, 0),
(7671, 197, 34, 0, 0, 0, 0),
(7672, 198, 34, 0, 0, 0, 0),
(7673, 203, 34, 0, 0, 0, 0),
(7674, 204, 34, 0, 0, 0, 0),
(7675, 217, 34, 0, 0, 0, 0),
(7676, 218, 34, 0, 0, 0, 0),
(7677, 231, 34, 0, 0, 0, 0),
(7678, 17, 34, 0, 0, 0, 0),
(7679, 18, 34, 0, 0, 0, 0),
(7680, 4, 34, 0, 0, 0, 0),
(7681, 5, 34, 0, 0, 0, 0),
(7682, 205, 34, 0, 0, 0, 0),
(7683, 206, 34, 0, 0, 0, 0),
(7684, 254, 34, 0, 0, 0, 0),
(7685, 255, 34, 0, 0, 0, 0),
(7686, 23, 34, 0, 0, 0, 0),
(7687, 32, 34, 0, 0, 0, 0),
(7688, 34, 34, 0, 0, 0, 0),
(7689, 35, 34, 0, 0, 0, 0),
(7690, 36, 34, 0, 0, 0, 0),
(7691, 37, 34, 0, 0, 0, 0),
(7692, 39, 34, 0, 0, 0, 0),
(7693, 174, 34, 0, 0, 0, 0),
(7694, 175, 34, 0, 0, 0, 0),
(7695, 188, 34, 0, 0, 0, 0),
(7696, 189, 34, 0, 0, 0, 0),
(7697, 219, 34, 0, 0, 0, 0),
(7698, 220, 34, 0, 0, 0, 0),
(7699, 221, 34, 0, 0, 0, 0),
(7700, 222, 34, 0, 0, 0, 0),
(7701, 223, 34, 0, 0, 0, 0),
(7702, 224, 34, 0, 0, 0, 0),
(7703, 225, 34, 0, 0, 0, 0),
(7704, 226, 34, 0, 0, 0, 0),
(7705, 229, 34, 0, 0, 0, 0),
(7706, 230, 34, 0, 0, 0, 0),
(7707, 232, 34, 0, 0, 0, 0),
(7708, 233, 34, 0, 0, 0, 0),
(7709, 249, 34, 0, 0, 0, 0),
(7710, 250, 34, 0, 0, 0, 0),
(7711, 251, 34, 0, 0, 0, 0),
(7712, 21, 34, 0, 0, 0, 0),
(7713, 22, 34, 0, 0, 0, 0),
(7714, 212, 34, 0, 0, 0, 0),
(7715, 213, 34, 0, 0, 0, 0),
(7716, 214, 34, 0, 0, 0, 0),
(7717, 215, 34, 0, 0, 0, 0),
(7718, 1, 34, 0, 0, 0, 0),
(7719, 2, 34, 0, 0, 0, 0),
(7720, 194, 34, 0, 0, 0, 0),
(7721, 195, 34, 0, 0, 0, 0),
(7722, 196, 34, 0, 0, 0, 0),
(7723, 208, 34, 0, 0, 0, 0),
(7724, 209, 34, 0, 0, 0, 0),
(7725, 210, 34, 0, 0, 0, 0),
(7726, 211, 34, 0, 0, 0, 0),
(7727, 257, 34, 0, 0, 0, 0),
(7728, 99, 34, 0, 0, 0, 0),
(7729, 100, 34, 0, 0, 0, 0),
(7730, 227, 34, 0, 0, 0, 0),
(7731, 228, 34, 0, 0, 0, 0),
(7732, 70, 34, 0, 0, 0, 0),
(7733, 71, 34, 0, 0, 0, 0),
(7734, 72, 34, 0, 0, 0, 0),
(7735, 73, 34, 0, 0, 0, 0),
(7736, 65, 34, 0, 0, 0, 0),
(7737, 102, 34, 1, 1, 1, 1),
(7738, 103, 34, 1, 1, 1, 1),
(7739, 104, 34, 1, 1, 1, 1),
(7740, 105, 34, 0, 0, 0, 0),
(7741, 106, 34, 0, 0, 0, 0),
(7742, 107, 34, 0, 0, 0, 0),
(7743, 108, 34, 0, 0, 0, 0),
(7744, 109, 34, 0, 0, 0, 0),
(7745, 110, 34, 0, 0, 0, 0),
(7746, 111, 34, 0, 0, 0, 0),
(7747, 112, 34, 1, 1, 1, 1),
(7748, 113, 34, 0, 0, 0, 0),
(7749, 115, 34, 0, 0, 0, 0),
(7750, 116, 34, 0, 0, 0, 0),
(7751, 117, 34, 0, 0, 0, 0),
(7752, 124, 34, 0, 0, 0, 0),
(7753, 192, 34, 1, 1, 1, 1),
(7754, 193, 34, 1, 1, 1, 1),
(7755, 239, 34, 1, 1, 1, 1),
(7756, 125, 34, 0, 0, 0, 0),
(7757, 126, 34, 0, 0, 0, 0),
(7758, 190, 34, 0, 1, 1, 0),
(7759, 191, 34, 1, 1, 1, 0),
(7760, 256, 34, 0, 0, 0, 0),
(7761, 170, 34, 0, 0, 0, 0),
(7762, 171, 34, 0, 0, 0, 0),
(7763, 172, 34, 0, 0, 0, 0),
(7764, 173, 34, 0, 0, 0, 0),
(7765, 241, 34, 0, 0, 0, 0),
(7766, 242, 34, 0, 0, 0, 0),
(7767, 243, 34, 0, 0, 0, 0),
(7768, 244, 34, 0, 0, 0, 0),
(7769, 245, 34, 0, 0, 0, 0),
(7770, 246, 34, 0, 0, 0, 0),
(7771, 247, 34, 0, 0, 0, 0),
(7772, 248, 34, 0, 0, 0, 0),
(7773, 252, 34, 0, 0, 0, 0),
(7774, 253, 34, 0, 0, 0, 0),
(7890, 156, 26, 0, 1, 0, 0),
(7891, 157, 26, 0, 0, 0, 0),
(7892, 97, 26, 0, 0, 0, 0),
(7893, 98, 26, 0, 1, 0, 0),
(7894, 10, 26, 0, 0, 0, 0),
(7895, 11, 26, 0, 1, 0, 0),
(7896, 12, 26, 0, 0, 0, 0),
(7897, 13, 26, 0, 1, 0, 0),
(7898, 14, 26, 0, 0, 0, 0),
(7899, 16, 26, 0, 1, 0, 0),
(7900, 197, 26, 0, 0, 0, 0),
(7901, 198, 26, 0, 1, 0, 0),
(7902, 203, 26, 0, 0, 0, 0),
(7903, 204, 26, 0, 1, 0, 0),
(7904, 217, 26, 0, 0, 0, 0),
(7905, 218, 26, 0, 1, 0, 0),
(7906, 231, 26, 0, 0, 0, 0),
(7907, 17, 26, 1, 1, 1, 1),
(7908, 18, 26, 1, 1, 1, 1),
(7909, 4, 26, 1, 1, 1, 1),
(7910, 5, 26, 1, 1, 1, 1),
(7911, 205, 26, 0, 0, 0, 0),
(7912, 206, 26, 0, 0, 0, 0),
(7913, 254, 26, 0, 0, 0, 0),
(7914, 255, 26, 0, 0, 0, 0),
(7915, 23, 26, 0, 0, 0, 0),
(7916, 32, 26, 1, 1, 1, 1),
(7917, 34, 26, 1, 1, 1, 1),
(7918, 35, 26, 1, 1, 1, 1),
(7919, 36, 26, 1, 1, 1, 1),
(7920, 37, 26, 1, 1, 1, 1),
(7921, 39, 26, 1, 1, 1, 1),
(7922, 174, 26, 1, 1, 1, 1),
(7923, 175, 26, 1, 1, 1, 1),
(7924, 188, 26, 1, 1, 1, 1),
(7925, 189, 26, 0, 0, 0, 0),
(7926, 219, 26, 1, 1, 1, 1),
(7927, 220, 26, 1, 1, 1, 1),
(7928, 221, 26, 1, 1, 1, 1),
(7929, 222, 26, 1, 1, 1, 1),
(7930, 223, 26, 0, 0, 0, 0),
(7931, 224, 26, 0, 0, 0, 0),
(7932, 225, 26, 0, 0, 0, 0),
(7933, 226, 26, 0, 0, 0, 0),
(7934, 229, 26, 1, 1, 1, 1),
(7935, 230, 26, 0, 0, 0, 0),
(7936, 232, 26, 1, 1, 1, 1),
(7937, 233, 26, 1, 1, 1, 1),
(7938, 249, 26, 1, 1, 1, 1),
(7939, 250, 26, 1, 1, 1, 1),
(7940, 251, 26, 1, 1, 1, 1),
(7941, 258, 26, 0, 0, 0, 0),
(7942, 21, 26, 1, 1, 1, 1),
(7943, 22, 26, 1, 1, 1, 1),
(7944, 212, 26, 1, 1, 1, 1),
(7945, 213, 26, 1, 1, 1, 1),
(7946, 214, 26, 1, 1, 1, 1),
(7947, 215, 26, 1, 1, 1, 1),
(7948, 1, 26, 1, 1, 1, 1),
(7949, 2, 26, 1, 1, 1, 1),
(7950, 194, 26, 1, 1, 1, 1),
(7951, 195, 26, 1, 1, 1, 1),
(7952, 196, 26, 1, 1, 1, 1),
(7953, 208, 26, 1, 1, 1, 1),
(7954, 209, 26, 1, 1, 1, 1),
(7955, 210, 26, 1, 1, 1, 1),
(7956, 211, 26, 1, 1, 1, 1),
(7957, 257, 26, 1, 1, 1, 1),
(7958, 99, 26, 1, 1, 1, 1),
(7959, 100, 26, 1, 1, 1, 1),
(7960, 227, 26, 1, 1, 1, 1),
(7961, 228, 26, 1, 1, 1, 1),
(7962, 70, 26, 0, 0, 0, 0),
(7963, 71, 26, 0, 0, 0, 0),
(7964, 72, 26, 0, 0, 0, 0),
(7965, 73, 26, 0, 0, 0, 0),
(7966, 65, 26, 0, 0, 0, 0),
(7967, 102, 26, 0, 0, 0, 0),
(7968, 103, 26, 0, 0, 0, 0),
(7969, 104, 26, 0, 0, 0, 0),
(7970, 105, 26, 0, 0, 0, 0),
(7971, 106, 26, 0, 0, 0, 0),
(7972, 107, 26, 0, 0, 0, 0),
(7973, 108, 26, 0, 0, 0, 0),
(7974, 109, 26, 0, 0, 0, 0),
(7975, 110, 26, 0, 0, 0, 0),
(7976, 111, 26, 0, 0, 0, 0),
(7977, 112, 26, 0, 0, 0, 0),
(7978, 113, 26, 0, 0, 0, 0),
(7979, 115, 26, 0, 0, 0, 0),
(7980, 116, 26, 0, 0, 0, 0),
(7981, 117, 26, 0, 0, 0, 0),
(7982, 124, 26, 0, 0, 0, 0),
(7983, 192, 26, 0, 0, 0, 0),
(7984, 193, 26, 0, 0, 0, 0),
(7985, 239, 26, 0, 0, 0, 0),
(7986, 125, 26, 0, 0, 0, 0),
(7987, 126, 26, 0, 1, 0, 0),
(7988, 190, 26, 0, 0, 0, 0),
(7989, 191, 26, 0, 1, 0, 0),
(7990, 256, 26, 0, 0, 0, 0),
(7991, 170, 26, 0, 0, 0, 0),
(7992, 171, 26, 0, 0, 0, 0),
(7993, 172, 26, 0, 0, 0, 0),
(7994, 173, 26, 0, 0, 0, 0),
(7995, 241, 26, 0, 0, 0, 0),
(7996, 242, 26, 0, 1, 0, 0),
(7997, 243, 26, 1, 1, 1, 1),
(7998, 244, 26, 1, 1, 1, 1),
(7999, 245, 26, 1, 1, 1, 1),
(8000, 246, 26, 1, 1, 1, 1),
(8001, 247, 26, 1, 1, 1, 1),
(8002, 248, 26, 1, 1, 1, 1),
(8003, 252, 26, 1, 1, 1, 1),
(8004, 253, 26, 1, 1, 1, 1),
(8005, 156, 24, 1, 1, 1, 0),
(8006, 157, 24, 0, 1, 1, 0),
(8007, 97, 24, 1, 1, 1, 1),
(8008, 98, 24, 1, 1, 1, 1),
(8009, 10, 24, 1, 1, 1, 1),
(8010, 11, 24, 1, 1, 1, 1),
(8011, 12, 24, 1, 1, 1, 1),
(8012, 13, 24, 1, 1, 1, 1),
(8013, 14, 24, 1, 1, 1, 1),
(8014, 16, 24, 1, 1, 1, 1),
(8015, 197, 24, 1, 1, 1, 1),
(8016, 198, 24, 1, 1, 1, 1),
(8017, 203, 24, 1, 1, 1, 1),
(8018, 204, 24, 1, 1, 1, 1),
(8019, 217, 24, 1, 1, 1, 1),
(8020, 218, 24, 1, 1, 1, 1),
(8021, 231, 24, 1, 1, 1, 1),
(8022, 17, 24, 1, 1, 1, 1),
(8023, 18, 24, 1, 1, 1, 1),
(8024, 4, 24, 1, 1, 1, 1),
(8025, 5, 24, 1, 1, 1, 1),
(8026, 205, 24, 1, 1, 1, 1),
(8027, 206, 24, 1, 1, 1, 1),
(8028, 254, 24, 1, 1, 1, 1),
(8029, 255, 24, 1, 1, 1, 1),
(8030, 23, 24, 1, 1, 1, 1),
(8031, 32, 24, 1, 1, 1, 1),
(8032, 34, 24, 1, 1, 1, 1),
(8033, 35, 24, 1, 1, 1, 1),
(8034, 36, 24, 1, 1, 1, 1),
(8035, 37, 24, 1, 1, 1, 1),
(8036, 39, 24, 1, 1, 1, 1),
(8037, 174, 24, 1, 1, 1, 1),
(8038, 175, 24, 1, 1, 1, 1),
(8039, 188, 24, 1, 1, 1, 1),
(8040, 189, 24, 1, 1, 1, 1),
(8041, 219, 24, 1, 1, 1, 1),
(8042, 220, 24, 1, 1, 1, 1),
(8043, 221, 24, 1, 1, 1, 1),
(8044, 222, 24, 1, 1, 1, 1),
(8045, 223, 24, 1, 1, 1, 1),
(8046, 224, 24, 1, 1, 1, 1),
(8047, 225, 24, 1, 1, 1, 1),
(8048, 226, 24, 1, 1, 1, 1),
(8049, 229, 24, 1, 1, 1, 1),
(8050, 230, 24, 1, 1, 1, 1),
(8051, 232, 24, 1, 1, 1, 1),
(8052, 233, 24, 1, 1, 1, 1),
(8053, 249, 24, 1, 1, 1, 1),
(8054, 250, 24, 1, 1, 1, 1),
(8055, 251, 24, 1, 1, 1, 1),
(8056, 258, 24, 1, 1, 1, 1),
(8057, 21, 24, 1, 1, 1, 1),
(8058, 22, 24, 1, 1, 1, 1),
(8059, 212, 24, 1, 1, 1, 1),
(8060, 213, 24, 1, 1, 1, 1),
(8061, 214, 24, 1, 1, 1, 1),
(8062, 215, 24, 1, 1, 1, 1),
(8063, 1, 24, 1, 1, 1, 1),
(8064, 2, 24, 1, 1, 1, 1),
(8065, 194, 24, 1, 1, 1, 1),
(8066, 195, 24, 1, 1, 1, 1),
(8067, 196, 24, 1, 1, 1, 1),
(8068, 208, 24, 1, 1, 1, 1),
(8069, 209, 24, 1, 1, 1, 1),
(8070, 210, 24, 1, 1, 1, 1),
(8071, 211, 24, 1, 1, 1, 1),
(8072, 257, 24, 1, 1, 1, 1),
(8073, 99, 24, 1, 1, 1, 1),
(8074, 100, 24, 1, 1, 1, 1),
(8075, 227, 24, 1, 1, 1, 1),
(8076, 228, 24, 1, 1, 1, 1),
(8077, 70, 24, 1, 1, 1, 1),
(8078, 71, 24, 1, 1, 1, 1),
(8079, 72, 24, 1, 1, 1, 1),
(8080, 73, 24, 1, 1, 1, 1),
(8081, 65, 24, 0, 0, 0, 0),
(8082, 102, 24, 0, 0, 0, 0),
(8083, 103, 24, 1, 1, 1, 1),
(8084, 104, 24, 1, 1, 1, 1),
(8085, 105, 24, 0, 0, 0, 0),
(8086, 106, 24, 0, 0, 0, 0),
(8087, 107, 24, 0, 0, 0, 0),
(8088, 108, 24, 0, 0, 0, 0),
(8089, 109, 24, 0, 0, 0, 0),
(8090, 110, 24, 0, 0, 0, 0),
(8091, 111, 24, 0, 1, 0, 0),
(8092, 112, 24, 1, 1, 1, 1),
(8093, 113, 24, 0, 0, 0, 0),
(8094, 115, 24, 0, 0, 0, 0),
(8095, 116, 24, 0, 0, 0, 0),
(8096, 117, 24, 0, 0, 0, 0),
(8097, 124, 24, 0, 0, 0, 0),
(8098, 192, 24, 1, 1, 1, 1),
(8099, 193, 24, 1, 1, 1, 1),
(8100, 239, 24, 0, 0, 0, 0),
(8101, 125, 24, 1, 1, 1, 1),
(8102, 126, 24, 1, 1, 1, 1),
(8103, 190, 24, 0, 1, 1, 0),
(8104, 191, 24, 1, 1, 1, 0),
(8105, 256, 24, 1, 1, 1, 1),
(8106, 170, 24, 1, 1, 1, 1),
(8107, 171, 24, 1, 1, 1, 1),
(8108, 172, 24, 1, 1, 1, 1),
(8109, 173, 24, 1, 1, 1, 1),
(8110, 241, 24, 1, 1, 1, 1),
(8111, 242, 24, 1, 1, 1, 1),
(8112, 243, 24, 1, 1, 1, 1),
(8113, 244, 24, 1, 1, 1, 1),
(8114, 245, 24, 1, 1, 1, 1),
(8115, 246, 24, 1, 1, 1, 1),
(8116, 247, 24, 1, 1, 1, 1),
(8117, 248, 24, 1, 1, 1, 1),
(8118, 252, 24, 1, 1, 1, 1),
(8119, 253, 24, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `role_permission_re`
--

CREATE TABLE `role_permission_re` (
  `id` int(10) UNSIGNED NOT NULL,
  `type` varchar(100) NOT NULL,
  `module_id` int(10) UNSIGNED NOT NULL,
  `submodule_id` int(10) UNSIGNED NOT NULL,
  `create` tinyint(1) NOT NULL DEFAULT 0,
  `read` tinyint(1) NOT NULL DEFAULT 0,
  `update` tinyint(1) NOT NULL DEFAULT 0,
  `delete` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_permission_re`
--

INSERT INTO `role_permission_re` (`id`, `type`, `module_id`, `submodule_id`, `create`, `read`, `update`, `delete`) VALUES
(16, 'Role1', 1, 1, 1, 1, 1, 1),
(17, 'Role1', 2, 2, 1, 1, 1, 1),
(18, 'Role1', 3, 3, 1, 1, 1, 1),
(19, 'Role1', 4, 4, 0, 0, 0, 0),
(20, 'Role1', 4, 5, 0, 0, 0, 0),
(21, 'Role1', 4, 6, 0, 0, 0, 0),
(22, 'Role1', 4, 7, 0, 0, 0, 0),
(23, 'Role1', 4, 8, 0, 0, 0, 0),
(24, 'Role1', 4, 9, 0, 0, 0, 0),
(25, 'Role1', 4, 10, 0, 0, 0, 0),
(26, 'Role1', 4, 11, 0, 0, 0, 0),
(27, 'Role1', 5, 12, 0, 0, 0, 0),
(28, 'Role1', 6, 13, 0, 0, 0, 0),
(29, 'Role1', 6, 14, 0, 0, 0, 0),
(30, 'Role1', 6, 15, 0, 0, 0, 0),
(63, 'Testing', 1, 1, 1, 1, 1, 1),
(64, 'Testing', 2, 2, 1, 1, 1, 1),
(65, 'Testing', 3, 3, 1, 1, 1, 1),
(66, 'Testing', 4, 4, 1, 1, 1, 1),
(67, 'Testing', 4, 5, 1, 1, 1, 1),
(68, 'Testing', 4, 6, 1, 1, 1, 1),
(69, 'Testing', 4, 7, 1, 1, 1, 1),
(70, 'Testing', 4, 8, 1, 1, 1, 1),
(71, 'Testing', 4, 9, 1, 1, 1, 1),
(72, 'Testing', 4, 10, 1, 1, 1, 1),
(73, 'Testing', 4, 11, 1, 1, 1, 1),
(74, 'Testing', 5, 12, 1, 1, 1, 1),
(75, 'Testing', 6, 13, 0, 0, 0, 0),
(76, 'Testing', 6, 14, 0, 0, 0, 0),
(77, 'Testing', 6, 15, 0, 0, 0, 0),
(78, 'Testing', 6, 16, 0, 0, 0, 0),
(79, 'Testing', 6, 20, 0, 0, 0, 0),
(80, 'Testing', 7, 17, 1, 1, 1, 1),
(81, 'Testing', 8, 18, 1, 1, 1, 1),
(82, 'Testing', 8, 19, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sale`
--

CREATE TABLE `sale` (
  `id` int(11) NOT NULL,
  `sale_id` varbinary(2000) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `employee_id` int(20) NOT NULL,
  `date` varchar(255) NOT NULL,
  `details` varchar(2000) NOT NULL,
  `discount` varbinary(2000) NOT NULL,
  `total_discount_ammount` varbinary(2000) NOT NULL,
  `total_vat_amnt` varbinary(2000) NOT NULL,
  `grandTotal` varbinary(2000) NOT NULL,
  `total` varbinary(2000) NOT NULL,
  `payment_type` int(11) NOT NULL,
  `createddate` datetime NOT NULL,
  `lastupdateddate` datetime NOT NULL,
  `type2` varbinary(2000) NOT NULL,
  `status` tinyint(1) DEFAULT 0,
  `userid` int(10) NOT NULL,
  `incidenttype` int(100) NOT NULL,
  `already` int(10) NOT NULL,
  `branch` int(100) NOT NULL,
  `invoicetype` varchar(1000) NOT NULL,
  `sales_order_no` int(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_order`
--

CREATE TABLE `sales_order` (
  `id` int(11) NOT NULL,
  `sale_id` varbinary(2000) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `employee_id` int(20) NOT NULL,
  `date` varchar(255) NOT NULL,
  `details` varchar(2000) NOT NULL,
  `discount` varbinary(2000) NOT NULL,
  `total_discount_ammount` varbinary(2000) NOT NULL,
  `total_vat_amnt` varbinary(2000) NOT NULL,
  `grandTotal` varbinary(2000) NOT NULL,
  `total` varbinary(2000) NOT NULL,
  `createddate` datetime NOT NULL,
  `lastupdateddate` datetime NOT NULL,
  `type2` varbinary(2000) NOT NULL,
  `status` tinyint(1) DEFAULT 0,
  `userid` int(10) NOT NULL,
  `incidenttype` int(100) NOT NULL,
  `already` int(10) NOT NULL,
  `branch` int(100) NOT NULL,
  `invoicetype` varchar(1000) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_order_details`
--

CREATE TABLE `sales_order_details` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `product` int(11) NOT NULL,
  `store` int(11) NOT NULL,
  `quantity` varbinary(500) NOT NULL,
  `product_rate` varbinary(500) NOT NULL,
  `discount` varbinary(500) NOT NULL,
  `discount_value` varbinary(500) NOT NULL,
  `vat_percent` varbinary(500) NOT NULL,
  `vat_value` varbinary(500) NOT NULL,
  `total_price` varbinary(500) NOT NULL,
  `total_discount` varbinary(500) NOT NULL,
  `all_discount` varbinary(500) NOT NULL,
  `type2` varbinary(1000) NOT NULL,
  `conversion_id` int(20) NOT NULL,
  `unit` int(20) NOT NULL,
  `batch` int(20) NOT NULL,
  `group_id` int(20) NOT NULL DEFAULT 0,
  `parent` int(20) NOT NULL DEFAULT 0,
  `invoicegroup` int(20) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_order_group_details`
--

CREATE TABLE `sales_order_group_details` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `groupid` int(11) NOT NULL,
  `quantity` varbinary(500) NOT NULL,
  `product_rate` varbinary(500) NOT NULL,
  `discount` varbinary(500) NOT NULL,
  `discount_value` varbinary(500) NOT NULL,
  `vat_percent` varbinary(500) NOT NULL,
  `vat_value` varbinary(500) NOT NULL,
  `total_price` varbinary(500) NOT NULL,
  `total_discount` varbinary(500) NOT NULL,
  `all_discount` varbinary(500) NOT NULL,
  `type2` varbinary(1000) NOT NULL,
  `unit` int(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_return`
--

CREATE TABLE `sales_return` (
  `id` int(11) NOT NULL,
  `sales_return_id` varbinary(2000) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `employee_id` int(20) NOT NULL,
  `date` varchar(255) NOT NULL,
  `rdate` varchar(255) NOT NULL,
  `details` varchar(2000) NOT NULL,
  `discount` varbinary(2000) NOT NULL,
  `total_discount_ammount` varbinary(2000) NOT NULL,
  `total_vat_amnt` varbinary(2000) NOT NULL,
  `grandTotal` varbinary(2000) NOT NULL,
  `total` varbinary(2000) NOT NULL,
  `payment_type` int(11) NOT NULL,
  `createddate` datetime NOT NULL,
  `lastupdateddate` datetime NOT NULL,
  `type2` varbinary(2000) NOT NULL,
  `status` tinyint(1) DEFAULT 0,
  `userid` int(10) NOT NULL,
  `incidenttype` int(100) NOT NULL,
  `already` int(10) NOT NULL,
  `branch` int(100) NOT NULL,
  `invoicetype` varchar(1000) NOT NULL,
  `sales_id` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_return_details`
--

CREATE TABLE `sales_return_details` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `product` int(11) NOT NULL,
  `store` int(11) NOT NULL,
  `quantity` varbinary(500) NOT NULL,
  `product_rate` varbinary(500) NOT NULL,
  `discount` varbinary(500) NOT NULL,
  `discount_value` varbinary(500) NOT NULL,
  `vat_percent` varbinary(500) NOT NULL,
  `vat_value` varbinary(500) NOT NULL,
  `rqty` varbinary(500) NOT NULL,
  `rstore` int(11) NOT NULL,
  `rdeduction` varbinary(500) NOT NULL,
  `total_price` varbinary(500) NOT NULL,
  `total_discount` varbinary(500) NOT NULL,
  `all_discount` varbinary(500) NOT NULL,
  `type2` varbinary(1000) NOT NULL,
  `conversion_id` int(20) NOT NULL,
  `unit` int(20) NOT NULL,
  `batch` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sale_details`
--

CREATE TABLE `sale_details` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `product` int(11) NOT NULL,
  `store` int(11) NOT NULL,
  `quantity` varbinary(500) NOT NULL,
  `product_rate` varbinary(500) NOT NULL,
  `discount` varbinary(500) NOT NULL,
  `discount_value` varbinary(500) NOT NULL,
  `vat_percent` varbinary(500) NOT NULL,
  `vat_value` varbinary(500) NOT NULL,
  `total_price` varbinary(500) NOT NULL,
  `total_discount` varbinary(500) NOT NULL,
  `all_discount` varbinary(500) NOT NULL,
  `type2` varbinary(1000) NOT NULL,
  `conversion_id` int(20) NOT NULL,
  `unit` int(20) NOT NULL,
  `batch` int(20) NOT NULL,
  `group_id` int(20) NOT NULL DEFAULT 0,
  `parent` int(20) NOT NULL DEFAULT 0,
  `invoicegroup` int(20) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sale_group_details`
--

CREATE TABLE `sale_group_details` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `groupid` int(11) NOT NULL,
  `quantity` varbinary(500) NOT NULL,
  `product_rate` varbinary(500) NOT NULL,
  `discount` varbinary(500) NOT NULL,
  `discount_value` varbinary(500) NOT NULL,
  `vat_percent` varbinary(500) NOT NULL,
  `vat_value` varbinary(500) NOT NULL,
  `total_price` varbinary(500) NOT NULL,
  `total_discount` varbinary(500) NOT NULL,
  `all_discount` varbinary(500) NOT NULL,
  `type2` varbinary(1000) NOT NULL,
  `unit` int(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sec_branch`
--

CREATE TABLE `sec_branch` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `branchid` int(11) NOT NULL,
  `createby` int(11) NOT NULL,
  `createdate` varchar(255) NOT NULL,
  `default` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sec_branch_re`
--

CREATE TABLE `sec_branch_re` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `branch_id` int(10) UNSIGNED NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sec_role`
--

CREATE TABLE `sec_role` (
  `id` int(11) NOT NULL,
  `type` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `sec_role`
--

INSERT INTO `sec_role` (`id`, `type`, `status`) VALUES
(24, 'System Implementer', 1),
(26, 'Cashier', 1),
(29, 'Store Keeper', 1),
(30, 'Accountant', 1),
(31, 'Manager', 1),
(34, 'Admin', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sec_store`
--

CREATE TABLE `sec_store` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `storeid` int(11) NOT NULL,
  `createby` int(11) NOT NULL,
  `createdate` varchar(255) NOT NULL,
  `default` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sec_store_re`
--

CREATE TABLE `sec_store_re` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `store_id` int(10) UNSIGNED NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sec_userrole`
--

CREATE TABLE `sec_userrole` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `roleid` int(11) NOT NULL,
  `createby` varchar(50) NOT NULL,
  `createdate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `sec_userrole`
--

INSERT INTO `sec_userrole` (`id`, `user_id`, `roleid`, `createby`, `createdate`) VALUES
(38, '240776', 25, '1', '2025-10-28 03:29:48'),
(39, '977501', 26, '1', '2025-10-28 03:46:22'),
(40, '329331', 27, '1', '2025-10-28 04:07:45'),
(41, '368862', 26, '1', '2025-10-28 04:27:10'),
(42, '329331', 26, '1', '2025-10-28 04:27:23'),
(43, '240776', 24, '1', '2025-10-28 04:27:33'),
(44, '602812', 26, '1', '2025-10-28 04:50:55'),
(45, '739262', 29, '1', '2025-10-28 04:58:41'),
(46, '654348', 29, '1', '2025-10-28 04:58:54'),
(47, '495733', 29, '1', '2025-10-28 05:02:36'),
(48, '175725', 30, '1', '2025-10-28 05:05:04'),
(49, '149551', 31, '1', '2025-10-28 05:09:32'),
(50, '863140', 29, '1', '2025-10-28 05:11:53'),
(51, '974686', 30, '1', '2025-10-28 05:16:42'),
(52, '409360', 31, '1', '2025-10-28 05:20:13'),
(56, '2680', 24, '1', '2026-03-23 12:57:36'),
(57, '235553', 31, '1', '2026-05-12 11:18:09'),
(58, '589134', 34, '1', '2026-06-06 06:08:17'),
(59, '190693', 31, '1', '2026-07-17 05:43:58'),
(61, '210', 26, '1', '2026-07-22 02:28:57'),
(62, '588420', 24, '1', '2026-07-24 12:11:17'),
(63, '240411', 31, '1', '2026-07-24 12:16:48'),
(64, '983426', 26, '1', '2026-07-24 03:20:43'),
(65, '130798', 26, '1', '2026-07-24 03:20:58'),
(66, '912790', 26, '1', '2026-07-24 03:21:21'),
(67, '887282', 29, '1', '2026-07-24 03:21:44'),
(68, '746111', 29, '1', '2026-07-24 03:22:07'),
(69, '680313', 29, '1', '2026-07-24 03:22:23'),
(70, '763872', 29, '1', '2026-07-24 03:22:38'),
(71, '593628', 30, '1', '2026-07-24 03:23:20'),
(72, '427225', 30, '1', '2026-07-24 03:23:33'),
(73, '559079', 31, '1', '2026-07-24 03:23:52'),
(74, '150050', 31, '1', '2026-07-24 03:24:04'),
(75, '106152', 34, '1', '2026-10-02 07:37:12');

-- --------------------------------------------------------

--
-- Table structure for table `sec_userrole_re`
--

CREATE TABLE `sec_userrole_re` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `role_type` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sec_userrole_re`
--

INSERT INTO `sec_userrole_re` (`id`, `user_id`, `role_type`, `status`) VALUES
(1, '329331', 'Role1', 1),
(2, '240776', 'Testing', 1),
(4, '2680', 'Testing', 1),
(5, '588420', 'Testing', 1);

-- --------------------------------------------------------

--
-- Table structure for table `seles_termscondi`
--

CREATE TABLE `seles_termscondi` (
  `id` int(11) NOT NULL,
  `description` text NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seles_termscondi`
--

INSERT INTO `seles_termscondi` (`id`, `description`, `status`) VALUES
(5, 'Goods purchased without the original invoice are not to be returned or exchanged', 1);

-- --------------------------------------------------------

--
-- Table structure for table `service`
--

CREATE TABLE `service` (
  `id` int(11) NOT NULL,
  `service_id` varbinary(2000) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `employee_id` int(20) NOT NULL,
  `date` varchar(255) NOT NULL,
  `eod_date` varchar(20) NOT NULL DEFAULT '',
  `details` varchar(2000) NOT NULL,
  `discount` varbinary(2000) NOT NULL,
  `total_discount_ammount` varbinary(2000) NOT NULL,
  `total_vat_amnt` varbinary(2000) NOT NULL,
  `grandTotal` varbinary(2000) NOT NULL,
  `total` varbinary(2000) NOT NULL,
  `payment_type` int(11) NOT NULL,
  `invoicetype` varchar(20) NOT NULL DEFAULT '',
  `createddate` datetime NOT NULL,
  `lastupdateddate` datetime NOT NULL,
  `type2` varbinary(2000) NOT NULL,
  `status` tinyint(1) DEFAULT 0,
  `userid` int(10) NOT NULL,
  `already` int(10) NOT NULL,
  `branch` int(100) NOT NULL,
  `service_order_id` int(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_details`
--

CREATE TABLE `service_details` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `service` int(11) NOT NULL,
  `quantity` varbinary(500) NOT NULL,
  `product_rate` varbinary(500) NOT NULL,
  `discount` varbinary(500) NOT NULL,
  `discount_value` varbinary(500) NOT NULL,
  `vat_percent` varbinary(500) NOT NULL,
  `vat_value` varbinary(500) NOT NULL,
  `total_price` varbinary(500) NOT NULL,
  `total_discount` varbinary(500) NOT NULL,
  `all_discount` varbinary(500) NOT NULL,
  `type2` varbinary(1000) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_invoice`
--

CREATE TABLE `service_invoice` (
  `id` int(11) NOT NULL,
  `voucher_no` varchar(30) NOT NULL,
  `date` date NOT NULL,
  `employee_id` varchar(100) DEFAULT NULL,
  `customer_id` varchar(30) NOT NULL,
  `total_amount` decimal(20,2) NOT NULL DEFAULT 0.00,
  `total_discount` decimal(20,2) NOT NULL DEFAULT 0.00,
  `invoice_discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_vat_amnt` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'total invoice vat',
  `total_tax` decimal(10,2) DEFAULT 0.00,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `due_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `shipping_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `previous` decimal(10,2) NOT NULL DEFAULT 0.00,
  `details` varchar(250) NOT NULL,
  `sales_by` varchar(20) DEFAULT NULL,
  `is_fixed` int(11) NOT NULL DEFAULT 0,
  `is_dynamic` int(11) NOT NULL DEFAULT 0,
  `is_credit` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_invoice_details`
--

CREATE TABLE `service_invoice_details` (
  `id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `service_inv_id` varchar(30) NOT NULL,
  `qty` decimal(10,2) NOT NULL DEFAULT 0.00,
  `charge` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(10,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `vat` decimal(10,2) DEFAULT 0.00,
  `vat_amnt` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_order`
--

CREATE TABLE `service_order` (
  `id` int(11) NOT NULL,
  `service_order_id` varbinary(2000) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `employee_id` int(20) NOT NULL,
  `invoicetype` varchar(20) NOT NULL DEFAULT '',
  `date` varchar(255) NOT NULL,
  `eod_date` varchar(20) NOT NULL DEFAULT '',
  `details` varchar(2000) NOT NULL,
  `discount` varbinary(2000) NOT NULL,
  `total_discount_ammount` varbinary(2000) NOT NULL,
  `total_vat_amnt` varbinary(2000) NOT NULL,
  `grandTotal` varbinary(2000) NOT NULL,
  `total` varbinary(2000) NOT NULL,
  `createddate` datetime NOT NULL,
  `lastupdateddate` datetime NOT NULL,
  `type2` varbinary(2000) NOT NULL,
  `status` tinyint(1) DEFAULT 0,
  `userid` int(10) NOT NULL,
  `already` int(10) NOT NULL,
  `branch` int(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_order_details`
--

CREATE TABLE `service_order_details` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `service` int(11) NOT NULL,
  `quantity` varbinary(500) NOT NULL,
  `product_rate` varbinary(500) NOT NULL,
  `discount` varbinary(500) NOT NULL,
  `discount_value` varbinary(500) NOT NULL,
  `vat_percent` varbinary(500) NOT NULL,
  `vat_value` varbinary(500) NOT NULL,
  `total_price` varbinary(500) NOT NULL,
  `total_discount` varbinary(500) NOT NULL,
  `all_discount` varbinary(500) NOT NULL,
  `type2` varbinary(1000) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sms_settings`
--

CREATE TABLE `sms_settings` (
  `id` int(11) NOT NULL,
  `api_key` varchar(100) DEFAULT NULL,
  `api_secret` varchar(100) DEFAULT NULL,
  `from` varchar(100) DEFAULT NULL,
  `isinvoice` int(11) NOT NULL DEFAULT 0,
  `isservice` int(11) NOT NULL DEFAULT 0,
  `isreceive` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `sms_settings`
--

INSERT INTO `sms_settings` (`id`, `api_key`, `api_secret`, `from`, `isinvoice`, `isservice`, `isreceive`) VALUES
(1, '5d6db102011', '456452dfgdf', '8801645452', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `stockbatch`
--

CREATE TABLE `stockbatch` (
  `id` int(11) NOT NULL,
  `batchid` varbinary(1000) NOT NULL,
  `details` varchar(1000) NOT NULL,
  `opening` int(11) NOT NULL,
  `busage` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `product` int(11) NOT NULL,
  `mdate` varchar(255) NOT NULL,
  `pdate` varchar(255) NOT NULL,
  `mrp` varbinary(1000) NOT NULL,
  `edate` varchar(255) NOT NULL,
  `edate_enabled` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stockbatch`
--

INSERT INTO `stockbatch` (`id`, `batchid`, `details`, `opening`, `busage`, `status`, `product`, `mdate`, `pdate`, `mrp`, `edate`, `edate_enabled`) VALUES
(1, 0xb857732c2281694ae978b2b24511e7ee, '', 0, 'multiple', 1, 0, '', '', 0x548ce29c08ec9fbb3766bc2362a68dc9, '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `stockbatchopening`
--

CREATE TABLE `stockbatchopening` (
  `id` int(11) NOT NULL,
  `batch` int(11) NOT NULL,
  `product` int(11) NOT NULL,
  `mdate` varchar(1000) NOT NULL,
  `edate` varchar(1000) NOT NULL,
  `mrp` varbinary(1000) NOT NULL,
  `pdate` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_details`
--

CREATE TABLE `stock_details` (
  `id` int(11) NOT NULL,
  `product` int(11) NOT NULL,
  `batch` int(11) NOT NULL,
  `store` int(11) NOT NULL,
  `stock` varbinary(500) NOT NULL,
  `type` varchar(255) NOT NULL,
  `pid` int(14) NOT NULL,
  `date` date NOT NULL,
  `conversion_id` int(20) NOT NULL,
  `unit` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `store`
--

CREATE TABLE `store` (
  `id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `auto_gdn` int(11) NOT NULL,
  `auto_grn` int(11) NOT NULL,
  `dstock` int(11) NOT NULL,
  `store_nature` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `store`
--

INSERT INTO `store` (`id`, `code`, `name`, `status`, `auto_gdn`, `auto_grn`, `dstock`, `store_nature`) VALUES
(1, '000000', 'N/A', 1, 0, 0, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subunit_product`
--

CREATE TABLE `subunit_product` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `subsell_price` varbinary(1000) NOT NULL,
  `subcost_price` varbinary(1000) NOT NULL,
  `first` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sub_module`
--

CREATE TABLE `sub_module` (
  `id` int(11) NOT NULL,
  `mid` int(11) NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `image` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `directory` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `sub_module`
--

INSERT INTO `sub_module` (`id`, `mid`, `name`, `description`, `image`, `directory`, `status`) VALUES
(1, 10, 'new_invoice', NULL, NULL, 'new_invoice', 1),
(2, 10, 'manage_invoice', NULL, NULL, 'manage_invoice', 1),
(4, 5, 'add_customer', NULL, NULL, 'add_customer', 1),
(5, 5, 'manage_customer', NULL, NULL, 'manage_customer', 1),
(6, 0, 'credit_customer', NULL, NULL, 'credit_customer', 1),
(7, 0, 'paid_customer', NULL, NULL, 'paid_customer', 1),
(8, 0, 'customer_ledger', NULL, NULL, 'customer_ledger', 1),
(9, 0, 'customer_advance', NULL, NULL, 'customer_advance', 1),
(10, 3, 'category', NULL, NULL, 'category', 1),
(11, 3, 'manage_category', NULL, NULL, 'manage_category', 1),
(12, 3, 'unit', NULL, NULL, 'unit', 1),
(13, 3, 'manage_unit', NULL, NULL, 'manage_unit', 1),
(14, 3, 'add_product', NULL, NULL, 'create_product', 1),
(16, 3, 'manage_product', NULL, NULL, 'manage_product', 1),
(17, 4, 'add_supplier', NULL, NULL, 'add_supplier', 1),
(18, 4, 'manage_supplier', NULL, NULL, 'manage_supplier', 1),
(19, 0, 'supplier_ledger', NULL, NULL, 'supplier_ledger_report', 1),
(20, 0, 'supplier_advance', NULL, NULL, 'supplier_advance', 1),
(21, 9, 'add_purchase', NULL, NULL, 'add_purchase', 1),
(22, 9, 'manage_purchase', NULL, NULL, 'manage_purchase', 1),
(23, 8, 'stock_report', NULL, NULL, 'stock_report', 1),
(32, 8, 'sales_report', NULL, NULL, 'todays_sales_report', 1),
(34, 8, 'purchase_report', NULL, NULL, 'todays_purchase_report', 1),
(35, 8, 'purchase_report_category_wise', NULL, NULL, 'purchase_report_category_wise', 1),
(36, 8, 'product_wise_sales_report', NULL, NULL, 'product_wise_sales_report', 1),
(37, 8, 'sales_report_category_wise', NULL, NULL, 'sales_report_category_wise', 1),
(39, 8, 'user_wise_sales_report', NULL, NULL, 'user_wise_sales_report', 1),
(63, 0, 'bank_transaction', NULL, NULL, 'bank_transaction', 1),
(64, 0, 'bank_ledger', NULL, NULL, 'bank_ledger', 1),
(65, 15, 'tax_settings', NULL, NULL, 'tax_settings', 1),
(66, 0, 'add_incometax', NULL, NULL, 'add_incometax', 1),
(67, 0, 'manage_income_tax', NULL, NULL, 'manage_income_tax', 1),
(68, 0, 'tax_report', NULL, NULL, 'tax_report', 1),
(69, 0, 'invoice_wise_tax_report', NULL, NULL, 'invoice_wise_tax_report', 1),
(70, 12, 'add_designation', NULL, NULL, 'add_designation', 1),
(71, 12, 'manage_designation', NULL, NULL, 'manage_designation', 1),
(72, 12, 'add_employee', NULL, NULL, 'add_employee', 1),
(73, 12, 'manage_employee', NULL, NULL, 'manage_employee', 1),
(97, 2, 'add_service', NULL, NULL, 'create_service', 1),
(98, 2, 'manage_service', NULL, NULL, 'manage_service', 1),
(99, 11, 'service_invoice', NULL, NULL, 'service_invoice', 1),
(100, 11, 'manage_service_invoice', NULL, NULL, 'manage_service_invoice', 1),
(102, 15, 'manage_company', NULL, NULL, 'manage_company', 1),
(103, 15, 'add_user', NULL, NULL, 'add_user', 1),
(104, 15, 'manage_users', NULL, NULL, 'manage_user', 1),
(105, 15, 'language', NULL, NULL, 'add_language', 1),
(106, 15, 'currency', NULL, NULL, 'add_currency', 1),
(107, 15, 'setting', NULL, NULL, 'soft_setting', 1),
(108, 15, 'print_setting', NULL, NULL, 'print_setting', 1),
(109, 15, 'mail_setting', NULL, NULL, 'mail_setting', 1),
(110, 15, 'add_role', NULL, NULL, 'add_role', 1),
(111, 15, 'role_list', NULL, NULL, 'role_list', 1),
(112, 15, 'user_assign_role', NULL, NULL, 'user_assign', 1),
(113, 15, 'Permission', NULL, NULL, NULL, 1),
(115, 15, 'backup_restore', NULL, NULL, 'back_up', 1),
(116, 15, 'import', NULL, NULL, 'sql_import', 1),
(117, 15, 'restore', NULL, NULL, 'restore', 1),
(124, 15, 'vat_tax_setting', NULL, NULL, 'vat_tax_setting', 1),
(125, 20, 'add_payment_method', NULL, NULL, 'add_payment_method', 1),
(126, 20, 'payment_method_list', NULL, NULL, 'payment_method_list', 1),
(156, 1, 'storelist', NULL, NULL, 'storelist', 1),
(157, 1, 'add_store', NULL, NULL, 'add_store', 1),
(170, 24, 'new_grn', NULL, NULL, 'new_grn', 1),
(171, 24, 'manage_grn', NULL, NULL, 'manage_grn', 1),
(172, 24, 'new_gdn', NULL, NULL, 'new_gdn', 1),
(173, 24, 'manage_gdn', NULL, NULL, 'manage_gdn', 1),
(174, 8, 'cash_book', NULL, NULL, 'cash_book', 1),
(175, 8, 'live_stock_report', NULL, NULL, 'live_stock_report', 1),
(188, 8, 'product_wise_purchase_report', NULL, NULL, 'product_wise_purchase_report', 1),
(189, 8, 'stock_audit_report', NULL, NULL, 'stock_audit_report', 1),
(190, 21, 'add_branch', NULL, NULL, 'add_branch', 1),
(191, 21, 'branch_list', NULL, NULL, 'branch_list', 1),
(192, 15, 'user_assign_branch', NULL, NULL, 'user_assign_branch', 1),
(193, 15, 'user_assign_store', NULL, NULL, 'user_assign_store', 1),
(194, 10, 'new_pos', NULL, NULL, 'new_pos', 1),
(195, 10, 'new_quotation', NULL, NULL, 'new_quotation', 1),
(196, 10, 'manage_quotation', NULL, NULL, 'manage_quotation', 1),
(197, 3, 'brand_form', NULL, NULL, 'brand_form', 1),
(198, 3, 'brand_list', NULL, NULL, 'brand_list', 1),
(203, 3, 'subcategory_form', NULL, NULL, 'subcategory_form', 1),
(204, 3, 'subcategory_list', NULL, NULL, 'subcategory_list', 1),
(205, 6, 'add_stockbatch', NULL, NULL, 'add_stockbatch', 1),
(206, 6, 'stockbatchlist', NULL, NULL, 'stockbatchlist', 1),
(208, 10, 'new_sales_return', NULL, NULL, 'new_sales_return', 1),
(209, 10, 'manage_sales_return', NULL, NULL, 'manage_sales_return', 1),
(212, 9, 'new_purchase_order', NULL, NULL, 'new_purchase_order', 1),
(213, 9, 'manage_purchase_order', NULL, NULL, 'manage_purchase_order', 1),
(214, 9, 'new_purchase_return', NULL, NULL, 'new_purchase_return', 1),
(215, 9, 'manage_purchase_return', NULL, NULL, 'manage_purchase_return', 1),
(217, 3, 'add_product_group', NULL, NULL, 'add_product_group', 1),
(218, 3, 'product_grouplist', NULL, '', 'product_grouplist', 1),
(219, 8, 'sales_order_report', NULL, NULL, 'sales_order_report', 1),
(220, 8, 'sales_return_report', NULL, NULL, 'sales_return_report', 1),
(221, 8, 'purchase_order_report', NULL, NULL, 'purchase_order_report', 1),
(222, 8, 'purchase_return_report', NULL, NULL, 'purchase_return_report', 1),
(223, 8, 'grn_report', NULL, NULL, 'grn_report', 1),
(224, 8, 'gdn_report', NULL, NULL, 'gdn_report', 1),
(225, 8, 'gross_profit_report', NULL, NULL, 'gross_profit_report', 1),
(226, 8, 'gross_profit_category_report', NULL, NULL, 'gross_profit_category_report', 1),
(227, 11, 'serviceorder_invoice', NULL, NULL, 'serviceorder_invoice', 1),
(228, 11, 'manage_serviceorder_invoice', NULL, NULL, 'manage_serviceorder_invoice', 1),
(229, 8, 'service_report', NULL, NULL, 'service_report', 1),
(230, 8, 'purchase_report_productwise', NULL, NULL, 'purchase_report_productwise', 1),
(231, 3, 'labelprint', NULL, NULL, 'labelprint', 1),
(232, 8, 'product_batch_summary_report', NULL, NULL, 'product_batch_summary_report', 1),
(233, 8, 'service_order_report', NULL, NULL, 'service_order_report', 1),
(239, 15, 'add_company', NULL, NULL, 'add_company', 1),
(241, 25, 'payment_receipt_type_form', NULL, NULL, 'payment_receipt_type_form', 1),
(242, 25, 'payment_receipt_type_list', NULL, NULL, 'payment_receipt_type_list', 1),
(243, 25, 'new_payment_voucher', NULL, NULL, 'new_payment_voucher', 1),
(244, 25, 'manage_payment_voucher', NULL, NULL, 'manage_payment_voucher', 1),
(245, 25, 'new_receipt_voucher', NULL, NULL, 'new_receipt_voucher', 1),
(246, 25, 'manage_receipt_voucher', NULL, NULL, 'manage_receipt_voucher', 1),
(247, 25, 'new_contra_voucher', NULL, NULL, 'new_contra_voucher', 1),
(248, 25, 'manage_contra_voucher', NULL, NULL, 'manage_contra_voucher', 1),
(249, 8, 'payment_report', NULL, NULL, 'payment_report', 1),
(250, 8, 'receipt_report', NULL, NULL, 'receipt_report', 1),
(251, 8, 'contra_voucher_report', NULL, NULL, 'contra_voucher_report', 1),
(252, 26, 'stock_alert', NULL, NULL, 'stock_alert', 1),
(253, 26, 'expiry_alert', NULL, NULL, 'expiry_alert', 1),
(254, 6, 'new_inventory_transection', NULL, NULL, 'new_inventory_transection', 1),
(255, 6, 'manage_inventory_transection', NULL, NULL, 'manage_inventory_transection', 1),
(256, 22, 'data_uploader', NULL, NULL, 'data_uploader', 1),
(257, 10, 'touch_invoice', NULL, NULL, 'touch_invoice', 1),
(258, 8, 'profit_report_invoicewise', NULL, NULL, 'profit_report_invoicewise', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sub_module_re`
--

CREATE TABLE `sub_module_re` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `mid` int(10) UNSIGNED NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sub_module_re`
--

INSERT INTO `sub_module_re` (`id`, `name`, `mid`, `status`) VALUES
(1, 'dashboard', 1, 1),
(2, 'branch', 2, 1),
(3, 'store', 3, 1),
(4, 'brand', 4, 1),
(5, 'oop', 4, 1),
(6, 'category', 4, 1),
(7, 'subcategory', 4, 1),
(8, 'unit', 4, 1),
(9, 'product', 4, 1),
(10, 'product_group', 4, 1),
(11, 'conversion_ratio', 4, 1),
(12, 'service', 5, 1),
(13, 'company', 6, 1),
(14, 'users', 6, 1),
(15, 'role', 6, 1),
(16, 'assign_role', 6, 1),
(17, 'customer', 7, 1),
(18, 'purchase', 8, 1),
(19, 'purchase_order', 8, 1),
(20, 'user_access', 6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `supplier_information`
--

CREATE TABLE `supplier_information` (
  `supplier_id` bigint(20) NOT NULL,
  `supplier_name` varbinary(1000) NOT NULL,
  `address` varbinary(1000) DEFAULT NULL,
  `address2` varbinary(1000) NOT NULL,
  `mobile` varbinary(1000) DEFAULT NULL,
  `emailnumber` varbinary(1000) DEFAULT NULL,
  `email_address` varbinary(1000) DEFAULT NULL,
  `contact` varbinary(1000) DEFAULT NULL,
  `phone` varbinary(1000) DEFAULT NULL,
  `fax` varchar(100) DEFAULT NULL,
  `city` text DEFAULT NULL,
  `state` text DEFAULT NULL,
  `zip` varchar(50) DEFAULT NULL,
  `country` varchar(250) DEFAULT NULL,
  `details` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL,
  `supplier_calling_name` varbinary(2000) NOT NULL,
  `supplier_billing_name` varbinary(2000) NOT NULL,
  `nic_no` varbinary(2000) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `supplier_information`
--

INSERT INTO `supplier_information` (`supplier_id`, `supplier_name`, `address`, `address2`, `mobile`, `emailnumber`, `email_address`, `contact`, `phone`, `fax`, `city`, `state`, `zip`, `country`, `details`, `status`, `supplier_calling_name`, `supplier_billing_name`, `nic_no`) VALUES
(1, 0xbf868df4d6f344184ae396dc1a3a89bb, 0xc07ea54c5bebf537d9734e19b80e3873, 0xc07ea54c5bebf537d9734e19b80e3873, 0xc07ea54c5bebf537d9734e19b80e3873, 0xc07ea54c5bebf537d9734e19b80e3873, 0xc07ea54c5bebf537d9734e19b80e3873, 0xc07ea54c5bebf537d9734e19b80e3873, 0xc07ea54c5bebf537d9734e19b80e3873, '', '', '', '', '', NULL, 1, 0xbf868df4d6f344184ae396dc1a3a89bb, 0xbf868df4d6f344184ae396dc1a3a89bb, 0xc07ea54c5bebf537d9734e19b80e3873);

-- --------------------------------------------------------

--
-- Table structure for table `supplier_product`
--

CREATE TABLE `supplier_product` (
  `supplier_pr_id` int(11) NOT NULL,
  `product_id` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `products_model` varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `supplier_id` bigint(20) NOT NULL,
  `supplier_price` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `synchronizer_setting`
--

CREATE TABLE `synchronizer_setting` (
  `id` int(11) NOT NULL,
  `hostname` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `port` varchar(10) NOT NULL,
  `debug` varchar(10) NOT NULL,
  `project_root` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tax_collection`
--

CREATE TABLE `tax_collection` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `customer_id` varchar(30) NOT NULL,
  `relation_id` varchar(30) NOT NULL,
  `tax0` text DEFAULT NULL,
  `tax1` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tax_settings`
--

CREATE TABLE `tax_settings` (
  `id` int(11) NOT NULL,
  `default_value` float NOT NULL,
  `tax_name` varchar(250) NOT NULL,
  `nt` int(11) NOT NULL,
  `reg_no` varchar(100) NOT NULL,
  `is_show` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `tax_settings`
--

INSERT INTO `tax_settings` (`id`, `default_value`, `tax_name`, `nt`, `reg_no`, `is_show`) VALUES
(1, 12, 'VAT', 1, '12345678', 1);

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `unit_id` int(11) NOT NULL,
  `unit_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `status` int(11) NOT NULL,
  `unit_display_name` varchar(10000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`unit_id`, `unit_name`, `status`, `unit_display_name`) VALUES
(1, 'Qty', 1, 'Qty');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user_id` varchar(15) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `company_name` varchar(250) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `gender` int(11) DEFAULT NULL,
  `date_of_birth` varchar(255) DEFAULT NULL,
  `logo` varchar(250) DEFAULT NULL,
  `status` int(11) NOT NULL,
  `is_online` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_id`, `last_name`, `first_name`, `company_name`, `address`, `phone`, `gender`, `date_of_birth`, `logo`, `status`, `is_online`) VALUES
(1, '1', 'Admin', 'Super', NULL, NULL, NULL, NULL, NULL, './assets/img/user/2026-06-02/466ec36fd4e9de6a9221560283003644.png', 1, 1),
(34, '2680', '', 'System IMPL', NULL, NULL, NULL, NULL, NULL, '', 1, 0),
(51, '79048', '', 'test', NULL, NULL, NULL, NULL, NULL, '', 1, 0),
(70, '106152', '', 'Admin', NULL, NULL, NULL, NULL, NULL, '', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_login`
--

CREATE TABLE `user_login` (
  `id` int(11) NOT NULL,
  `user_id` varchar(15) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `user_type` int(11) DEFAULT NULL,
  `security_code` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL,
  `screen` int(11) NOT NULL,
  `startdate` date NOT NULL DEFAULT curdate(),
  `enddate` date NOT NULL DEFAULT curdate(),
  `temporary` int(11) NOT NULL DEFAULT 0,
  `encrypted_password` varbinary(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `user_login`
--

INSERT INTO `user_login` (`id`, `user_id`, `username`, `password`, `user_type`, `security_code`, `status`, `screen`, `startdate`, `enddate`, `temporary`, `encrypted_password`) VALUES
(1, '1', 'admin', '42157191a9bef4f97c5cf1a0faf487de', 1, NULL, 1, 1, '2026-01-01', '2026-01-01', 0, 0xf43a4a841f2b48be65a573ab6c5101c2),
(34, '2680', 'system', '06555f55aa389475cdb6a1c3e8331afc', 2, NULL, 1, 1, '2026-01-01', '2026-01-01', 0, 0x87c41a3ac6b22bd018ea73e1bb7aa081),
(51, '79048', 'test', '46195da05f08b1e08d03746a582f11bb', 9, NULL, 1, 1, '2026-06-06', '2026-06-06', 0, 0xe48630a7628ee5c32cf1d26faa69f282),
(70, '106152', 'admin', '4023fd6260a66a8b7f77d5a15657d21a', 2, NULL, 1, 11, '2026-01-01', '2026-01-01', 0, 0xd4dc428f9b3f56e415daeca684438ad4);

-- --------------------------------------------------------

--
-- Table structure for table `user_login_info`
--

CREATE TABLE `user_login_info` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `operating_system` varchar(255) DEFAULT NULL,
  `device_type` varchar(255) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user_login_info`
--

INSERT INTO `user_login_info` (`id`, `userid`, `browser`, `operating_system`, `device_type`, `ip_address`, `created_at`) VALUES
(1, 1, 'Chrome', 'MacOS', 'Desktop', '112.134.138.188', '2026-10-01 16:16:59'),
(2, 1, 'Chrome', 'MacOS', 'Desktop', '112.134.138.188', '2026-10-01 16:23:02'),
(3, 1, 'Chrome', 'MacOS', 'Desktop', '112.134.138.188', '2026-10-01 16:24:50'),
(4, 1, 'Chrome', 'MacOS', 'Desktop', '112.134.139.39', '2026-10-01 16:27:41'),
(5, 1, 'Chrome', 'MacOS', 'Desktop', '112.134.139.39', '2026-10-01 22:57:32'),
(6, 1, 'Chrome', 'MacOS', 'Desktop', '112.134.139.39', '2026-10-02 11:37:30'),
(7, 1, 'Chrome', 'MacOS', 'Desktop', '112.134.139.39', '2026-10-02 13:08:30'),
(8, 106152, 'Chrome', 'MacOS', 'Desktop', '112.134.139.39', '2026-10-02 13:08:40'),
(9, 1, 'Chrome', 'MacOS', 'Desktop', '112.134.139.39', '2026-10-02 13:09:10'),
(10, 1, 'Chrome', 'MacOS', 'Desktop', '112.134.139.39', '2026-10-02 14:37:55'),
(11, 1, 'Chrome', 'Windows 10 or 11', 'Desktop', '112.134.138.44', '2026-10-02 19:32:49');

-- --------------------------------------------------------

--
-- Table structure for table `user_online`
--

CREATE TABLE `user_online` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `fullname` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `last_seen` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `user_online`
--

INSERT INTO `user_online` (`id`, `user_id`, `fullname`, `email`, `last_seen`) VALUES
(217, 1, 'Super Admin', 'admin', '2026-10-02 19:33:05');

-- --------------------------------------------------------

--
-- Table structure for table `vat_tax_setting`
--

CREATE TABLE `vat_tax_setting` (
  `id` int(11) NOT NULL,
  `ischecked` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vat_tax_setting`
--

INSERT INTO `vat_tax_setting` (`id`, `ischecked`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `voucher`
--

CREATE TABLE `voucher` (
  `id` int(11) NOT NULL,
  `voucher_id` varbinary(2000) NOT NULL,
  `branch` int(11) NOT NULL,
  `type` int(20) NOT NULL,
  `from` int(20) NOT NULL,
  `date` varchar(255) NOT NULL,
  `remark` varchar(2000) NOT NULL,
  `total` varbinary(2000) NOT NULL,
  `type2` varbinary(2000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `voucher_details`
--

CREATE TABLE `voucher_details` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `to` int(20) NOT NULL,
  `sto` int(20) NOT NULL,
  `comment` varchar(2000) NOT NULL,
  `amount` varbinary(2000) NOT NULL,
  `type2` varbinary(2000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `web_setting`
--

CREATE TABLE `web_setting` (
  `setting_id` int(11) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `invoice_logo` varchar(255) DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `timezone` varchar(150) NOT NULL,
  `currency_position` varchar(10) DEFAULT NULL,
  `footer_text` varchar(255) DEFAULT NULL,
  `expiry_alert_days` int(11) NOT NULL DEFAULT 0,
  `language` varchar(255) DEFAULT NULL,
  `rtr` varchar(255) DEFAULT NULL,
  `captcha` int(11) DEFAULT 1 COMMENT '0=active,1=inactive',
  `is_qr` int(11) NOT NULL,
  `is_autoapprove_v` int(11) NOT NULL DEFAULT 0,
  `site_key` varchar(250) DEFAULT NULL,
  `secret_key` varchar(250) DEFAULT NULL,
  `discount_type` int(11) DEFAULT 1,
  `vk_enable` tinyint(1) NOT NULL DEFAULT 1,
  `pos_print_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=Normal,1=Thermal',
  `terms_conditions` text DEFAULT NULL,
  `product_image_upload` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `web_setting`
--

INSERT INTO `web_setting` (`setting_id`, `logo`, `invoice_logo`, `favicon`, `currency`, `timezone`, `currency_position`, `footer_text`, `expiry_alert_days`, `language`, `rtr`, `captcha`, `is_qr`, `is_autoapprove_v`, `site_key`, `secret_key`, `discount_type`, `vk_enable`, `pos_print_type`, `terms_conditions`, `product_image_upload`) VALUES
(1, 'assets/img/icons/2026-03-18/396886f4ae6b87690cfb3215f439ea58.png', 'assets/img/icons/2026-06-09/117889f0bfb1f6718ec542f4405b5fdf.jpg', 'assets/img/icons/2025-12-04/05653a7c175693937a7e182603b6ccf6.png', 'LKR', 'Asia/Kolkata', '0', 'n/a', 90, 'english', '0', 1, 0, 1, '6LdiKhsUAAAAAMV4jQRmNYdZy2kXEuFe1HrdP5tt', '6LdiKhsUAAAAAMV4jQRmNYdZy2kXEuFe1HrdP5tt', 1, 0, 0, '', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `acc_coa`
--
ALTER TABLE `acc_coa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `HeadCode` (`HeadCode`),
  ADD KEY `HeadName` (`HeadName`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `PHeadName` (`PHeadName`),
  ADD KEY `HeadLevel` (`HeadLevel`),
  ADD KEY `IsTransaction` (`IsTransaction`),
  ADD KEY `IsGL` (`IsGL`),
  ADD KEY `IsBudget` (`IsBudget`),
  ADD KEY `IsDepreciation` (`IsDepreciation`),
  ADD KEY `isCashNature` (`isCashNature`),
  ADD KEY `isBankNature` (`isBankNature`);

--
-- Indexes for table `acc_monthly_balance`
--
ALTER TABLE `acc_monthly_balance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `acc_opening_balance`
--
ALTER TABLE `acc_opening_balance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `COAID` (`COAID`),
  ADD KEY `year` (`fyear`);

--
-- Indexes for table `acc_predefine_account`
--
ALTER TABLE `acc_predefine_account`
  ADD PRIMARY KEY (`id`),
  ADD KEY `COAID` (`cashCode`),
  ADD KEY `cashCode` (`cashCode`);

--
-- Indexes for table `acc_subcode`
--
ALTER TABLE `acc_subcode`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subTypeId` (`subTypeId`);

--
-- Indexes for table `acc_subtype`
--
ALTER TABLE `acc_subtype`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `acc_transaction`
--
ALTER TABLE `acc_transaction`
  ADD UNIQUE KEY `ID` (`ID`),
  ADD KEY `COAID` (`COAID`),
  ADD KEY `VNo` (`VNo`),
  ADD KEY `RevCodde` (`RevCodde`),
  ADD KEY `subType` (`subType`),
  ADD KEY `subCode` (`subCode`),
  ADD KEY `referenceNo` (`referenceNo`),
  ADD KEY `vid` (`vid`);

--
-- Indexes for table `acc_vaucher`
--
ALTER TABLE `acc_vaucher`
  ADD PRIMARY KEY (`id`),
  ADD KEY `VNo` (`VNo`),
  ADD KEY `fyear` (`fyear`),
  ADD KEY `VDate` (`VDate`),
  ADD KEY `COAID` (`COAID`),
  ADD KEY `RevCodde` (`RevCodde`),
  ADD KEY `subType` (`subType`),
  ADD KEY `subCode` (`subCode`),
  ADD KEY `referenceNo` (`referenceNo`);

--
-- Indexes for table `adj_stock`
--
ALTER TABLE `adj_stock`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app_setting`
--
ALTER TABLE `app_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `audit_stock`
--
ALTER TABLE `audit_stock`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bank_add`
--
ALTER TABLE `bank_add`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `branch`
--
ALTER TABLE `branch`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bulk_branch_details`
--
ALTER TABLE `bulk_branch_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bulk_brand_details`
--
ALTER TABLE `bulk_brand_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bulk_category_details`
--
ALTER TABLE `bulk_category_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bulk_conversionratio_details`
--
ALTER TABLE `bulk_conversionratio_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bulk_customer_details`
--
ALTER TABLE `bulk_customer_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bulk_designation_details`
--
ALTER TABLE `bulk_designation_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bulk_details`
--
ALTER TABLE `bulk_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploadedby` (`uploadedby`);

--
-- Indexes for table `bulk_employee_details`
--
ALTER TABLE `bulk_employee_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bulk_openingstock_details`
--
ALTER TABLE `bulk_openingstock_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bulk_paymentmethod_details`
--
ALTER TABLE `bulk_paymentmethod_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bulk_productgroup_details`
--
ALTER TABLE `bulk_productgroup_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bulk_product_details`
--
ALTER TABLE `bulk_product_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bulk_service_details`
--
ALTER TABLE `bulk_service_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bulk_stockadjustment_details`
--
ALTER TABLE `bulk_stockadjustment_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bulk_stockbatch_details`
--
ALTER TABLE `bulk_stockbatch_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bulk_store_details`
--
ALTER TABLE `bulk_store_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bulk_subcategory_details`
--
ALTER TABLE `bulk_subcategory_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bulk_supplier_details`
--
ALTER TABLE `bulk_supplier_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bulk_unit_details`
--
ALTER TABLE `bulk_unit_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bulk_upload_csv`
--
ALTER TABLE `bulk_upload_csv`
  ADD PRIMARY KEY (`id`),
  ADD KEY `batch_lookup` (`batch_table`,`batch_id`);

--
-- Indexes for table `company_information`
--
ALTER TABLE `company_information`
  ADD PRIMARY KEY (`company_id`);

--
-- Indexes for table `conversion_ratio`
--
ALTER TABLE `conversion_ratio`
  ADD PRIMARY KEY (`conversionratio_id`);

--
-- Indexes for table `currency_tbl`
--
ALTER TABLE `currency_tbl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_information`
--
ALTER TABLE `customer_information`
  ADD PRIMARY KEY (`customer_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `designation`
--
ALTER TABLE `designation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_config`
--
ALTER TABLE `email_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_history`
--
ALTER TABLE `employee_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `financial_year`
--
ALTER TABLE `financial_year`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gdn_stock`
--
ALTER TABLE `gdn_stock`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `grn_stock`
--
ALTER TABLE `grn_stock`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `invoice_details`
--
ALTER TABLE `invoice_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `language`
--
ALTER TABLE `language`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `module`
--
ALTER TABLE `module`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `module_re`
--
ALTER TABLE `module_re`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_receipt_type`
--
ALTER TABLE `payment_receipt_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_type`
--
ALTER TABLE `payment_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `phystock_details`
--
ALTER TABLE `phystock_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product` (`product`),
  ADD KEY `pid` (`pid`),
  ADD KEY `phystock_details_ibfk_1` (`store`);

--
-- Indexes for table `print_setting`
--
ALTER TABLE `print_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_brand`
--
ALTER TABLE `product_brand`
  ADD PRIMARY KEY (`brand_id`);

--
-- Indexes for table `product_category`
--
ALTER TABLE `product_category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `product_group`
--
ALTER TABLE `product_group`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_group_details`
--
ALTER TABLE `product_group_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pid` (`pid`),
  ADD KEY `product` (`product`),
  ADD KEY `unit` (`unit`);

--
-- Indexes for table `product_information`
--
ALTER TABLE `product_information`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `store` (`store`),
  ADD KEY `subcategory_id` (`subcategory_id`),
  ADD KEY `brand_id` (`brand_id`),
  ADD KEY `oop_id` (`oop_id`),
  ADD KEY `idx_product_information_supplier_id` (`supplier_id`);

--
-- Indexes for table `product_oop`
--
ALTER TABLE `product_oop`
  ADD PRIMARY KEY (`oop_id`);

--
-- Indexes for table `product_purchase`
--
ALTER TABLE `product_purchase`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_id` (`purchase_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `product_purchase_details`
--
ALTER TABLE `product_purchase_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_id` (`purchase_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_return`
--
ALTER TABLE `product_return`
  ADD KEY `product_id` (`product_id`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `purchase_id` (`purchase_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `product_service`
--
ALTER TABLE `product_service`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `product_subcategory`
--
ALTER TABLE `product_subcategory`
  ADD PRIMARY KEY (`subcategory_id`);

--
-- Indexes for table `purchase`
--
ALTER TABLE `purchase`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `purchase_details`
--
ALTER TABLE `purchase_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `store` (`store`),
  ADD KEY `product` (`product`),
  ADD KEY `pid` (`pid`);

--
-- Indexes for table `purchase_order`
--
ALTER TABLE `purchase_order`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchase_order_details`
--
ALTER TABLE `purchase_order_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchase_return`
--
ALTER TABLE `purchase_return`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchase_return_details`
--
ALTER TABLE `purchase_return_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_permission`
--
ALTER TABLE `role_permission`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_module_id` (`fk_module_id`),
  ADD KEY `fk_user_id` (`role_id`);

--
-- Indexes for table `role_permission_re`
--
ALTER TABLE `role_permission_re`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_module` (`module_id`),
  ADD KEY `idx_submodule` (`submodule_id`);

--
-- Indexes for table `sale`
--
ALTER TABLE `sale`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `sales_order`
--
ALTER TABLE `sales_order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `sales_order_details`
--
ALTER TABLE `sales_order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `store` (`store`),
  ADD KEY `product` (`product`),
  ADD KEY `pid` (`pid`);

--
-- Indexes for table `sales_order_group_details`
--
ALTER TABLE `sales_order_group_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pid` (`pid`),
  ADD KEY `groupid` (`groupid`),
  ADD KEY `unit` (`unit`);

--
-- Indexes for table `sales_return`
--
ALTER TABLE `sales_return`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales_return_details`
--
ALTER TABLE `sales_return_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sale_details`
--
ALTER TABLE `sale_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `store` (`store`),
  ADD KEY `product` (`product`),
  ADD KEY `pid` (`pid`);

--
-- Indexes for table `sale_group_details`
--
ALTER TABLE `sale_group_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pid` (`pid`),
  ADD KEY `groupid` (`groupid`),
  ADD KEY `unit` (`unit`);

--
-- Indexes for table `sec_branch`
--
ALTER TABLE `sec_branch`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sec_branch_re`
--
ALTER TABLE `sec_branch_re`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sec_branch_re_user_id_branch_id_unique` (`user_id`,`branch_id`);

--
-- Indexes for table `sec_role`
--
ALTER TABLE `sec_role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sec_store`
--
ALTER TABLE `sec_store`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sec_store_re`
--
ALTER TABLE `sec_store_re`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sec_store_re_user_id_store_id_unique` (`user_id`,`store_id`);

--
-- Indexes for table `sec_userrole`
--
ALTER TABLE `sec_userrole`
  ADD UNIQUE KEY `ID` (`id`);

--
-- Indexes for table `sec_userrole_re`
--
ALTER TABLE `sec_userrole_re`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_user_role` (`user_id`,`role_type`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_role_type` (`role_type`);

--
-- Indexes for table `seles_termscondi`
--
ALTER TABLE `seles_termscondi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `service_details`
--
ALTER TABLE `service_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service` (`service`),
  ADD KEY `pid` (`pid`);

--
-- Indexes for table `service_invoice`
--
ALTER TABLE `service_invoice`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `service_invoice_details`
--
ALTER TABLE `service_invoice_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `service_order`
--
ALTER TABLE `service_order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `service_order_details`
--
ALTER TABLE `service_order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service` (`service`),
  ADD KEY `pid` (`pid`);

--
-- Indexes for table `sms_settings`
--
ALTER TABLE `sms_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stockbatch`
--
ALTER TABLE `stockbatch`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stockbatchopening`
--
ALTER TABLE `stockbatchopening`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_details`
--
ALTER TABLE `stock_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product` (`product`),
  ADD KEY `pid` (`pid`),
  ADD KEY `stock_details_ibfk_1` (`store`);

--
-- Indexes for table `store`
--
ALTER TABLE `store`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subunit_product`
--
ALTER TABLE `subunit_product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `unit_id` (`unit_id`);

--
-- Indexes for table `sub_module`
--
ALTER TABLE `sub_module`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sub_module_re`
--
ALTER TABLE `sub_module_re`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sub_module_mid` (`mid`);

--
-- Indexes for table `supplier_information`
--
ALTER TABLE `supplier_information`
  ADD PRIMARY KEY (`supplier_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `supplier_product`
--
ALTER TABLE `supplier_product`
  ADD PRIMARY KEY (`supplier_pr_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `tax_collection`
--
ALTER TABLE `tax_collection`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `tax_settings`
--
ALTER TABLE `tax_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`unit_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_login`
--
ALTER TABLE `user_login`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_login_info`
--
ALTER TABLE `user_login_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_online`
--
ALTER TABLE `user_online`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `vat_tax_setting`
--
ALTER TABLE `vat_tax_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `voucher`
--
ALTER TABLE `voucher`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `voucher_details`
--
ALTER TABLE `voucher_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `web_setting`
--
ALTER TABLE `web_setting`
  ADD PRIMARY KEY (`setting_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `acc_coa`
--
ALTER TABLE `acc_coa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=253;

--
-- AUTO_INCREMENT for table `acc_monthly_balance`
--
ALTER TABLE `acc_monthly_balance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `acc_opening_balance`
--
ALTER TABLE `acc_opening_balance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `acc_predefine_account`
--
ALTER TABLE `acc_predefine_account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `acc_subcode`
--
ALTER TABLE `acc_subcode`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `acc_transaction`
--
ALTER TABLE `acc_transaction`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `acc_vaucher`
--
ALTER TABLE `acc_vaucher`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `adj_stock`
--
ALTER TABLE `adj_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `app_setting`
--
ALTER TABLE `app_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `audit_stock`
--
ALTER TABLE `audit_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bank_add`
--
ALTER TABLE `bank_add`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `branch`
--
ALTER TABLE `branch`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bulk_branch_details`
--
ALTER TABLE `bulk_branch_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bulk_brand_details`
--
ALTER TABLE `bulk_brand_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bulk_category_details`
--
ALTER TABLE `bulk_category_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bulk_conversionratio_details`
--
ALTER TABLE `bulk_conversionratio_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bulk_customer_details`
--
ALTER TABLE `bulk_customer_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bulk_designation_details`
--
ALTER TABLE `bulk_designation_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bulk_details`
--
ALTER TABLE `bulk_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bulk_employee_details`
--
ALTER TABLE `bulk_employee_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bulk_openingstock_details`
--
ALTER TABLE `bulk_openingstock_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bulk_paymentmethod_details`
--
ALTER TABLE `bulk_paymentmethod_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bulk_productgroup_details`
--
ALTER TABLE `bulk_productgroup_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bulk_product_details`
--
ALTER TABLE `bulk_product_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bulk_service_details`
--
ALTER TABLE `bulk_service_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bulk_stockadjustment_details`
--
ALTER TABLE `bulk_stockadjustment_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bulk_stockbatch_details`
--
ALTER TABLE `bulk_stockbatch_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bulk_store_details`
--
ALTER TABLE `bulk_store_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bulk_subcategory_details`
--
ALTER TABLE `bulk_subcategory_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bulk_supplier_details`
--
ALTER TABLE `bulk_supplier_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bulk_unit_details`
--
ALTER TABLE `bulk_unit_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bulk_upload_csv`
--
ALTER TABLE `bulk_upload_csv`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `company_information`
--
ALTER TABLE `company_information`
  MODIFY `company_id` int(250) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `conversion_ratio`
--
ALTER TABLE `conversion_ratio`
  MODIFY `conversionratio_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `currency_tbl`
--
ALTER TABLE `currency_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `customer_information`
--
ALTER TABLE `customer_information`
  MODIFY `customer_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `designation`
--
ALTER TABLE `designation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `email_config`
--
ALTER TABLE `email_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `employee_history`
--
ALTER TABLE `employee_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `financial_year`
--
ALTER TABLE `financial_year`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `gdn_stock`
--
ALTER TABLE `gdn_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grn_stock`
--
ALTER TABLE `grn_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_details`
--
ALTER TABLE `invoice_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `language`
--
ALTER TABLE `language`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1223;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2142;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `module`
--
ALTER TABLE `module`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `module_re`
--
ALTER TABLE `module_re`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `payment_receipt_type`
--
ALTER TABLE `payment_receipt_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `payment_type`
--
ALTER TABLE `payment_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phystock_details`
--
ALTER TABLE `phystock_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `print_setting`
--
ALTER TABLE `print_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `product_brand`
--
ALTER TABLE `product_brand`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_category`
--
ALTER TABLE `product_category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_group`
--
ALTER TABLE `product_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_group_details`
--
ALTER TABLE `product_group_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_information`
--
ALTER TABLE `product_information`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_oop`
--
ALTER TABLE `product_oop`
  MODIFY `oop_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_purchase`
--
ALTER TABLE `product_purchase`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_purchase_details`
--
ALTER TABLE `product_purchase_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_service`
--
ALTER TABLE `product_service`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_subcategory`
--
ALTER TABLE `product_subcategory`
  MODIFY `subcategory_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase`
--
ALTER TABLE `purchase`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_details`
--
ALTER TABLE `purchase_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_order`
--
ALTER TABLE `purchase_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_order_details`
--
ALTER TABLE `purchase_order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_return`
--
ALTER TABLE `purchase_return`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_return_details`
--
ALTER TABLE `purchase_return_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `role_permission`
--
ALTER TABLE `role_permission`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8120;

--
-- AUTO_INCREMENT for table `role_permission_re`
--
ALTER TABLE `role_permission_re`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `sale`
--
ALTER TABLE `sale`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_order`
--
ALTER TABLE `sales_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_order_details`
--
ALTER TABLE `sales_order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_order_group_details`
--
ALTER TABLE `sales_order_group_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_return`
--
ALTER TABLE `sales_return`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_return_details`
--
ALTER TABLE `sales_return_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sale_details`
--
ALTER TABLE `sale_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sale_group_details`
--
ALTER TABLE `sale_group_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sec_branch`
--
ALTER TABLE `sec_branch`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sec_branch_re`
--
ALTER TABLE `sec_branch_re`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sec_role`
--
ALTER TABLE `sec_role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `sec_store`
--
ALTER TABLE `sec_store`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sec_store_re`
--
ALTER TABLE `sec_store_re`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sec_userrole`
--
ALTER TABLE `sec_userrole`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `sec_userrole_re`
--
ALTER TABLE `sec_userrole_re`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `seles_termscondi`
--
ALTER TABLE `seles_termscondi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `service`
--
ALTER TABLE `service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_details`
--
ALTER TABLE `service_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_invoice`
--
ALTER TABLE `service_invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_invoice_details`
--
ALTER TABLE `service_invoice_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_order`
--
ALTER TABLE `service_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_order_details`
--
ALTER TABLE `service_order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sms_settings`
--
ALTER TABLE `sms_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stockbatch`
--
ALTER TABLE `stockbatch`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stockbatchopening`
--
ALTER TABLE `stockbatchopening`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_details`
--
ALTER TABLE `stock_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `store`
--
ALTER TABLE `store`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;

--
-- AUTO_INCREMENT for table `subunit_product`
--
ALTER TABLE `subunit_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sub_module`
--
ALTER TABLE `sub_module`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=259;

--
-- AUTO_INCREMENT for table `sub_module_re`
--
ALTER TABLE `sub_module_re`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `supplier_information`
--
ALTER TABLE `supplier_information`
  MODIFY `supplier_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `supplier_product`
--
ALTER TABLE `supplier_product`
  MODIFY `supplier_pr_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tax_collection`
--
ALTER TABLE `tax_collection`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tax_settings`
--
ALTER TABLE `tax_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `user_login`
--
ALTER TABLE `user_login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `user_login_info`
--
ALTER TABLE `user_login_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `user_online`
--
ALTER TABLE `user_online`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=218;

--
-- AUTO_INCREMENT for table `vat_tax_setting`
--
ALTER TABLE `vat_tax_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `voucher`
--
ALTER TABLE `voucher`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `voucher_details`
--
ALTER TABLE `voucher_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `web_setting`
--
ALTER TABLE `web_setting`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `phystock_details`
--
ALTER TABLE `phystock_details`
  ADD CONSTRAINT `phystock_details_ibfk_1` FOREIGN KEY (`store`) REFERENCES `store` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
