@product @domain
Feature: Adding a new Product
  In order to extend my catalog
  The catalog has a price-rule for a country
  I want to create a new product

  Background:
    Given the site operates on a store in "Austria"
    Given the site has a currency "Euro" with iso "EUR"
    Given I am in country "Austria"
    Given the site has a category "Shoes"
    Given the site has a product "Shoe" priced at 100
    Given it is in category "Shoes"
    Given the site has a product "Shoe 2" priced at 150
    Given it is in category "Shoes"
    Then the product "Shoe" should be priced at "100"
    Then the product "Shoe 2" should be priced at "150"

  Scenario: Add a new country category price rule which is valid
    Given adding a product price rule named "country-discount"
    And the price rule is active
    And the price rule has a condition countries with country "Austria"
    Then the price rule should be valid for product "Shoe"
    Then the price rule should be valid for product "Shoe 2"

  Scenario: Add a new country category price rule which is invalid
    Given the site has a country "Germany" with currency "EUR"
    Given adding a product price rule named "country-discount"
    And the price rule is active
    And the price rule has a condition countries with country "Germany"
    Then the price rule should be invalid for product "Shoe"
    Then the price rule should be invalid for product "Shoe 2"
