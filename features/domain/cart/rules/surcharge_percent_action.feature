@domain @cart
Feature: Adding a new cart rule
  In order to give the customer discounts
  based on the cart, we add a new rule

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has a tax rate "AT" with "20%" rate
    And the site has a tax rule group "AT"
    And the tax rule group has a tax rule for country "Austria" with tax rate "AT"
    And the site has a product "Shoe" priced at 10000
    And the product has the tax rule group "AT"
    And I add the product "Shoe" to my cart

  Scenario: Add a new surcharge rule with 20 percent for all products
    Given adding a cart price rule named "surcharge"
    And the cart rule is active
    And the cart rule is a voucher rule with code "asdf"
    And the cart rule has a action surcharge-percent with 20% discount
    And I apply the voucher code "asdf" to my cart
    Then the cart discount should be "2400" including tax
    Then the cart discount should be "2000" excluding tax
    Then the cart total tax should be "2400"
    Then the cart item taxes should be "2400"
    Then the cart total should be "12000" excluding tax
    Then the cart total should be "14400" including tax
