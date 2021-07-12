@domain @cart
Feature: Create a new cart
  In order to know what taxes are applied
  we store the shipping tax rate into the cart

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
    And the site has a carrier "Post"
    And the carrier has the tax rule group "AT"
    And adding a shipping rule named "post"
    And the shipping rule is active
    And the shipping rule has a action price of 10 in currency "EUR"
    And the shipping rule belongs to carrier "Post"

  Scenario: Create a new cart, add a product and shipping should be applied
    And I add the product "T-Shirt" to my cart
    Then the cart shipping should be "1000" excluding tax
    And the cart shipping should be "1200" including tax
    Then the cart converted shipping should be "2000" excluding tax
    And the cart converted shipping should be "2400" including tax
