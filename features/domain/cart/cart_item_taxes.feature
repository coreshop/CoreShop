@domain @cart
Feature: Create a new cart where store uses net values
  In Order to calculate taxes
  we create a cart and add items to it

  Background:
    Given the site operates on a store in "Austria"
    And the site has a tax rate "AT" with "20%" rate
    And the site has a tax rule group "AT"
    And the tax rule group has a tax rule for country "Austria" with tax rate "AT"
    And the site has a tax rate "CH" with "7.7%" rate
    And the site has a tax rule group "CH"
    And the tax rule group has a tax rule for country "Austria" with tax rate "CH"
    And the site has a product "Shoes" priced at 1000
    And the product has the tax rule group "AT"
    And the site has a product "T-Shirt" priced at 2000
    And the product has the tax rule group "AT"
    And the site has a product "Shorts" priced at 3000
    And the product has the tax rule group "AT"
    And the site has a product "CH T-Shirt" priced at 1590
    And the product has the tax rule group "CH"

  Scenario: Create a new cart and add a product and calculate item totals
    Given I add the product "T-Shirt" to my cart
    Then the cart item with product "T-Shirt" should have discount-price with "0" including tax
    Then the cart item with product "T-Shirt" should have discount-price with "0" excluding tax
    Then the cart item with product "T-Shirt" should have discount with "0" including tax
    Then the cart item with product "T-Shirt" should have discount with "0" excluding tax
    Then the cart item with product "T-Shirt" should have retail-price with "2400" including tax
    Then the cart item with product "T-Shirt" should have retail-price with "2000" excluding tax
    Then the cart item with product "T-Shirt" should have total with "2400" including tax
    Then the cart item with product "T-Shirt" should have total with "2000" excluding tax

  Scenario: Create a new cart and add a product with a price ruleand calculate item totals
    Given adding a product specific price rule to product "T-Shirt" named "discount-percent"
    And the specific price rule is active
    And the specific price rule has a action discount-percent with 20% discount
    And I add the product "T-Shirt" to my cart
    Then the cart item with product "T-Shirt" should have discount-price with "0" including tax
    Then the cart item with product "T-Shirt" should have discount-price with "0" excluding tax
    Then the cart item with product "T-Shirt" should have discount with "480" including tax
    Then the cart item with product "T-Shirt" should have discount with "400" excluding tax
    Then the cart item with product "T-Shirt" should have retail-price with "2400" including tax
    Then the cart item with product "T-Shirt" should have retail-price with "2000" excluding tax
    Then the cart item with product "T-Shirt" should have total with "1920" including tax
    Then the cart item with product "T-Shirt" should have total with "1600" excluding tax

  Scenario: Create a new cart and add a product with a price ruleand calculate item totals
    Given adding a product specific price rule to product "T-Shirt" named "discount-percent"
    And the specific price rule is active
    And the specific price rule has a action discount-price of 15 in currency "EUR"
    And I add the product "T-Shirt" to my cart
    Then the cart item with product "T-Shirt" should have discount-price with "1800" including tax
    Then the cart item with product "T-Shirt" should have discount-price with "1500" excluding tax
    Then the cart item with product "T-Shirt" should have discount with "0" including tax
    Then the cart item with product "T-Shirt" should have discount with "0" excluding tax
    Then the cart item with product "T-Shirt" should have retail-price with "2400" including tax
    Then the cart item with product "T-Shirt" should have retail-price with "2000" excluding tax
    Then the cart item with product "T-Shirt" should have total with "1800" including tax
    Then the cart item with product "T-Shirt" should have total with "1500" excluding tax

