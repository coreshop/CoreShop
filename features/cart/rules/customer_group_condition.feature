@cart @cart_rules @cart_rule_condition_customer_group
Feature: Adding a new cart rule
  In order to give the customer discounts
  based on the cart, we add a new rule
  with a customer group condition

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And the site has a customer-group "Frequent Buyers"
    And the site has a customer "some-customer@something.com"
    And it is in customer-group "Frequent Buyers"
    And the cart belongs to customer "some-customer@something.com"

  Scenario: Add a new customer-group cart rule which is valid
    Given adding a cart price rule named "customer-group"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has a condition customer-groups with customer-group "Frequent Buyers"
    Then the cart rule should be valid for my cart

  Scenario: Add a new customer-group cart rule which is invalid
    Given the site has a customer-group "New Customers"
    And the customer "some-customer@something.com" is in customer-group "New Customers"
    And adding a cart price rule named "customer-group"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has a condition customer-groups with customer-group "Frequent Buyers"
    Then the cart rule should be invalid for my cart