DROP TABLE IF EXISTS `coreshop_carriers`;
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
  `taxRuleGroupId` INT(11) NULL DEFAULT NULL,
  `rangeBehaviour` enum('largest','deactivate') NOT NULL,
  `maxHeight` double NOT NULL DEFAULT '0',
  `maxWidth` double NOT NULL DEFAULT '0',
  `maxDepth` double NOT NULL DEFAULT '0',
  `maxWeight` double NOT NULL DEFAULT '0',
  `class` varchar(255) NULL
) COMMENT='';

DROP TABLE IF EXISTS `coreshop_countries`;
CREATE TABLE `coreshop_countries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `isoCode` varchar(2) NULL,
  `active` tinyint(1) DEFAULT 0,
  `currencyId` int,
  `useStoreCurrency` int(11) NOT NULL DEFAULT 0,
  `zoneId` int,
  `addressFormat` VARCHAR(255) NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `coreshop_currencies`;
CREATE TABLE `coreshop_currencies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `isoCode` varchar(255) DEFAULT NULL,
  `numericIsoCode` varchar(255) DEFAULT NULL,
  `symbol` varchar(255) DEFAULT NULL,
  `exchangeRate` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT INTO `coreshop_currencies` (`id`, `name`, `isoCode`, `numericIsoCode`, `symbol`, `exchangeRate`) VALUES
