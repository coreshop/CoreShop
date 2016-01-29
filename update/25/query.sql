
DROP TABLE IF EXISTS `coreshop_product_specificprice`;
CREATE TABLE `coreshop_product_specificprice` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `o_id` int NOT NULL,
  `name` varchar(50) NULL,
  `conditions` text NULL,
  `actions` text NULL
) COMMENT='';