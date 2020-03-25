@cart @domain
Feature: Adding a new cart rule
  In order to give the customer discounts
  based on the cart, we add a new rule

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has two categories "Shoes" and "Coats"
    And the site has a product "Shoe" priced at 10000
    And it is in category "Shoes"
    And the site has a product "Shoe 2" priced at 15000
    And it is in category "Shoes"
    And the site has a product "Jacket" priced at 40000
    And it is in category "Coats"
    And I add the product "Shoe" to my cart

  Scenario: Add a new discount rule with 20 percent discount for all products
    Given adding a cart price rule named "discount"
    And the cart rule is active
    And the cart rule is a voucher rule with code "asdf"
    And the cart rule has a action discount-percent with 10% discount
    And I apply the voucher code "asdf" to my cart
    Then the cart discount should be "-1000" including tax
    Then the cart total should be "9000" including tax
