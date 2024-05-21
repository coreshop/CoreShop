@domain @cart
Feature: Adding a new cart rule
  In order to give the customer discounts
  based on the cart, we add a new rule
  with a guest condition

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has a customer "some-customer@something.com"
    And the site has a guest "some-guest@something.com"
    And adding a cart price rule named "guest"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has a condition guest

  Scenario: Add a new guest cart rule for a guest customer which is valid
    Given the cart belongs to guest "some-guest@something.com"
    Then the cart rule should be valid for my cart

  Scenario: Add a new guest cart rule for a cart without a customer which is valid
    Then the cart rule should be valid for my cart

  Scenario: Add a new guest cart rule for a customer which is invalid
    Given the cart belongs to customer "some-customer@something.com"
    Then the cart rule should be invalid for my cart