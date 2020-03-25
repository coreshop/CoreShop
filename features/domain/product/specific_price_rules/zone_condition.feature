@product @domain
Feature: Adding a new Product
  In order to extend my catalog
  The product has a specific-price-rule for a zone
  I want to create a new product

  Background:
    Given the site operates on a store in "Austria"
    And the site has a zone "Europe"
    And the country "Austria" is in zone "Europe"
    And I am in country "Austria"
    And the site has a product "Shoe" priced at 100
    Then the product "Shoe" should be priced at "100"

  Scenario: Add a new zone product specific price rule which is valid
    Given adding a product specific price rule to product "Shoe" named "zone-discount"
    And the specific price rule is active
    And the specific price rule has a condition zones with zone "Europe"
    Then the specific price rule should be valid for product "Shoe"

  Scenario: Add a new zone product specific price rule which is invalid
    Given the site has a zone "Asia"
    Given adding a product specific price rule to product "Shoe" named "zone-discount"
    And the specific price rule is active
    And the specific price rule has a condition zones with zone "Asia"
    Then the specific price rule should be invalid for product "Shoe"
