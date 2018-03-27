@product @product_price_rules @product_price_rules_condition_timespan
Feature: Adding a new Product
  In order to extend my catalog
  the catalog has a price-rule for a time-span
  I want to create a new product

  Background:
    Given the site operates on a store in "Austria"
    Given the site has a product "Shoe" priced at 100
    Then the product "Shoe" should be priced at "100"

  Scenario: Add a new timespan category price rule which is valid
    Given adding a product price rule named "timespan-discount"
    And the price rule is active
    And the price rule has a condition timespan which is valid from "yesterday" to "tomorrow"
    Then the price rule should be valid for product "Shoe"

  Scenario: Add a new timespan category price rule which is invalid
    Given adding a product price rule named "timespan-discount"
    And the price rule is active
    And the price rule has a condition timespan which is valid from "10 days ago" to "yesterday"
    Then the price rule should be invalid for product "Shoe"