CREATE TABLE `coreshop_carriers` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `delay` int NULL,
  `grade` int NOT NULL DEFAULT '1',
  `image` int NULL,
  `trackingUrl` varchar(512) NULL,
  `isFree` tinyint NOT NULL DEFAULT '0',
  `shippingMethod` enum('price','weight') NOT NULL,
  `tax` int NULL,
  `rangeBehaviour` enum('largest','deactivate') NOT NULL,
  `maxHeight` double NOT NULL DEFAULT '0',
  `maxWidth` double NOT NULL DEFAULT '0',
  `maxDepth` double NOT NULL DEFAULT '0',
  `maxWeidht` double NOT NULL DEFAULT '0'
) COMMENT='';

CREATE TABLE `coreshop_carriers_range_weight` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `carrier` int NOT NULL,
  `delimiter1` double NOT NULL,
  `delimiter2` double NOT NULL
) COMMENT='';

CREATE TABLE `coreshop_carriers_delivery_price` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `carrier` int NOT NULL,
  `range` int NOT NULL,
  `rangeType` enum('price','weight') NOT NULL,
  `price` double NOT NULL
) COMMENT='';