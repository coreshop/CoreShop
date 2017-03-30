CREATE TABLE `coreshop_customer_groups` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(255) NOT NULL,
  `discount` double NOT NULL
);

INSERT INTO `users_permission_definitions` (`key`)
VALUES ('coreshop_permission_customer_groups');