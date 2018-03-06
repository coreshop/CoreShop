@product @product_price_rules @product_price_rules_condition_zone
Feature: Adding a new Product
  In order to extend my catalog
  the catalog has a price-rule for a zone
  I want to create a new product

  Background:
    Given the site operates on a store in "Austria"
    And the site has a zone "Europe"
    And the country "Austria" is in zone "Europe"
    And I am in country "Austria"
    And the site has a product "Shoe" priced at 100
    Then the product "Shoe" should be priced at "100"

  Scenario: Add a new zone category price rule which is valid
    Given adding a product price rule named "zone-discount"
    And the price rule is active
    And the price rule has a condition zones with zone "Europe"
    Then the price rule should be valid for product "Shoe"

  Scenario: Add a new zone category price rule which is invalid
    Given the site has a zone "Asia"
    Given adding a product price rule named "zone-discount"
    And the price rule is active
    And the price rule has a condition zones with zone "Asia"
    Then the price rule should be invalid for product "Shoe"