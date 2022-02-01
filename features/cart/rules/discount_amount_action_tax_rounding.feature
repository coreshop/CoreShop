@domain @cart
Feature: Adding a new cart rule
  In order to give the customer discounts
  based on the cart, we add a new rule

  Background:
    Given the site operates on a store in "Austria" with gross values
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has a tax rate "AT" with "20%" rate
    And the site has a tax rule group "AT"
    And the tax rule group has a tax rule for country "Austria" with tax rate "AT"
    And the site has a product "Dinner" priced at 5900
    And the product has the tax rule group "AT"
    And the site has a product "Summer" priced at 1000
    And the product has the tax rule group "AT"
    And the site has a product "Winter" priced at 500
    And the product has the tax rule group "AT"
    And the site has a product "Potential" priced at 21000
    And the product has the tax rule group "AT"
    And the site has a product "Voucher" priced at 1000
    And I add the product "Dinner" to my cart
    And I add the product "Summer" to my cart
    And I add the product "Summer" to my cart
    And I add the product "Winter" to my cart
    And I add the product "Potential" to my cart
    And I add the product "Voucher" to my cart

  Scenario: Add a new discount rule with 10 € discount
    And adding a cart price rule named "discount"
    And the cart rule is active
    And the cart rule is a voucher rule with code "TEI6CXCBAL"
    And the voucher code "TEI6CXCBAL" is a credit voucher with credit "1000" in currency "EUR"
    And the cart rule has a action voucher credit
    And I apply the voucher code "TEI6CXCBAL" to my cart
    Then the cart discount should be "-840" excluding tax
    Then the cart discount should be "-1000" including tax
    Then the cart total tax should be "4738"
    Then the cart subtotal should be "25500" excluding tax
    Then the cart subtotal should be "30400" including tax
    Then the cart total should be "24660" excluding tax
    Then the cart total should be "29400" including tax
  
