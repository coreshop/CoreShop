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
  `tax` int NULL,
  `needsRange` tinyint(1) NOT NULL DEFAULT '0',
  `rangeBehaviour` enum('largest','deactivate') NOT NULL,
  `maxHeight` double NOT NULL DEFAULT '0',
  `maxWidth` double NOT NULL DEFAULT '0',
  `maxDepth` double NOT NULL DEFAULT '0',
  `maxWeight` double NOT NULL DEFAULT '0'
) COMMENT='';

DROP TABLE IF EXISTS `coreshop_carriers_range_weight`;
CREATE TABLE `coreshop_carriers_range_weight` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `carrierId` int NOT NULL,
  `delimiter1` double NOT NULL,
  `delimiter2` double NOT NULL
) COMMENT='';


DROP TABLE IF EXISTS `coreshop_carriers_range_price`;
CREATE TABLE `coreshop_carriers_range_price` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `carrierId` int NOT NULL,
  `delimiter1` double NOT NULL,
  `delimiter2` double NOT NULL
) COMMENT='';

DROP TABLE IF EXISTS `coreshop_carriers_delivery_price`;
CREATE TABLE `coreshop_carriers_delivery_price` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `rangeType` enum('price','weight') NOT NULL,
  `rangeId` int NOT NULL,
  `price` double NOT NULL,
  `zoneId` int,
  `carrierId` int
) COMMENT='';

