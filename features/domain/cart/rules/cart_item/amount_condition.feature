@domain @cart @cart_item
Feature: Adding a new cart item rule
  In order to give the customer discounts
  based on the cart, we add a new rule
  with a amount condition for specific cart items

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has two categories "Shoes" and "Coats"
    And the site has a category "Sneakers"
    And the category "Sneakers" is child of category "Shoes"
    And the site has a product "Shoe" priced at 10000
    And it is in category "Shoes"
    And the site has a product "Shoe 2" priced at 15000
    And it is in category "Shoes"
    And the site has a product "Jacket" priced at 40000
    And it is in category "Coats"
    And the site has a product "Sneaker" priced at 350000
    And it is in category "Sneakers"
    And I add the product "Shoe" to my cart

  Scenario: Add a new amount condition with is valid
    Given adding a cart price rule named "amount"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has a cart-item-action action
    And the cart item action has a condition amount with value "90" to "150"
    And the cart item action has a action discount-percent with 10% discount
    Then I refresh my cart
    And the cart discount should be "-1000" excluding tax

  Scenario: Add a new amount condition with is invalid cause of min value
    Given adding a cart price rule named "amount"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has a cart-item-action action
    And the cart item action has a condition amount with value "120" to "500"
    And the cart item action has a action discount-percent with 10% discount
    Then the cart rule should be valid for my cart
    And the cart discount should be "0" excluding tax

  Scenario: Add a new amount condition with is invalid cause of max value
    Given adding a cart price rule named "amount"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has a cart-item-action action
    And the cart item action has a condition amount with value "10" to "90"
    And the cart item action has a action discount-percent with 10% discount
    Then the cart rule should be valid for my cart
    And the cart discount should be "0" excluding tax
