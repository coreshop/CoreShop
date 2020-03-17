@cart
Feature: Create a new cart with different currency then store base currency
  In Order to calculate taxes
  we create a cart and add items to it
  we also store base values for the selected currency and the store currency

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Swiss Franc" with iso "CHF"
    And the site has a country "Switzerland" with currency "CHF"
    And the country "Switzerland" is valid for store "Austria"
    And the currency "EUR" has a exchange-rate to currency "CHF" of "2"
    And my cart uses currency "CHF"
    And the site has a tax rate "AT" with "20%" rate
    And the site has a tax rule group "AT"
    And the tax rule group has a tax rule for country "Austria" with tax rate "AT"
    And the site has a product "T-Shirt" priced at 2000
    And the product has the tax rule group "AT"

  Scenario: Create a new cart and add a product and calculate item totals
    Given I add the product "T-Shirt" to my cart
    Then the cart item with product "T-Shirt" should have base discount-price with "0" including tax
    Then the cart item with product "T-Shirt" should have base discount-price with "0" excluding tax
    Then the cart item with product "T-Shirt" should have base discount-price with "0" excluding tax
    Then the cart item with product "T-Shirt" should have base discount with "0" including tax
    Then the cart item with product "T-Shirt" should have base discount with "0" excluding tax
    Then the cart item with product "T-Shirt" should have base retail-price with "2400" including tax
    Then the cart item with product "T-Shirt" should have base retail-price with "2000" excluding tax
    Then the cart item with product "T-Shirt" should have retail-price with "4800" including tax
    Then the cart item with product "T-Shirt" should have retail-price with "4000" excluding tax
    Then the cart item with product "T-Shirt" should have base total with "2400" including tax
    Then the cart item with product "T-Shirt" should have base total with "2000" excluding tax
    Then the cart item with product "T-Shirt" should have total with "4800" including tax
    Then the cart item with product "T-Shirt" should have total with "4000" excluding tax

  Scenario: Create a new cart and add a product with a price rule and calculate item totals
    Given adding a product specific price rule to product "T-Shirt" named "discount-percent"
    And the specific price rule is active
    And the specific price rule has a action discount-percent with 20% discount
    And I add the product "T-Shirt" to my cart
    Then the cart item with product "T-Shirt" should have discount with "960" including tax
    Then the cart item with product "T-Shirt" should have discount with "800" excluding tax
    Then the cart item with product "T-Shirt" should have base discount with "480" including tax
    Then the cart item with product "T-Shirt" should have base discount with "400" excluding tax
    Then the cart item with product "T-Shirt" should have retail-price with "4800" including tax
    Then the cart item with product "T-Shirt" should have retail-price with "4000" excluding tax
    Then the cart item with product "T-Shirt" should have base retail-price with "2400" including tax
    Then the cart item with product "T-Shirt" should have base retail-price with "2000" excluding tax
    Then the cart item with product "T-Shirt" should have total with "3840" including tax
    Then the cart item with product "T-Shirt" should have total with "3200" excluding tax
    Then the cart item with product "T-Shirt" should have base total with "1920" including tax
    Then the cart item with product "T-Shirt" should have base total with "1600" excluding tax

  Scenario: Create a new cart and add a product with a price rule and calculate item totals
    Given adding a product specific price rule to product "T-Shirt" named "discount-price"
    And the specific price rule is active
    And the specific price rule has a action discount-price of 10 in currency "EUR"
    And I add the product "T-Shirt" to my cart
    Then the cart item with product "T-Shirt" should have base discount-price with "1200" including tax
    Then the cart item with product "T-Shirt" should have base discount-price with "1000" excluding tax
    Then the cart item with product "T-Shirt" should have discount-price with "2400" including tax
    Then the cart item with product "T-Shirt" should have discount-price with "2000" excluding tax
    Then the cart item with product "T-Shirt" should have retail-price with "4800" including tax
    Then the cart item with product "T-Shirt" should have retail-price with "4000" excluding tax
    Then the cart item with product "T-Shirt" should have base retail-price with "2400" including tax
    Then the cart item with product "T-Shirt" should have base retail-price with "2000" excluding tax
    Then the cart item with product "T-Shirt" should have total with "2400" including tax
    Then the cart item with product "T-Shirt" should have total with "2000" excluding tax
    Then the cart item with product "T-Shirt" should have base total with "1200" including tax
    Then the cart item with product "T-Shirt" should have base total with "1000" excluding tax
