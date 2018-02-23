@product
Feature: Adding a new Product
  In order to extend my catalog
  the product has a specific-price-rule for a time-span
  I want to create a new product

  Background:
    Given the site operates on a store in "Austria"
    Given the site has a product "Shoe" priced at 100
    Then the product "Shoe" should be priced at 100

  Scenario: Add a new timespan product specific price rule which is valid
    Given adding a product specific price rule to product "Shoe" named "timespan-discount"
    And it is active
    And it has a condition timespan which is valid from "yesterday" to "tomorrow"
    Then it should be valid for product "Shoe"

  Scenario: Add a new timespan product specific price rule which is invalid
    Given adding a product specific price rule to product "Shoe" named "timespan-discount"
    And it is active
    And it has a condition timespan which is valid from "10 days ago" to "yesterday"
    Then it should be invalid for product "Shoe"