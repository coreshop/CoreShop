@product @domain
Feature: Adding a new Product
  In order to extend my catalog
  The product has a specific-price-rule for a country
  I want to create a new product

  Background:
    Given the site operates on a store in "Austria"
    Given the site has a currency "Euro" with iso "EUR"
    Given I am in country "Austria"
    Given the site has a product "Shoe" priced at 100
    Given the site has a category "Shoes"
    Given the product "Shoe" is in category "Shoes"
    Then the product "Shoe" should be priced at "100"

  Scenario: Add a new country product specific price rule which is valid
    Given adding a product specific price rule to product "Shoe" named "country-discount"
    And the specific price rule is active
    And the specific price rule has a condition countries with country "Austria"
    Then the specific price rule should be valid for product "Shoe"

  Scenario: Add a new country product specific price rule which is invalid
    Given the site has a country "Germany" with currency "EUR"
    Given adding a product specific price rule to product "Shoe" named "country-discount"
    And the specific price rule is active
    And the specific price rule has a condition countries with country "Germany"
    Then the specific price rule should be invalid for product "Shoe"
