@product @domain
Feature: Adding a new Product
  In order to extend my catalog
  The product has a specific-price-rule for a store
  I want to create a new product

  Background:
    Given the site operates on a store in "Austria"
    And the site has a product "Shoe" priced at 100
    And the site has a currency "YEN" with iso "YEN"
    And the site has a country "China" with currency "YEN"
    And the site has a store "Asia" with country "China" and currency "Yen"
    Then the product "Shoe" should be priced at "100"

  Scenario: Add a new store product specific price rule which is valid
    Given adding a product specific price rule to product "Shoe" named "store-discount"
    And the specific price rule is active
    And the specific price rule has a condition stores with store "Austria"
    Then the specific price rule should be valid for product "Shoe"

  Scenario: Add a new store product specific price rule which is invalid
    Given adding a product specific price rule to product "Shoe" named "store-discount"
    And the specific price rule is active
    And the specific price rule has a condition stores with store "Asia"
    Then the specific price rule should be invalid for product "Shoe"
