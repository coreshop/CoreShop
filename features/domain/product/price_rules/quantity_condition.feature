@product @product_price_rules @product_price_rules_condition_quantity
Feature: Adding a new Product
  In order to extend my catalog
  The catalog has a price-rule for a product
  I want to create a new product

  Background:
    Given the site operates on a store in "Austria"
    Given the site has a currency "Euro" with iso "EUR"
    Given I am in country "Austria"
    Given the site has a product "Shoe" priced at 100
    Then the product "Shoe" should be priced at "100"

  Scenario: Add a new product catalog price rule which is invalid
    Given adding a product price rule named "quantity-discount"
    And the price rule is active
    And the price rule has a condition quantity with min 2 and max 100
    Then the price rule should be invalid for product "Shoe"

  Scenario: Add a new product catalog price rule which is valid
    Given adding a product price rule named "quantity-discount"
    And the price rule is active
    And the price rule has a condition quantity with min 2 and max 100
    Then the price rule should be invalid for product "Shoe"
    And I add the product "Shoe" to my cart
    And I add the product "Shoe" to my cart
    Then the price rule should be valid for product "Shoe"

