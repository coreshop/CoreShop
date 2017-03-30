DELETE FROM `users_permission_definitions` WHERE `key` LIKE 'coreshop_permission_%';

INSERT INTO `users_permission_definitions` (`key`)
VALUES
  ('coreshop_permission_carriers'),
  ('coreshop_permission_zones'),
  ('coreshop_permission_settings'),
  ('coreshop_permission_priceRules'),
  ('coreshop_permission_orderStates'),
  ('coreshop_permission_currencies'),
  ('coreshop_permission_taxes'),
  ('coreshop_permission_tax_rules'),
  ('coreshop_permission_customer_groups'),
  ('coreshop_permission_plugins'),
  ('coreshop_permission_countries'),
  ('coreshop_permission_filters'),
  ('coreshop_permission_indexes');
