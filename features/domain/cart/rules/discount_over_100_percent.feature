@domain @cart
Feature: Adding a new cart rule
  In order to give the customer discounts
  based on the cart, we add a new rule with a 100% discount

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

  Scenario: Add a new discount rule with 100 percent discount
    Given adding a cart price rule named "discount"
    And the cart rule is a voucher rule with code "asdf"
    And the cart rule is active
    And the cart rule has a action discount-percent with 10% discount
    Given adding a cart price rule named "discount2"
    And the cart rule is a voucher rule with code "asdf2"
    And the cart rule is active
    And the cart rule has a action discount-percent with 100% discount
    And I apply the voucher code "asdf2" to my cart
    Then the cart discount should be "-12000" including tax
    Then the cart discount should be "-10000" excluding tax
    Then the cart total tax should be "0"
    Then the cart total should be "0" excluding tax
    Then the cart total should be "0" including tax