(1,	'Euro',	'EUR',	'978',	'€',	1.2),
(6,	'UAE Dirham',	'AED',	'784',	'د.إ',	1),
(7,	'East Caribbean Dollar',	'XCD',	'951',	'$',	1),
(8,	'Netherlands Antilles Guilder',	'ANG',	'532',	'ƒ',	1),
(9,	'US Dollars',	'USD',	'840',	'$',	1),
(10,	'Australian Dollars',	'AUD',	'036',	'$',	1),
(11,	'Aruban Florin',	'AWG',	'533',	'ƒ',	1),
(12,	'Azerbaijanian Manat',	'AZN',	'944',	'ман',	1),
(13,	'Marka',	'BAM',	'977',	'KM',	1),
(14,	'Barbados Dollar',	'BBD',	'052',	'$',	1),
(15,	'Bangladeshi Taka',	'BDT',	'050',	'৳',	1),
(16,	'Brazilian Real',	'BRL',	'986',	'R$',	1),
(17,	'Bahamian Dollar',	'BSD',	'044',	'$',	1),
(18,	'Norwegian Krone',	'NOK',	'578',	'kr',	1),
(19,	'Belarussian Rouble',	'BYR',	'974',	'p.',	1),
(20,	'Canadian Dollars',	'CAD',	'124',	'$',	1),
(21,	'New Zealand Dollars',	'NZD',	'554',	'$',	1),
(22,	'Chinese Renminbi Yuan',	'CNY',	'156',	'¥',	1),
(23,	'Costa Rican Colon',	'CRC',	'188',	'₡',	1),
(24,	'Czech Koruna',	'CZK',	'203',	'Kč',	1),
(25,	'Danish Krone',	'DKK',	'208',	'kr',	1),
(26,	'Dominican Republic Peso',	'DOP',	'214',	'RD$',	1),
(27,	'Estonia Kroon',	'EEK',	'233',	'kr',	1),
(28,	'Egyptian Pound',	'EGP',	'818',	'£',	1),
(29,	'Eritrean Nakfa',	'ERN',	'232',	'Nfa',	1),
(30,	'British Pounds Sterling',	'GBP',	'826',	'£',	1),
(31,	'Georgian Lari',	'GEL',	'981',	'GEL',	1),
(32,	'Ghana Cedi',	'GHC',	'288',	'¢',	1),
(33,	'Gibraltar Pound',	'GIP',	'292',	'£',	1),
(34,	'Guatemala Quetzal',	'GTQ',	'320',	'Q',	1),
(35,	'Hong Kong Dollars',	'HKD',	'344',	'$',	1),
(36,	'Honduras Lempira',	'HNL',	'340',	'L',	1),
(37,	'Croatia Kuna',	'HRK',	'191',	'kn',	1),
(38,	'Hungary Forint',	'HUF',	'348',	'Ft',	1),
(39,	'Indonesia Rupiah',	'IDR',	'360',	'Rp',	1),
(40,	'Israel Shekel',	'ILS',	'376',	'₪',	1),
(41,	'Indian Rupee',	'INR',	'356',	'₹',	1),
(42,	'Jamaican Dollar',	'JMD',	'388',	'J$',	1),
(43,	'Japanese yen',	'JPY',	'392',	'¥',	1),
(44,	'Kenyan Shilling',	'KES',	'404',	'KSh',	1),
(45,	'Cambodia Riel',	'KHR',	'116',	'៛',	1),
(46,	'North Korean Won',	'KPW',	'408',	'₩',	1),
(47,	'South Korean Won',	'KRW',	'410',	'₩',	1),
(48,	'Kuwaiti Dinar',	'KWD',	'414',	'د.ك',	1),
(49,	'Cayman Islands Dollar',	'KYD',	'136',	'$',	1),
(50,	'Kazakhstan Tenge',	'KZT',	'368',	'лв',	1),
(51,	'Lebanese Pound',	'LBP',	'422',	'£',	1),
(52,	'Swiss Francs',	'CHF',	'756',	'CHF',	1),
(53,	'Sri Lankan Rupee',	'LKR',	'144',	'₨',	1),
(54,	'South African Rand',	'ZAR',	'710',	'R',	1),
(55,	'Lithuania Litas',	'LTL',	'440',	'Lt',	1),
(56,	'Latvia Lat',	'LVL',	'428',	'Ls',	1),
(57,	'Macau Pataca',	'MOP',	'446',	'MOP$',	1),
(58,	'Mauritius Rupee',	'MUR',	'480',	'₨',	1),
(59,	'Malaysian Ringgit',	'MYR',	'458',	'RM',	1),
(60,	'Nigeria Naira',	'NGN',	'566',	'₦',	1),
(61,	'Nicaragua Cordoba',	'NIO',	'558',	'C$',	1),
(62,	'Nepalese Rupee',	'NPR',	'524',	'₨',	1),
(63,	'Omani Riyal',	'OMR',	'512',	'﷼',	1),
(64,	'Philippine Pesos',	'PHP',	'608',	'₱',	1),
(65,	'Pakistani Rupee',	'PKR',	'586',	'₨',	1),
(66,	'Paraguay Guarani',	'PYG',	'600',	'Gs',	1),
(67,	'Qatari Riyal',	'QAR',	'634',	'﷼',	1),
(68,	'Russian Federation Ruble',	'RUB',	'643',	'руб',	1),
(69,	'Saudi Riyal',	'SAR',	'682',	'﷼',	1),
(70,	'Seychelles Rupee',	'SCR',	'690',	'₨',	1),
(71,	'Swedish Krona',	'SEK',	'752',	'kr',	1),
(72,	'Singapore Dollars',	'SGD',	'702',	'$',	1),
(73,	'Thai Baht',	'THB',	'764',	'฿',	1),
(74,	'Trinidad and Tobago Dollar',	'TTD',	'780',	'TT$',	1),
(75,	'New Taiwanese Dollars',	'TWD',	'901',	'NT$',	1),
(76,	'Tanzanian Shilling',	'TZS',	'834',	'x/y',	1),
(77,	'United States dollar ',	'USS',	'998',	'$',	0.8),
(78,	'Uzbekistani Som',	'UZS',	'860',	'лв',	1);

