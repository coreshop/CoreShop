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
    And the site has a carrier "Post"
    And adding a shipping rule named "post"
    And the shipping rule is active
    And the shipping rule has a action price of 20 in currency "EUR"
    And the shipping rule belongs to carrier "Post"

  Scenario: Add a new discount rule with free shipping
    Given adding a cart price rule named "free-shipping"
    And the cart rule is inactive
    And the cart rule is not a voucher rule
    And the cart rule has a action free-shipping
    And I add the product "Shoe" to my cart
    And the cart shipping should be "2000" excluding tax
    And the cart shipping should be "2000" including tax
    And the cart rule is active
    And I refresh my cart
    Then the cart discount should be "0" including tax
    And the cart total should be "10000" including tax
    And the cart shipping should be "0" excluding tax
    And the cart shipping should be "0" including tax

  Scenario: Add a new discount rule with free shipping with two valid rules
    Given adding a cart price rule named "free-shipping"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has a action free-shipping
    Given adding a cart price rule named "free-shipping2"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has a action free-shipping
    And I add the product "Shoe" to my cart
    Then the cart discount should be "0" including tax
    And the cart total should be "10000" including tax
    And the cart shipping should be "0" excluding tax
    And the cart shipping should be "0" including tax
