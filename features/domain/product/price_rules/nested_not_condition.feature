@domain @product
Feature: Adding a new Product
  In order to extend my catalog
  The product has a specific-price-rule with nested conditions

  Background:
    Given the site operates on a store in "Austria"
    And the site has a product "Shoe" priced at 100
    And the site has a product "Shirt" priced at 200

  Scenario: Add a new nested not product specific price rule with not product shoe
    Given adding a product price rule named "not-nested-condition"
    And the price rule is active
    And the price rule has a condition nested with operator "not" with product "Shirt"
    Then the price rule should be valid for product "Shoe"
    Then the price rule should be invalid for product "Shirt"
