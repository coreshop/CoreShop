CREATE TABLE `coreshop_pricerules` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(50) NULL,
  `code` varchar(50) NULL,
  `label` text NULL,
  `description` text NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `highlight` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `conditions` text NULL,
  `actions` text NULL
) COMMENT='';