INSERT INTO `coreshop_countries` (`id`, `name`, `isoCode`, `active`, `currencyId`, `useStoreCurrency`, `zoneId`, `addressFormat`) VALUES
(1, 'Germany', 'DE', 1, 1, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(2, 'Austria', 'AT', 1, 1, 1, 1, '%Text(company);\n%Text(firstname); %Text(lastname);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(3, 'Belgium', 'BE', 0, 1, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(4, 'Canada', 'CA', 0, 20, 1, 2, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(5, 'China', 'CN', 0, 22, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(6, 'Spain', 'ES', 0, NULL, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(7, 'Finland', 'FI', 0, NULL, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(8, 'France', 'FR', 0, NULL, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(9, 'Greece', 'GR', 0, NULL, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(10, 'Italy', 'IT', 0, NULL, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(11, 'Japan', 'JP', 0, 43, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(12, 'Luxemburg', 'LU', 0, NULL, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(13, 'Netherlands', 'NL', 0, 1, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(14, 'Poland', 'PL', 0, NULL, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(15, 'Portugal', 'PT', 0, NULL, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(16, 'Czech Republic', 'CZ', 0, 24, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(17, 'United Kingdom', 'GB', 1, 30, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(street); %Text(nr);\n%Text(city);\n%Object(state,{"method" : "getName"});\n%Text(zip);\n%Object(country,{"method" : "getName"});'),
(18, 'Sweden', 'SE', 0, 71, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(19, 'Switzerland', 'CH', 0, NULL, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(20, 'Denmark', 'DK', 0, 25, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(21, 'United States', 'US', 1, 9, 1, 2, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(street); %Text(nr); %Text(city);, %Object(state,{"method" : "getName"}); %Text(zip); \n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(22, 'HongKong', 'HK', 0, 35, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(23, 'Norway', 'NO', 0, 18, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(24, 'Australia', 'AU', 1, 10, 1, 5, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(25, 'Singapore', 'SG', 0, 72, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(26, 'Ireland', 'IE', 0, NULL, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(27, 'New Zealand', 'NZ', 0, 21, 1, 5, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(28, 'South Korea', 'KR', 0, 47, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(29, 'Israel', 'IL', 0, 40, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(30, 'South Africa', 'ZA', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(31, 'Nigeria', 'NG', 0, 60, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(32, 'Ivory Coast', 'CI', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(33, 'Togo', 'TG', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(34, 'Bolivia', 'BO', 0, NULL, 1, 6, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(35, 'Mauritius', 'MU', 0, 58, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(36, 'Romania', 'RO', 0, NULL, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(37, 'Slovakia', 'SK', 0, NULL, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(38, 'Algeria', 'DZ', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(39, 'American Samoa', 'AS', 0, 9, 1, 2, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(40, 'Andorra', 'AD', 0, 1, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(41, 'Angola', 'AO', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(42, 'Anguilla', 'AI', 0, 7, 1, 7, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(43, 'Antigua and Barbuda', 'AG', 0, 7, 1, 2, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(44, 'Argentina', 'AR', 0, NULL, 1, 6, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(45, 'Armenia', 'AM', 0, NULL, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(46, 'Aruba', 'AW', 0, 11, 1, 7, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(47, 'Azerbaijan', 'AZ', 0, 12, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(48, 'Bahamas', 'BS', 0, 17, 1, 2, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(49, 'Bahrain', 'BH', 0, NULL, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(50, 'Bangladesh', 'BD', 0, 15, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(51, 'Barbados', 'BB', 0, 14, 1, 2, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(52, 'Belarus', 'BY', 0, 19, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(53, 'Belize', 'BZ', 0, NULL, 1, 7, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(54, 'Benin', 'BJ', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(55, 'Bermuda', 'BM', 0, NULL, 1, 2, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(56, 'Bhutan', 'BT', 0, NULL, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(57, 'Botswana', 'BW', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(58, 'Brazil', 'BR', 0, 16, 1, 6, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(59, 'Brunei', 'BN', 0, NULL, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(60, 'Burkina Faso', 'BF', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(61, 'Burma (Myanmar)', 'MM', 0, NULL, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(62, 'Burundi', 'BI', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(63, 'Cambodia', 'KH', 0, 45, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(64, 'Cameroon', 'CM', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(65, 'Cape Verde', 'CV', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(66, 'Central African Republic', 'CF', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(67, 'Chad', 'TD', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(68, 'Chile', 'CL', 0, NULL, 1, 6, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(69, 'Colombia', 'CO', 0, NULL, 1, 6, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(70, 'Comoros', 'KM', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(71, 'Congo, Dem. Republic', 'CD', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(72, 'Congo, Republic', 'CG', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(73, 'Costa Rica', 'CR', 0, 23, 1, 7, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(74, 'Croatia', 'HR', 0, 37, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(75, 'Cuba', 'CU', 0, NULL, 1, 7, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(76, 'Cyprus', 'CY', 0, 1, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(77, 'Djibouti', 'DJ', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(78, 'Dominica', 'DM', 0, 7, 1, 7, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(79, 'Dominican Republic', 'DO', 0, 26, 1, 7, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(80, 'East Timor', 'TL', 0, 9, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(81, 'Ecuador', 'EC', 0, 9, 1, 6, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(82, 'Egypt', 'EG', 0, 28, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(83, 'El Salvador', 'SV', 0, 9, 1, 7, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(84, 'Equatorial Guinea', 'GQ', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(85, 'Eritrea', 'ER', 0, 29, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(86, 'Estonia', 'EE', 0, 27, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(87, 'Ethiopia', 'ET', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(88, 'Falkland Islands', 'FK', 0, NULL, 1, 7, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(89, 'Faroe Islands', 'FO', 0, 25, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(90, 'Fiji', 'FJ', 0, NULL, 1, 5, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(91, 'Gabon', 'GA', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(92, 'Gambia', 'GM', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(93, 'Georgia', 'GE', 0, 31, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(94, 'Ghana', 'GH', 0, 32, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(95, 'Grenada', 'GD', 0, 7, 1, 7, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(96, 'Greenland', 'GL', 0, 25, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(97, 'Gibraltar', 'GI', 0, 33, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(98, 'Guadeloupe', 'GP', 0, 1, 1, 7, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(99, 'Guam', 'GU', 0, 9, 1, 5, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(100, 'Guatemala', 'GT', 0, 34, 1, 7, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(101, 'Guernsey', 'GG', 0, NULL, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(102, 'Guinea', 'GN', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(103, 'Guinea-Bissau', 'GW', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(104, 'Guyana', 'GY', 0, NULL, 1, 6, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(105, 'Haiti', 'HT', 0, 9, 1, 7, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(106, 'Heard Island and McDonald Islands', 'HM', 0, 10, 1, 5, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(107, 'Vatican City State', 'VA', 0, 1, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(108, 'Honduras', 'HN', 0, 36, 1, 7, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(109, 'Iceland', 'IS', 0, NULL, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(110, 'India', 'IN', 0, 41, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(111, 'Indonesia', 'ID', 0, 39, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(112, 'Iran', 'IR', 0, NULL, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(113, 'Iraq', 'IQ', 0, NULL, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(114, 'Man Island', 'IM', 0, NULL, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(115, 'Jamaica', 'JM', 0, 42, 1, 7, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(116, 'Jersey', 'JE', 0, NULL, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(117, 'Jordan', 'JO', 0, NULL, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(118, 'Kazakhstan', 'KZ', 0, 50, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(119, 'Kenya', 'KE', 0, 44, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(120, 'Kiribati', 'KI', 0, 10, 1, 5, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(121, 'Korea, Dem. Republic of', 'KP', 0, 46, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(122, 'Kuwait', 'KW', 0, 48, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(123, 'Kyrgyzstan', 'KG', 0, NULL, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(124, 'Laos', 'LA', 0, NULL, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(125, 'Latvia', 'LV', 0, 56, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(126, 'Lebanon', 'LB', 0, 51, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(127, 'Lesotho', 'LS', 0, 54, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(128, 'Liberia', 'LR', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(129, 'Libya', 'LY', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(130, 'Liechtenstein', 'LI', 0, 52, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(131, 'Lithuania', 'LT', 0, 55, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(132, 'Macau', 'MO', 0, 57, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(133, 'Macedonia', 'MK', 0, NULL, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(134, 'Madagascar', 'MG', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(135, 'Malawi', 'MW', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(136, 'Malaysia', 'MY', 0, 59, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(137, 'Maldives', 'MV', 0, NULL, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(138, 'Mali', 'ML', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(139, 'Malta', 'MT', 0, NULL, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(140, 'Marshall Islands', 'MH', 0, 9, 1, 5, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(141, 'Martinique', 'MQ', 0, 1, 1, 7, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(142, 'Mauritania', 'MR', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(143, 'Hungary', 'HU', 0, 38, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(144, 'Mayotte', 'YT', 0, 1, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(145, 'Mexico', 'MX', 0, NULL, 1, 2, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(146, 'Micronesia', 'FM', 0, 9, 1, 5, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(147, 'Moldova', 'MD', 0, NULL, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(148, 'Monaco', 'MC', 0, NULL, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(149, 'Mongolia', 'MN', 0, NULL, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(150, 'Montenegro', 'ME', 0, 1, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(151, 'Montserrat', 'MS', 0, 7, 1, 7, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(152, 'Morocco', 'MA', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(153, 'Mozambique', 'MZ', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(154, 'Namibia', 'NA', 0, 54, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(155, 'Nauru', 'NR', 0, 10, 1, 5, 'firstname lastname\ncompany\nvat_number\naddress1\naddress2\npostcode city\nCountry:name\nphone'),
(156, 'Nepal', 'NP', 0, 62, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(157, 'Netherlands Antilles', 'AN', 0, 8, 1, 7, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(158, 'New Caledonia', 'NC', 0, NULL, 1, 5, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(159, 'Nicaragua', 'NI', 0, 61, 1, 7, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(160, 'Niger', 'NE', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(161, 'Niue', 'NU', 0, 21, 1, 5, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(162, 'Norfolk Island', 'NF', 0, 10, 1, 5, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(163, 'Northern Mariana Islands', 'MP', 0, 9, 1, 5, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(164, 'Oman', 'OM', 0, 63, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(165, 'Pakistan', 'PK', 0, 65, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(166, 'Palau', 'PW', 0, 9, 1, 5, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(167, 'Palestinian Territories', 'PS', 0, NULL, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(168, 'Panama', 'PA', 0, 9, 1, 7, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(169, 'Papua New Guinea', 'PG', 0, NULL, 1, 5, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(170, 'Paraguay', 'PY', 0, 66, 1, 6, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(171, 'Peru', 'PE', 0, NULL, 1, 6, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(172, 'Philippines', 'PH', 0, 64, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(173, 'Pitcairn', 'PN', 0, 21, 1, 5, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(174, 'Puerto Rico', 'PR', 0, 9, 1, 7, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(175, 'Qatar', 'QA', 0, 67, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(176, 'Reunion Island', 'RE', 0, 1, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(177, 'Russian Federation', 'RU', 0, 68, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(178, 'Rwanda', 'RW', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(179, 'Saint Barthelemy', 'BL', 0, NULL, 1, 7, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(180, 'Saint Kitts and Nevis', 'KN', 0, 7, 1, 7, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(181, 'Saint Lucia', 'LC', 0, 7, 1, 7, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(182, 'Saint Martin', 'MF', 0, NULL, 1, 7, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(183, 'Saint Pierre and Miquelon', 'PM', 0, 1, 1, 7, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(184, 'Saint Vincent and the Grenadines', 'VC', 0, 7, 1, 7, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(185, 'Samoa', 'WS', 0, NULL, 1, 5, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(186, 'San Marino', 'SM', 0, 1, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(187, 'São Tomé and Príncipe', 'ST', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(188, 'Saudi Arabia', 'SA', 0, 69, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(189, 'Senegal', 'SN', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(190, 'Serbia', 'RS', 0, NULL, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(191, 'Seychelles', 'SC', 0, 70, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(192, 'Sierra Leone', 'SL', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(193, 'Slovenia', 'SI', 0, NULL, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(194, 'Solomon Islands', 'SB', 0, NULL, 1, 5, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(195, 'Somalia', 'SO', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(196, 'South Georgia and the South Sandwich Islands', 'GS', 0, NULL, 1, 7, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(197, 'Sri Lanka', 'LK', 0, 53, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(198, 'Sudan', 'SD', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(199, 'Suriname', 'SR', 0, NULL, 1, 7, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(200, 'Svalbard and Jan Mayen', 'SJ', 0, 18, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(201, 'Swaziland', 'SZ', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(202, 'Syria', 'SY', 0, NULL, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(203, 'Taiwan', 'TW', 0, 75, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(204, 'Tajikistan', 'TJ', 0, NULL, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(205, 'Tanzania', 'TZ', 0, 76, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(206, 'Thailand', 'TH', 0, 73, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(207, 'Tokelau', 'TK', 0, 21, 1, 5, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(208, 'Tonga', 'TO', 0, NULL, 1, 5, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(209, 'Trinidad and Tobago', 'TT', 0, 74, 1, 6, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(210, 'Tunisia', 'TN', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(211, 'Turkey', 'TR', 0, NULL, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(212, 'Turkmenistan', 'TM', 0, NULL, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(213, 'Turks and Caicos Islands', 'TC', 0, 9, 1, 7, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(214, 'Tuvalu', 'TV', 0, 10, 1, 5, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(215, 'Uganda', 'UG', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(216, 'Ukraine', 'UA', 0, NULL, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(217, 'United Arab Emirates', 'AE', 0, 6, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(218, 'Uruguay', 'UY', 0, NULL, 1, 6, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(219, 'Uzbekistan', 'UZ', 0, 78, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(220, 'Vanuatu', 'VU', 0, NULL, 1, 5, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(221, 'Venezuela', 'VE', 0, NULL, 1, 6, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(222, 'Vietnam', 'VN', 0, NULL, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(223, 'Virgin Islands (British)', 'VG', 0, 9, 1, 2, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(224, 'Virgin Islands (U.S.)', 'VI', 0, 9, 1, 2, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(225, 'Wallis and Futuna', 'WF', 0, NULL, 1, 5, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(226, 'Western Sahara', 'EH', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(227, 'Yemen', 'YE', 0, NULL, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(228, 'Zambia', 'ZM', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);');
INSERT INTO `coreshop_countries` (`id`, `name`, `isoCode`, `active`, `currencyId`, `useStoreCurrency`, `zoneId`, `addressFormat`) VALUES
(229, 'Zimbabwe', 'ZW', 0, NULL, 1, 4, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(230, 'Albania', 'AL', 0, NULL, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(231, 'Afghanistan', 'AF', 0, NULL, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(232, 'Antarctica', 'AQ', 0, NULL, 1, 5, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(233, 'Bosnia and Herzegovina', 'BA', 0, 13, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(234, 'Bouvet Island', 'BV', 0, 18, 1, 5, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(235, 'British Indian Ocean Territory', 'IO', 0, 9, 1, 5, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(236, 'Bulgaria', 'BG', 0, NULL, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(237, 'Cayman Islands', 'KY', 0, 49, 1, 7, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(238, 'Christmas Island', 'CX', 0, 10, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(239, 'Cocos (Keeling) Islands', 'CC', 0, 10, 1, 3, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(240, 'Cook Islands', 'CK', 0, 21, 1, 5, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(241, 'French Guiana', 'GF', 0, 1, 1, 6, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(242, 'French Polynesia', 'PF', 0, NULL, 1, 5, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(243, 'French Southern Territories', 'TF', 0, 1, 1, 5, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);'),
(244, 'Åland Islands', 'AX', 0, NULL, 1, 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{"method" : "getName"});\n%Text(phone);');

DROP TABLE IF EXISTS `coreshop_cart_pricerules`;
CREATE TABLE `coreshop_cart_pricerules` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(50) NULL,
  `code` varchar(50) NULL,
  `label` text NULL,
  `description` text NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `highlight` tinyint(1) NOT NULL DEFAULT '0',
  `usagePerVoucherCode` int(11) NOT NULL DEFAULT '0',
  `useMultipleVoucherCodes` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `conditions` text NULL,
  `actions` text NULL
) COMMENT='';

DROP TABLE IF EXISTS `coreshop_zones`;
CREATE TABLE `coreshop_zones` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(50) NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0'
) COMMENT='';

INSERT INTO `coreshop_zones` (`id`, `name`, `active`) VALUES
(1,	'Europe',	1),
(2,	'North America',	0),
(3,	'Asia',	0),
(4,	'Africa',	0),
(5,	'Oceania',	0),
(6,	'South America',	0),
(7,	'Central America/Antilla',	0);

DROP TABLE IF EXISTS `coreshop_numberranges`;
CREATE TABLE `coreshop_numberranges` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `type` varchar(255) NOT NULL,
  `number` int(11) NOT NULL DEFAULT '0'
);

ALTER TABLE coreshop_numberranges ADD UNIQUE (`type`);

DROP TABLE IF EXISTS `coreshop_orderstates`;
CREATE TABLE `coreshop_orderstates` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `accepted` tinyint(1) NOT NULL DEFAULT '0',
  `shipped` tinyint(1) NOT NULL DEFAULT '0',
  `paid` tinyint(1) NOT NULL DEFAULT '0',
  `invoice` tinyint(1) NOT NULL DEFAULT '0',
  `email` tinyint(1) NOT NULL DEFAULT '0',
  `color` varchar(7) NOT NULL DEFAULT '#000000'
);

DROP TABLE IF EXISTS `coreshop_taxes`;
CREATE TABLE `coreshop_taxes` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `rate` double NOT NULL,
  `active` tinyint NOT NULL
);

DROP TABLE IF EXISTS `coreshop_tax_rule_groups`;
CREATE TABLE `coreshop_tax_rule_groups` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(255) NOT NULL,
  `active` tinyint NOT NULL
);

DROP TABLE IF EXISTS `coreshop_tax_rules`;
CREATE TABLE `coreshop_tax_rules` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `taxRuleGroupId` int(11) NOT NULL,
  `countryId` int(11) NOT NULL,
  `stateId` int(11) NOT NULL,
  `taxId` int(11) NOT NULL,
  `behavior` tinyint NOT NULL
);

DELETE FROM `users_permission_definitions` WHERE `key` LIKE 'coreshop_permission%';

INSERT INTO `users_permission_definitions` (`key`)
VALUES
  ('coreshop_permission_carriers'),
  ('coreshop_permission_zones'),
  ('coreshop_permission_settings'),
  ('coreshop_permission_price_rules'),
  ('coreshop_permission_order_states'),
  ('coreshop_permission_currencies'),
  ('coreshop_permission_taxes'),
  ('coreshop_permission_tax_rules'),
  ('coreshop_permission_plugins'),
  ('coreshop_permission_countries'),
  ('coreshop_permission_filters'),
  ('coreshop_permission_states'),
  ('coreshop_permission_indexes'),
  ('coreshop_permission_messaging_contact'),
  ('coreshop_permission_messaging_thread_state'),
  ('coreshop_permission_product_price_rules'),
  ('coreshop_permission_shops');

DROP TABLE IF EXISTS `coreshop_product_specificprice`;
CREATE TABLE `coreshop_product_specificprice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `o_id` int(11) NOT NULL,
  `inherit` tinyint(5) NOT NULL DEFAULT '1',
  `priority` int(11) NOT NULL DEFAULT '0',
  `name` varchar(50) DEFAULT NULL,
  `conditions` text,
  `actions` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `coreshop_indexes`;
CREATE TABLE `coreshop_indexes` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `type` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `config` text NOT NULL
);

DROP TABLE IF EXISTS `coreshop_product_filters`;
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

DROP TABLE IF EXISTS `coreshop_states`;
CREATE TABLE `coreshop_states` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `isoCode` varchar(2) NULL,
  `active` tinyint(1) DEFAULT 0,
  `countryId` int,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `coreshop_messaging_contact`;
CREATE TABLE `coreshop_messaging_contact` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `email` varchar(255) NULL,
  `description` varchar(1000) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `coreshop_messaging_thread_state`;
CREATE TABLE `coreshop_messaging_thread_state` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `color` varchar(255) NULL,
  `finished` TINYINT(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `coreshop_messaging_thread`;
CREATE TABLE `coreshop_messaging_thread` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `shopId` int NOT NULL,
  `userId` int NULL,
  `orderId` int NULL,
  `productId` int NULL,
  `statusId` int NULL,
  `token` varchar(255) NOT NULL,
  `contactId` int NOT NULL,
  `language` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `coreshop_messaging_message`;
CREATE TABLE `coreshop_messaging_message` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `threadId` int NOT NULL,
  `adminUserId` int(11) unsigned NULL,
  `message` text NOT NULL,
  `read` tinyint(1)  NOT NULL DEFAULT '1',
  `creationDate` bigint NOT NULL,
  FOREIGN KEY (`adminUserId`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `coreshop_product_pricerules`;
CREATE TABLE `coreshop_product_pricerules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `description` text,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `conditions` text,
  `actions` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `coreshop_shops`;
CREATE TABLE `coreshop_shops` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `siteId` int DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `template` varchar(255) NOT NULL,
  `isDefault` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `coreshop_shops` (`name`, `template`, `isDefault`)
VALUES ('Default', 'default', '1');

DROP TABLE IF EXISTS `coreshop_countries_shops`;
CREATE TABLE `coreshop_countries_shops` (
  `oId` int(11) NOT NULL,
  `shopId` int(11) NOT NULL,
  PRIMARY KEY (`oId`,`shopId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `coreshop_carriers_shops`;
CREATE TABLE `coreshop_carriers_shops` (
  `oId` int(11) NOT NULL,
  `shopId` int(11) NOT NULL,
  PRIMARY KEY (`oId`,`shopId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `coreshop_customer_groups_shops`;
CREATE TABLE `coreshop_customer_groups_shops` (
  `oId` int(11) NOT NULL,
  `shopId` int(11) NOT NULL,
  PRIMARY KEY (`oId`,`shopId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `coreshop_tax_rule_groups_shops`;
CREATE TABLE `coreshop_tax_rule_groups_shops` (
  `oId` int(11) NOT NULL,
  `shopId` int(11) NOT NULL,
  PRIMARY KEY (`oId`,`shopId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `coreshop_messaging_contact_shops`;
CREATE TABLE `coreshop_messaging_contact_shops` (
  `oId` int(11) NOT NULL,
  `shopId` int(11) NOT NULL,
  PRIMARY KEY (`oId`,`shopId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `coreshop_messaging_thread_state_shops`;
CREATE TABLE `coreshop_messaging_thread_state_shops` (
  `oId` int(11) NOT NULL,
  `shopId` int(11) NOT NULL,
  PRIMARY KEY (`oId`,`shopId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `coreshop_voucher_codes` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `code` varchar(255) NOT NULL,
  `creationDate` int NOT NULL,
  `used` tinyint(1) NOT NULL,
  `uses` tinyint(12) NOT NULL,
  `priceRuleId` int NOT NULL
);

DROP TABLE IF EXISTS `coreshop_carrier_shippingrules`;
CREATE TABLE `coreshop_carrier_shippingrules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `description` text,
  `conditions` text,
  `actions` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `coreshop_carrier_shippingrule_groups`;
CREATE TABLE `coreshop_carrier_shippingrule_groups` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `carrierId` int NOT NULL,
  `priority` int NOT NULL,
  `shippingRuleId` int NOT NULL
);

DROP TABLE IF EXISTS `coreshop_visitor`;
CREATE TABLE `coreshop_visitor` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `shopId` int(11) NOT NULL,
  `userId` int NULL,
  `ip` int NOT NULL,
  `controller` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `module` varchar(255) NOT NULL,
  `referrer` varchar(255) NOT NULL,
  `creationDate` bigint(20) NOT NULL
);

DROP TABLE IF EXISTS `coreshop_visitor_page`;
CREATE TABLE `coreshop_visitor_page` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `visitorId` int NOT NULL,
  `controller` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `module` varchar(255) NOT NULL,
  `creationDate` bigint(20) NOT NULL
);

DROP TABLE IF EXISTS `coreshop_visitor_source`;
CREATE TABLE `coreshop_visitor_source` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `visitorId` int NOT NULL,
  `referrer` varchar(255) NOT NULL,
  `requestUrl` varchar(255) NOT NULL,
  `controller` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `module` varchar(255) NOT NULL,
  `creationDate` bigint(20) NOT NULL
);