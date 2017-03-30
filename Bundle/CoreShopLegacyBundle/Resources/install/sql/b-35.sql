CREATE TABLE `coreshop_indexes` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `type` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `config` text NOT NULL
);

CREATE TABLE `coreshop_product_filters` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(255) NOT NULL,
  `resultsPerPage` int(11) NOT NULL DEFAULT '15',
  `order` varchar(255) NOT NULL,
  `orderKey` varchar(255) NOT NULL,
  `preConditions` text NOT NULL,
  `filters` text NOT NULL,
  `index` int(11) NULL
);