--
-- We are going to use Fixtures some time, currently we just use this Sql File for default data.
-- We totally need to add way more data to this file, but for starters, this is enough
--

INSERT INTO `coreshop_zone` (`id`, `active`, `name`) VALUES
(1, 1, 'Europe');

INSERT INTO `coreshop_currency` (`id`, `isoCode`, `name`, `numericIsoCode`, `symbol`, `exchangeRate`) VALUES
(1, 'EUR', 'Euro', 100, '€', 1),
(2, 'CHF', 'Franken', 100, 'CHF', 1);

INSERT INTO `coreshop_store` (`id`, `name`, `template`, `isDefault`, `siteId`, `baseCurrencyId`, `baseCountryId`) VALUES
(1, 'Standard', 'standard', 1, 0, 1, 1);

INSERT INTO `coreshop_country` (`id`, `name`, `isoCode`, `active`, `addressFormat`, `zoneId`, `currencyId`) VALUES
(1, 'Österreich', 'AT', 1, '%Text(company);\n%Text(firstname); %Text(lastname);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{\"method\" : \"getName\"});\n%Text(phone);', 1, 1),
(2, 'Schweiz', 'CH', 1, '%Text(firstname); %Text(lastname);\n%Text(company);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{\"method\" : \"getName\"});\n%Text(phone);', 1, 2),
(3, 'Deutschland', 'DE', 1, '%Text(company);\n%Text(firstname); %Text(lastname);\n%Text(vatNumber);\n%Text(street); %Text(nr);\n%Text(zip); %Text(city);\n%Object(country,{\"method\" : \"getName\"});\n%Text(phone);', 1, 1);

INSERT INTO `coreshop_country_stores` (`country_id`, `store_id`) VALUES
(1, 1),
(2, 1),
(3, 1);