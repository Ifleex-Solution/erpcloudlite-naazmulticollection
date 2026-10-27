INSERT INTO `module` (`id`, `name`, `description`, `image`, `directory`, `status`) 
VALUES (NULL, 'warehouse_management', NULL, NULL, 'warehouse_management', '1');


INSERT INTO `language` (`id`, `phrase`, `english`) VALUES (NULL, 'warehouse_management', 'Warehouse Management');


UPDATE `sub_module` SET `mid` = '24' WHERE `sub_module`.`id` = 173;
UPDATE `sub_module` SET `mid` = '24' WHERE `sub_module`.`id` = 172;
UPDATE `sub_module` SET `mid` = '24' WHERE `sub_module`.`id` = 171;
UPDATE `sub_module` SET `mid` = '24' WHERE `sub_module`.`id` = 170;
