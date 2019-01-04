@cart @cart_create
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

  Scenario: Create a new cart and add a product
    Given I add the product "T-Shirt" to my cart
    Then there should be one product in my cart

  Scenario: Create a new cart and add a product and calculate totals
    Given I add the product "T-Shirt" to my cart
    Then there should be one product in my cart
    And the cart total should be "2400" including tax
    And the cart total should be "2000" excluding tax

  Scenario: Create a new cart and add a product and calculate subtotals
    Given I add the product "T-Shirt" to my cart
    Then there should be one product in my cart
    And the cart subtotal should be "2400" including tax
    And the cart subtotal should be "2000" excluding tax

  Scenario: Create a new cart and add a product when I come from a different country
    Given the site has a country "Germany" with currency "EUR"
    And the country "Germany" is active
    And the country "Germany" is valid for store "Austria"
    And I am in country "Germany"
    And I add the product "T-Shirt" to my cart
    Then there should be one product in my cart
    And the cart total should be "2000" including tax
    And the cart total should be "2000" excluding tax
    And the cart subtotal should be "2000" including tax
    And the cart subtotal should be "2000" excluding tax