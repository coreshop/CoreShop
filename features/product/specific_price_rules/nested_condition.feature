@product @product_specific_price_rules @product_specific_price_rules_condition_nested
Feature: Adding a new Product
  In order to extend my catalog
  The product has a specific-price-rule with nested conditions
  I want to create a new product

  Background:
    Given the site operates on a store in "Austria"
    And the site has a product "Shoe" priced at 100
    And the site has a currency "YEN" with iso "YEN"
    And the site has a currency "USD" with iso "USD"
    And the site has a country "China" with currency "YEN"
    And the site has a country "USA" with currency "USD"
    And the site has a store "Asia" with country "China" and currency "Yen"
    And the site has a store "US" with country "USA" and currency "USD"
    Then the product "Shoe" should be priced at "100"

  Scenario: Add a new nested product specific price rule with store and store which is valid
    Given adding a product specific price rule to product "Shoe" named "store-discount"
    And the specific price rule is active
    And the specific price rule has a condition nested with operator "or" for store "Austria" and store "Asia"
    Then the specific price rule should be valid for product "Shoe"

  Scenario: Add a new nested product specific price rule with store and store which is invalid
    Given adding a product specific price rule to product "Shoe" named "store-discount"
    And the specific price rule is active
    And the specific price rule has a condition nested with operator "or" for store "US" and store "Asia"
    Then the specific price rule should be invalid for product "Shoe"

  Scenario: Add a new nested product specific price rule with store and country which is valid
    Given adding a product specific price rule to product "Shoe" named "store-discount"
    And the specific price rule is active
    And the specific price rule has a condition nested with operator "and" for store "Austria" and country "Austria"
    Then the specific price rule should be valid for product "Shoe"

  Scenario: Add a new nested product specific price rule with store and country which is invalid
    Given adding a product specific price rule to product "Shoe" named "store-discount"
    And the specific price rule is active
    And the specific price rule has a condition nested with operator "and" for store "US" and country "Austria"
    Then the specific price rule should be invalid for product "Shoe"
