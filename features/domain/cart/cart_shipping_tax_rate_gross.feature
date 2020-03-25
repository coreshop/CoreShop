@domain
Feature: Create a new cart
  In order to know what taxes are applied
  we store the shipping tax rate into the cart

  Background:
    Given the site operates on a store in "Austria" with gross values
    And the site has a currency "Swiss Franc" with iso "CHF"
    And I am in country "Austria"
    And the site has a tax rate "CH" with "7.7%" rate
    And the site has a tax rule group "CH"
    And the tax rule group has a tax rule for country "Austria" with tax rate "CH"
    And the site has a carrier "Post"
    And the carrier has the tax rule group "CH"
    And adding a shipping rule named "post"
    And the shipping rule is active
    And the shipping rule has a action price of 10 in currency "CHF"
    And the shipping rule belongs to carrier "Post"
    And the site has a product "Shoe" priced at 10000
    And the product has the tax rule group "CH"

  Scenario: Create a new cart, add a product and shipping should be applied
    And I add the product "Shoe" to my cart
    Then the cart shipping should be "929" excluding tax
    And the cart shipping should be "1000" including tax
    And the loaded carts shipping tax rate should be "7.7"
