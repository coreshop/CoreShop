CREATE TABLE `coreshop_taxes` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `rate` double NOT NULL,
  `active` tinyint NOT NULL
);

CREATE TABLE `coreshop_tax_rule_groups` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(255) NOT NULL,
  `active` tinyint NOT NULL
);

CREATE TABLE `coreshop_tax_rules` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `taxRuleGroupId` int(11) NOT NULL,
  `countryId` int(11) NOT NULL,
  `taxId` int(11) NOT NULL,
  `behavior` tinyint NOT NULL
);

INSERT INTO `users_permission_definitions` (`key`)
VALUES
  ('coreshop_permission_taxes'),
  ('coreshop_permission_tax_rules');

ALTER TABLE `coreshop_carriers` CHANGE `tax` `taxRuleGroupId` INT(11) NULL DEFAULT NULL;