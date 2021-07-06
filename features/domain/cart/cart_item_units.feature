@domain @cart
Feature: Create a new cart and check it's units

  Background:
    Given the site operates on a store in "Austria"
    And the site has a tax rate "AT" with "20%" rate
    And the site has a tax rule group "AT"
    And the site has a product "Shoes" priced at 1000
    And the product has the tax rule group "AT"
    And the site has a product "T-Shirt" priced at 2000
    And the product has the tax rule group "AT"
    And the site has a product "Shorts" priced at 3000
    And the product has the tax rule group "AT"

  Scenario: Create a new cart and add a product
    Given I add the product "T-Shirt" to my cart
    Then the cart item with product "T-Shirt" should have one unit

  Scenario: Create a new cart and add a product multiple times
    Given I add the product "T-Shirt" to my cart
    Given I add the product "T-Shirt" to my cart
    Given I add the product "T-Shirt" to my cart
    Given I add the product "T-Shirt" to my cart
    Then the cart item with product "T-Shirt" should have 4 units
