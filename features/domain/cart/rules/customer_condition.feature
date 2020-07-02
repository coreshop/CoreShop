@domain @cart
Feature: Adding a new cart rule
  In order to give the customer discounts
  based on the cart, we add a new rule
  with a customer condition

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has a customer "some-customer@something.com"
    And the cart belongs to customer "some-customer@something.com"

  Scenario: Add a new customer category price rule which is valid
    Given adding a cart price rule named "customer"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has a condition customers with customer "some-customer@something.com"
    Then the cart rule should be valid for my cart

  Scenario: Add a new customer category price rule which is invalid
    Given the site has a customer "some-other-customer@something.com"
    And adding a cart price rule named "customer"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has a condition customers with customer "some-other-customer@something.com"
    Then the cart rule should be invalid for my cart
