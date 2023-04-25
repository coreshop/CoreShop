@domain @shipping
Feature: Adding a new Shipping Rule
  In order to calculate shipping
  I'll create a new shipping rule
  with an amount condition based on the total value of the cart

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has a tax rate "AT" with "20%" rate
    And the site has a tax rule group "AT"
    And the tax rule group has a tax rule for country "Austria" with tax rate "AT"
    And adding a cart price rule named "100% discount"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has a action discount-percent with 100% discount
    And the site has a product "Shoe" priced at 10000
    And the product has the tax rule group "AT"
    And I add the product "Shoe" to my cart
    And the site has a carrier "Post"

  Scenario: Add a new amount shipping rule which is valid
    Given adding a shipping rule named "amount"
    And the shipping rule is active
    And the shipping rule has a condition amount from total "0" to "1"
    Then the shipping rule should be valid for my cart with carrier "Post"
