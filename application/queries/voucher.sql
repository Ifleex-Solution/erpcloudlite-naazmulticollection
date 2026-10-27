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
    `lastupdateddate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `audit_stock`
  ADD PRIMARY KEY (`id`);
  
ALTER TABLE `audit_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;


  SELECT 
    product,
    date,
    scenario,
    incident,

    AES_DECRYPT(pvoucher, 'Th@#981420314') AS pvoucher,
    AES_DECRYPT(voucher, 'Th@#981420314') AS voucher,

    pid,
    store,

    AES_DECRYPT(astockstr, 'Th@#981420314') AS astockstr,
    AES_DECRYPT(pstockstr, 'Th@#981420314') AS pstockstr,

    AES_DECRYPT(astock, 'Th@#981420314') AS astock,
    AES_DECRYPT(pstock, 'Th@#981420314') AS pstock,

    lastupdateddate

FROM audit_stock;