@domain @cart
Feature: Create a new cart
  In Order for a customer to purchase something
  he needs to create a cart first
  and put items into it

  Background:
    Given the site operates on a store in "Austria"
    And the site has a country "Germany" with currency "EUR"
    And the country "Germany" is active
    And the site has a tax rate "AT" with "20%" rate
    And the site has a tax rule group "AT"
    And the tax rule group has a tax rule for country "Austria" with tax rate "AT"
    And the site has a product "T-Shirt" priced at 2000
    And the product has the tax rule group "AT"
    And the site has a product-unit "Pieces"
    And the site has a product-unit "Carton"
    And the site has a product-unit "Palette"
    And the product has the default unit "Pieces"
    And the product has an additional unit "Carton" with conversion rate "24"
    And the product has an additional unit "Palette" with conversion rate "200"

  Scenario: Create a new cart and add a product
    Given I add the product "T-Shirt" with unit "Pieces" to my cart
    Then there should be one product in my cart
    And the first item in my cart should have unit "Pieces"

  Scenario: Create a new cart and add a product with different unities
    Given I add the product "T-Shirt" with unit "Pieces" to my cart
    And I add the product "T-Shirt" with unit "Carton" to my cart
    Then there should be two products in my cart
    And the first item in my cart should have unit "Pieces"
    And the second item in my cart should have unit "Carton"
