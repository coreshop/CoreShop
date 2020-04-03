@domain @product
Feature: Adding a new Product
  In order to extend my catalog
  the catalog has a price-rule for a currency
  I want to create a new product

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And the site has a currency "USD" with iso "USD"
    And the site has a country "USA" with currency "USD"
    And the currency is valid for store "Austria"
    And I am in country "Austria"
    And the site has a product "Shoe" priced at 100
    Then the product "Shoe" should be priced at "100"
    Then the site should be using currency "EUR"

  Scenario: Add a new currency category price rule which is valid
    Given adding a product price rule named "currency-discount"
    And the price rule is active
    And the price rule has a condition currencies with currency "EUR"
    Then the price rule should be valid for product "Shoe"

  Scenario: Add a new currency category price rule which is invalid
    Given I am using currency "USD"
    And adding a product price rule named "currency-discount"
    And the price rule is active
    And the price rule has a condition currencies with currency "EUR"
    Then the price rule should be invalid for product "Shoe"
