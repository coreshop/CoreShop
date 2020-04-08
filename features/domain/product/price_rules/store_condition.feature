@domain @product
Feature: Adding a new Product
  In order to extend my catalog
  the catalog has a price-rule for a store
  I want to create a new product

  Background:
    Given the site operates on a store in "Austria"
    And the site has a product "Shoe" priced at 100
    And the site has a currency "YEN" with iso "YEN"
    And the site has a country "China" with currency "YEN"
    And the site has a store "Asia" with country "China" and currency "Yen"
    Then the product "Shoe" should be priced at "100"

  Scenario: Add a new store category price rule which is valid
    Given adding a product price rule named "store-discount"
    And the price rule is active
    And the price rule has a condition stores with store "Austria"
    Then the price rule should be valid for product "Shoe"

  Scenario: Add a new store category price rule which is invalid
    Given adding a product price rule named "store-discount"
    And the price rule is active
    And the price rule has a condition stores with store "Asia"
    Then the price rule should be invalid for product "Shoe"