DROP TABLE IF EXISTS `coreshop_countries`;
CREATE TABLE `coreshop_countries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `isoCode` varchar(2) NULL,
  `active` tinyint(1) DEFAULT 0,
  `currencyId` int,
  `zoneId` int,
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

INSERT INTO `coreshop_countries` (`id`, `name`, `isoCode`, `active`, `currencyId`, `zoneId`) VALUES
(1, 'Germany', 'DE', 1, 1, 1),
(2, 'Austria', 'AT', 1, 1, 1),
(3, 'Belgium', 'BE', 0, 1, 1),
(4, 'Canada', 'CA', 0, 20, 2),
(5, 'China', 'CN', 0, 22, 3),
(6, 'Spain', 'ES', 0, NULL, 1),
(7, 'Finland', 'FI', 0, NULL, 1),
(8, 'France', 'FR', 0, NULL, 1),
(9, 'Greece', 'GR', 0, NULL, 1),
(10, 'Italy', 'IT', 0, NULL, 1),
(11, 'Japan', 'JP', 0, 43, 3),
(12, 'Luxemburg', 'LU', 0, NULL, 1),
(13, 'Netherlands', 'NL', 0, 1, 1),
(14, 'Poland', 'PL', 0, NULL, 1),
(15, 'Portugal', 'PT', 0, NULL, 1),
(16, 'Czech Republic', 'CZ', 0, 24, 1),
(17, 'United Kingdom', 'GB', 0, 30, 1),
(18, 'Sweden', 'SE', 0, 71, 1),
(19, 'Switzerland', 'CH', 0, NULL, 1),
(20, 'Denmark', 'DK', 0, 25, 1),
(21, 'United States', 'US', 0, 77, 2),
(22, 'HongKong', 'HK', 0, 35, 3),
(23, 'Norway', 'NO', 0, 18, 1),
(24, 'Australia', 'AU', 0, 10, 5),
(25, 'Singapore', 'SG', 0, 72, 3),
(26, 'Ireland', 'IE', 0, NULL, 1),
(27, 'New Zealand', 'NZ', 0, 21, 5),
(28, 'South Korea', 'KR', 0, 47, 3),
(29, 'Israel', 'IL', 0, 40, 3),
(30, 'South Africa', 'ZA', 0, NULL, 4),
(31, 'Nigeria', 'NG', 0, 60, 4),
(32, 'Ivory Coast', 'CI', 0, NULL, 4),
(33, 'Togo', 'TG', 0, NULL, 4),
(34, 'Bolivia', 'BO', 0, NULL, 6),
(35, 'Mauritius', 'MU', 0, 58, 4),
(36, 'Romania', 'RO', 0, NULL, 1),
(37, 'Slovakia', 'SK', 0, NULL, 1),
(38, 'Algeria', 'DZ', 0, NULL, 4),
(39, 'American Samoa', 'AS', 0, 9, 2),
(40, 'Andorra', 'AD', 0, 1, 1),
(41, 'Angola', 'AO', 0, NULL, 4),
(42, 'Anguilla', 'AI', 0, 7, 7),
(43, 'Antigua and Barbuda', 'AG', 0, 7, 2),
(44, 'Argentina', 'AR', 0, NULL, 6),
(45, 'Armenia', 'AM', 0, NULL, 3),
(46, 'Aruba', 'AW', 0, 11, 7),
(47, 'Azerbaijan', 'AZ', 0, 12, 3),
(48, 'Bahamas', 'BS', 0, 17, 2),
(49, 'Bahrain', 'BH', 0, NULL, 3),
(50, 'Bangladesh', 'BD', 0, 15, 3),
(51, 'Barbados', 'BB', 0, 14, 2),
(52, 'Belarus', 'BY', 0, 19, 1),
(53, 'Belize', 'BZ', 0, NULL, 7),
(54, 'Benin', 'BJ', 0, NULL, 4),
(55, 'Bermuda', 'BM', 0, NULL, 2),
(56, 'Bhutan', 'BT', 0, NULL, 3),
(57, 'Botswana', 'BW', 0, NULL, 4),
(58, 'Brazil', 'BR', 0, 16, 6),
(59, 'Brunei', 'BN', 0, NULL, 3),
(60, 'Burkina Faso', 'BF', 0, NULL, 4),
(61, 'Burma (Myanmar)', 'MM', 0, NULL, 3),
(62, 'Burundi', 'BI', 0, NULL, 4),
(63, 'Cambodia', 'KH', 0, 45, 3),
(64, 'Cameroon', 'CM', 0, NULL, 4),
(65, 'Cape Verde', 'CV', 0, NULL, 4),
(66, 'Central African Republic', 'CF', 0, NULL, 4),
(67, 'Chad', 'TD', 0, NULL, 4),
(68, 'Chile', 'CL', 0, NULL, 6),
(69, 'Colombia', 'CO', 0, NULL, 6),
(70, 'Comoros', 'KM', 0, NULL, 4),
(71, 'Congo, Dem. Republic', 'CD', 0, NULL, 4),
(72, 'Congo, Republic', 'CG', 0, NULL, 4),
(73, 'Costa Rica', 'CR', 0, 23, 7),
(74, 'Croatia', 'HR', 0, 37, 1),
(75, 'Cuba', 'CU', 0, NULL, 7),
(76, 'Cyprus', 'CY', 0, 1, 1),
(77, 'Djibouti', 'DJ', 0, NULL, 4),
(78, 'Dominica', 'DM', 0, 7, 7),
(79, 'Dominican Republic', 'DO', 0, 26, 7),
(80, 'East Timor', 'TL', 0, 9, 3),
(81, 'Ecuador', 'EC', 0, 9, 6),
(82, 'Egypt', 'EG', 0, 28, 4),
(83, 'El Salvador', 'SV', 0, 9, 7),
(84, 'Equatorial Guinea', 'GQ', 0, NULL, 4),
(85, 'Eritrea', 'ER', 0, 29, 4),
(86, 'Estonia', 'EE', 0, 27, 1),
(87, 'Ethiopia', 'ET', 0, NULL, 4),
(88, 'Falkland Islands', 'FK', 0, NULL, 7),
(89, 'Faroe Islands', 'FO', 0, 25, 1),
(90, 'Fiji', 'FJ', 0, NULL, 5),
(91, 'Gabon', 'GA', 0, NULL, 4),
(92, 'Gambia', 'GM', 0, NULL, 4),
(93, 'Georgia', 'GE', 0, 31, 3),
(94, 'Ghana', 'GH', 0, 32, 4),
(95, 'Grenada', 'GD', 0, 7, 7),
(96, 'Greenland', 'GL', 0, 25, 1),
(97, 'Gibraltar', 'GI', 0, 33, 1),
(98, 'Guadeloupe', 'GP', 0, 1, 7),
(99, 'Guam', 'GU', 0, 9, 5),
(100, 'Guatemala', 'GT', 0, 34, 7),
(101, 'Guernsey', 'GG', 0, NULL, 1),
(102, 'Guinea', 'GN', 0, NULL, 4),
(103, 'Guinea-Bissau', 'GW', 0, NULL, 4),
(104, 'Guyana', 'GY', 0, NULL, 6),
(105, 'Haiti', 'HT', 0, 9, 7),
(106, 'Heard Island and McDonald Islands', 'HM', 0, 10, 5),
(107, 'Vatican City State', 'VA', 0, 1, 1),
(108, 'Honduras', 'HN', 0, 36, 7),
(109, 'Iceland', 'IS', 0, NULL, 1),
(110, 'India', 'IN', 0, 41, 3),
(111, 'Indonesia', 'ID', 0, 39, 3),
(112, 'Iran', 'IR', 0, NULL, 3),
(113, 'Iraq', 'IQ', 0, NULL, 3),
(114, 'Man Island', 'IM', 0, NULL, 1),
(115, 'Jamaica', 'JM', 0, 42, 7),
(116, 'Jersey', 'JE', 0, NULL, 1),
(117, 'Jordan', 'JO', 0, NULL, 3),
(118, 'Kazakhstan', 'KZ', 0, 50, 3),
(119, 'Kenya', 'KE', 0, 44, 4),
(120, 'Kiribati', 'KI', 0, 10, 5),
(121, 'Korea, Dem. Republic of', 'KP', 0, 46, 3),
(122, 'Kuwait', 'KW', 0, 48, 3),
(123, 'Kyrgyzstan', 'KG', 0, NULL, 3),
(124, 'Laos', 'LA', 0, NULL, 3),
(125, 'Latvia', 'LV', 0, 56, 1),
(126, 'Lebanon', 'LB', 0, 51, 3),
(127, 'Lesotho', 'LS', 0, 54, 4),
(128, 'Liberia', 'LR', 0, NULL, 4),
(129, 'Libya', 'LY', 0, NULL, 4),
(130, 'Liechtenstein', 'LI', 0, 52, 1),
(131, 'Lithuania', 'LT', 0, 55, 1),
(132, 'Macau', 'MO', 0, 57, 3),
(133, 'Macedonia', 'MK', 0, NULL, 1),
(134, 'Madagascar', 'MG', 0, NULL, 4),
(135, 'Malawi', 'MW', 0, NULL, 4),
(136, 'Malaysia', 'MY', 0, 59, 3),
(137, 'Maldives', 'MV', 0, NULL, 3),
(138, 'Mali', 'ML', 0, NULL, 4),
(139, 'Malta', 'MT', 0, NULL, 1),
(140, 'Marshall Islands', 'MH', 0, 9, 5),
(141, 'Martinique', 'MQ', 0, 1, 7),
(142, 'Mauritania', 'MR', 0, NULL, 4),
(143, 'Hungary', 'HU', 0, 38, 1),
(144, 'Mayotte', 'YT', 0, 1, 4),
(145, 'Mexico', 'MX', 0, NULL, 2),
(146, 'Micronesia', 'FM', 0, 9, 5),
(147, 'Moldova', 'MD', 0, NULL, 1),
(148, 'Monaco', 'MC', 0, NULL, 1),
(149, 'Mongolia', 'MN', 0, NULL, 3),
(150, 'Montenegro', 'ME', 0, 1, 1),
(151, 'Montserrat', 'MS', 0, 7, 7),
(152, 'Morocco', 'MA', 0, NULL, 4),
(153, 'Mozambique', 'MZ', 0, NULL, 4),
(154, 'Namibia', 'NA', 0, 54, 4),
(155, 'Nauru', 'NR', 0, 10, 5),
(156, 'Nepal', 'NP', 0, 62, 3),
(157, 'Netherlands Antilles', 'AN', 0, 8, 7),
(158, 'New Caledonia', 'NC', 0, NULL, 5),
(159, 'Nicaragua', 'NI', 0, 61, 7),
(160, 'Niger', 'NE', 0, NULL, 4),
(161, 'Niue', 'NU', 0, 21, 5),
(162, 'Norfolk Island', 'NF', 0, 10, 5),
(163, 'Northern Mariana Islands', 'MP', 0, 9, 5),
(164, 'Oman', 'OM', 0, 63, 3),
(165, 'Pakistan', 'PK', 0, 65, 3),
(166, 'Palau', 'PW', 0, 9, 5),
(167, 'Palestinian Territories', 'PS', 0, NULL, 3),
(168, 'Panama', 'PA', 0, 9, 7),
(169, 'Papua New Guinea', 'PG', 0, NULL, 5),
(170, 'Paraguay', 'PY', 0, 66, 6),
(171, 'Peru', 'PE', 0, NULL, 6),
(172, 'Philippines', 'PH', 0, 64, 3),
(173, 'Pitcairn', 'PN', 0, 21, 5),
(174, 'Puerto Rico', 'PR', 0, 9, 7),
(175, 'Qatar', 'QA', 0, 67, 3),
(176, 'Reunion Island', 'RE', 0, 1, 4),
(177, 'Russian Federation', 'RU', 0, 68, 1),
(178, 'Rwanda', 'RW', 0, NULL, 4),
(179, 'Saint Barthelemy', 'BL', 0, NULL, 7),
(180, 'Saint Kitts and Nevis', 'KN', 0, 7, 7),
(181, 'Saint Lucia', 'LC', 0, 7, 7),
(182, 'Saint Martin', 'MF', 0, NULL, 7),
(183, 'Saint Pierre and Miquelon', 'PM', 0, 1, 7),
(184, 'Saint Vincent and the Grenadines', 'VC', 0, 7, 7),
(185, 'Samoa', 'WS', 0, NULL, 5),
(186, 'San Marino', 'SM', 0, 1, 1),
(187, 'São Tomé and Príncipe', 'ST', 0, NULL, 4),
(188, 'Saudi Arabia', 'SA', 0, 69, 3),
(189, 'Senegal', 'SN', 0, NULL, 4),
(190, 'Serbia', 'RS', 0, NULL, 1),
(191, 'Seychelles', 'SC', 0, 70, 4),
(192, 'Sierra Leone', 'SL', 0, NULL, 4),
(193, 'Slovenia', 'SI', 0, NULL, 1),
(194, 'Solomon Islands', 'SB', 0, NULL, 5),
(195, 'Somalia', 'SO', 0, NULL, 4),
(196, 'South Georgia and the South Sandwich Islands', 'GS', 0, NULL, 7),
(197, 'Sri Lanka', 'LK', 0, 53, 3),
(198, 'Sudan', 'SD', 0, NULL, 4),
(199, 'Suriname', 'SR', 0, NULL, 7),
(200, 'Svalbard and Jan Mayen', 'SJ', 0, 18, 1),
(201, 'Swaziland', 'SZ', 0, NULL, 4),
(202, 'Syria', 'SY', 0, NULL, 3),
(203, 'Taiwan', 'TW', 0, 75, 3),
(204, 'Tajikistan', 'TJ', 0, NULL, 3),
(205, 'Tanzania', 'TZ', 0, 76, 4),
(206, 'Thailand', 'TH', 0, 73, 3),
(207, 'Tokelau', 'TK', 0, 21, 5),
(208, 'Tonga', 'TO', 0, NULL, 5),
(209, 'Trinidad and Tobago', 'TT', 0, 74, 6),
(210, 'Tunisia', 'TN', 0, NULL, 4),
(211, 'Turkey', 'TR', 0, NULL, 1),
(212, 'Turkmenistan', 'TM', 0, NULL, 3),
(213, 'Turks and Caicos Islands', 'TC', 0, 9, 7),
(214, 'Tuvalu', 'TV', 0, 10, 5),
(215, 'Uganda', 'UG', 0, NULL, 4),
(216, 'Ukraine', 'UA', 0, NULL, 1),
(217, 'United Arab Emirates', 'AE', 0, 6, 3),
(218, 'Uruguay', 'UY', 0, NULL, 6),
(219, 'Uzbekistan', 'UZ', 0, 78, 3),
(220, 'Vanuatu', 'VU', 0, NULL, 5),
(221, 'Venezuela', 'VE', 0, NULL, 6),
(222, 'Vietnam', 'VN', 0, NULL, 3),
(223, 'Virgin Islands (British)', 'VG', 0, 9, 2),
(224, 'Virgin Islands (U.S.)', 'VI', 0, 9, 2),
(225, 'Wallis and Futuna', 'WF', 0, NULL, 5),
(226, 'Western Sahara', 'EH', 0, NULL, 4),
(227, 'Yemen', 'YE', 0, NULL, 3),
(228, 'Zambia', 'ZM', 0, NULL, 4),
(229, 'Zimbabwe', 'ZW', 0, NULL, 4),
(230, 'Albania', 'AL', 0, NULL, 1),
(231, 'Afghanistan', 'AF', 0, NULL, 3),
(232, 'Antarctica', 'AQ', 0, NULL, 5),
(233, 'Bosnia and Herzegovina', 'BA', 0, 13, 1),
(234, 'Bouvet Island', 'BV', 0, 18, 5),
(235, 'British Indian Ocean Territory', 'IO', 0, 9, 5),
(236, 'Bulgaria', 'BG', 0, NULL, 1),
(237, 'Cayman Islands', 'KY', 0, 49, 7),
(238, 'Christmas Island', 'CX', 0, 10, 3),
(239, 'Cocos (Keeling) Islands', 'CC', 0, 10, 3),
(240, 'Cook Islands', 'CK', 0, 21, 5),
(241, 'French Guiana', 'GF', 0, 1, 6),
(242, 'French Polynesia', 'PF', 0, NULL, 5),
(243, 'French Southern Territories', 'TF', 0, 1, 5),
(244, 'Åland Islands', 'AX', 0, NULL, 1);

INSERT INTO `users_permission_definitions` (`key`)
VALUES ('plugin_coreshop');

DROP TABLE IF EXISTS `coreshop_pricerules`;
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

DROP TABLE IF EXISTS `coreshop_numberRanges`;
CREATE TABLE `coreshop_numberRanges` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `type` varchar(255) NOT NULL,
  `number` int(11) NOT NULL DEFAULT '0'
);

ALTER TABLE coreshop_numberRanges ADD UNIQUE (`type`);

DROP TABLE IF EXISTS `coreshop_orderStates`;
CREATE TABLE `coreshop_orderStates` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(255) NOT NULL,
  `accepted` tinyint(1) NOT NULL DEFAULT '0',
  `shipped` tinyint(1) NOT NULL DEFAULT '0',
  `paid` tinyint(1) NOT NULL DEFAULT '0',
  `invoice` tinyint(1) NOT NULL DEFAULT '0',
  `email` tinyint(1) NOT NULL DEFAULT '0'
);

/*
  TODO: Move this to any programmed Import
*/

INSERT INTO `coreshop_orderStates` (`id`, `name`, `accepted`, `shipped`, `paid`, `invoice`, `email`) VALUES
(1,	'Awaiting check payment',	0,	0,	0,	0,	1),
(2,	'Payment accepted',	1,	0,	1,	1,	1),
(3,	'Processing in progress',	1,	0,	1,	1,	1),
(4,	'Shipped',	1,	1,	1,	1,	1),
(5,	'Delivered',	1,	1,	1,	0,	0),
(6,	'Canceled',	0,	0,	0,	0,	1),
(7,	'Refunded',	0,	0,	0,	1,	1),
(8,	'Payment error',	0,	0,	0,	0,	1),
(9,	'On backorder (paid)',	0,	0,	1,	1,	1),
(10,	'Awaiting bank wire payment',	0,	0,	0,	0,	1),
(11,	'On backorder (not paid)',	0,	0,	0,	0,	1),
(12,	'Awaiting Cash On Delivery validation',	0,	0,	0,	0,	0);

DROP TABLE IF EXISTS `coreshop_orderStates_data`;
CREATE TABLE `coreshop_orderStates_data` (
  `ooo_id` int(11) NOT NULL DEFAULT '0',
  `language` varchar(10) NOT NULL DEFAULT '',
  `emailDocument` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ooo_id`,`language`),
  KEY `ooo_id` (`ooo_id`),
  KEY `language` (`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `coreshop_orderStates_data` (`ooo_id`, `language`, `emailDocument`) VALUES
(1,	'de',	'/en/shop/email/awaiting-check-payment'),
(1,	'en',	'/de/shop/email/awaiting-check-payment'),
(2,	'de',	'/de/shop/email/payment'),
(2,	'en',	'/en/shop/email/payment');

DROP VIEW IF EXISTS `coreshop_orderstates_data_localized_de`;
CREATE TABLE `coreshop_orderstates_data_localized_de` (`id` int(11), `name` varchar(255), `accepted` tinyint(1), `shipped` tinyint(1), `paid` tinyint(1), `invoice` tinyint(1), `email` tinyint(1), `ooo_id` int(11), `language` varchar(10), `emailDocument` varchar(255));


DROP VIEW IF EXISTS `coreshop_orderstates_data_localized_en`;
CREATE TABLE `coreshop_orderstates_data_localized_en` (`id` int(11), `name` varchar(255), `accepted` tinyint(1), `shipped` tinyint(1), `paid` tinyint(1), `invoice` tinyint(1), `email` tinyint(1), `ooo_id` bigint(11), `language` varchar(10), `emailDocument` varchar(255));


DROP TABLE IF EXISTS `coreshop_orderStates_query_de`;
CREATE TABLE `coreshop_orderStates_query_de` (
  `ooo_id` int(11) NOT NULL DEFAULT '0',
  `language` varchar(10) NOT NULL DEFAULT '',
  `emailDocument` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ooo_id`,`language`),
  KEY `ooo_id` (`ooo_id`),
  KEY `language` (`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `coreshop_orderStates_query_de` (`ooo_id`, `language`, `emailDocument`) VALUES
(1,	'de',	'/en/shop/email/awaiting-check-payment'),
(2,	'de',	'/de/shop/email/payment');

DROP TABLE IF EXISTS `coreshop_orderStates_query_en`;
CREATE TABLE `coreshop_orderStates_query_en` (
  `ooo_id` int(11) NOT NULL DEFAULT '0',
  `language` varchar(10) NOT NULL DEFAULT '',
  `emailDocument` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ooo_id`,`language`),
  KEY `ooo_id` (`ooo_id`),
  KEY `language` (`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `coreshop_orderStates_query_en` (`ooo_id`, `language`, `emailDocument`) VALUES
(1,	'en',	'/de/shop/email/awaiting-check-payment'),
(2,	'en',	'/en/shop/email/payment');

DROP TABLE IF EXISTS `coreshop_orderstates_data_localized_de`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `coreshop_orderstates_data_localized_de` AS select `coreshop_orderstates`.`id` AS `id`,`coreshop_orderstates`.`name` AS `name`,`coreshop_orderstates`.`accepted` AS `accepted`,`coreshop_orderstates`.`shipped` AS `shipped`,`coreshop_orderstates`.`paid` AS `paid`,`coreshop_orderstates`.`invoice` AS `invoice`,`coreshop_orderstates`.`email` AS `email`,`de`.`ooo_id` AS `ooo_id`,`de`.`language` AS `language`,`de`.`emailDocument` AS `emailDocument` from (`coreshop_orderstates` left join `coreshop_orderstates_query_de` `de` on((1 and (`coreshop_orderstates`.`id` = `de`.`ooo_id`))));

DROP TABLE IF EXISTS `coreshop_orderstates_data_localized_en`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `coreshop_orderstates_data_localized_en` AS select `coreshop_orderstates`.`id` AS `id`,`coreshop_orderstates`.`name` AS `name`,`coreshop_orderstates`.`accepted` AS `accepted`,`coreshop_orderstates`.`shipped` AS `shipped`,`coreshop_orderstates`.`paid` AS `paid`,`coreshop_orderstates`.`invoice` AS `invoice`,`coreshop_orderstates`.`email` AS `email`,ifnull(`en`.`ooo_id`,`de`.`ooo_id`) AS `ooo_id`,ifnull(`en`.`language`,`de`.`language`) AS `language`,ifnull(`en`.`emailDocument`,`de`.`emailDocument`) AS `emailDocument` from ((`coreshop_orderstates` left join `coreshop_orderstates_query_en` `en` on((1 and (`coreshop_orderstates`.`id` = `en`.`ooo_id`)))) left join `coreshop_orderstates_query_de` `de` on((1 and (`coreshop_orderstates`.`id` = `de`.`ooo_id`))));
