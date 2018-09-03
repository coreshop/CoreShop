@cart @cart_taxation @cart_taxation_net
Feature: Create a new cart
  In Order to calculate taxes
  we create a cart and add items to it

  Background:
    Given the site operates on a store in "Austria"
    And the site has a tax rate "AT" with "20%" rate
    And the site has a tax rule group "AT"
    And the tax rule group has a tax rule for country "Austria" with tax rate "AT"
    And the tax rule group is valid for store "Austria"
    And the site has a tax rate "CH" with "7.7%" rate
    And the site has a tax rule group "CH"
    And the tax rule group has a tax rule for country "Austria" with tax rate "CH"
    And the tax rule group is valid for store "Austria"
    And the site has a product "Shoes" priced at 1000
    And the product has the tax rule group "AT"
    And the site has a product "T-Shirt" priced at 2000
    And the product has the tax rule group "AT"
    And the site has a product "Shorts" priced at 3000
    And the product has the tax rule group "AT"
    And the site has a product "CH T-Shirt" priced at 1590
    And the product has the tax rule group "CH"

  Scenario: Create a new cart and add a product and calculate totals
    Given I add the product "T-Shirt" to my cart
    Then the cart total tax should be "400"

  Scenario: Create a new cart and add a product and calculate subtotals
    Given I add the product "T-Shirt" to my cart
    Given I add the product "Shorts" to my cart
    Then the cart total tax should be "1000"

  Scenario: Create a new cart and add a product and calculate totals
    Given I add the product "T-Shirt" to my cart
    Then the cart total tax should be "400"

  Scenario: Create a new cart, add a product and calculate subtotals
    Given I add the product "T-Shirt" to my cart
    Given I add the product "Shorts" to my cart
    Then the cart total tax should be "1000"

  Scenario: Create a new cart, add a product and calculate subtotals
    Given I add the product "Shoes" to my cart
    Given I add the product "Shoes" to my cart
    Given I add the product "Shoes" to my cart
    Given I add the product "Shoes" to my cart
    Given I add the product "Shoes" to my cart
    Then the cart total tax should be "1000"

  Scenario: Create a new cart for a different Tax Rate, add a product and calculate subtotals
    Given I add the product "CH T-Shirt" to my cart
    Given I add the product "CH T-Shirt" to my cart
    Then the cart total tax should be "245"
