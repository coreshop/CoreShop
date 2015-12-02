CREATE TABLE `coreshop_currency` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `isoCode` varchar(255) DEFAULT NULL,
  `numericIsoCode` varchar(255) DEFAULT NULL,
  `symbol` varchar(255) DEFAULT NULL,
  `exchangeRate` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;