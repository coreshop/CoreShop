@cart @cart_rules @cart_rule_condition_timespan
Feature: Adding a new cart rule
  In order to give the customer discounts
  based on the cart, we add a new rule
  with a timespan condition

  Background:
    Given the site operates on a store in "Austria"
    Given the site has a product "Shoe" priced at 100

  Scenario: Add a new timespan cart rule which is valid
    Given adding a cart price rule named "timespan"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has a condition timespan which is valid from "yesterday" to "tomorrow"
    Then the cart rule should be valid for my cart

  Scenario: Add a new timespan cart rule which is invalid
    Given adding a cart price rule named "timespan"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has a condition timespan which is valid from "10 days ago" to "yesterday"
    Then the cart rule should be invalid for my cart