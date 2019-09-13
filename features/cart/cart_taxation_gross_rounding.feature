@cart @cart_taxation @cart_taxation_gross_rounding
Feature: Create a new cart where store uses gross values
  In Order to calculate taxes and round them right
  we create a cart and add items to it

  Background:
    Given the site operates on a store in "Austria" with gross values
    And the site has a tax rate "DE" with "19%" rate
    And the site has a tax rule group "DE"
    And the tax rule group has a tax rule for country "Austria" with tax rate "DE"
    And the site has a product "Shoes" priced at 10500
    And the product has the tax rule group "DE"

  Scenario: Create a new cart and add a product and calculate totals
    Given I add the product "Shoes" to my cart
    Then the cart total tax should be "1676"
