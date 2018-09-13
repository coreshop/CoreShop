@shipping @shipping_rules @shipping_rule_condition_amount
Feature: Adding a new Shipping Rule
  In order to calculate shipping
  I'll create a new shipping rule
  with an amount condition

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
    And the site has a carrier "Post"

  Scenario: Add a new amount shipping rule which is valid
    Given adding a shipping rule named "amount"
    And the shipping rule is active
    And the shipping rule has a condition amount from "50" to "150"
    Then the shipping rule should be valid for my cart with carrier "Post"

  Scenario: Add a new amount shipping rule which is inactive
    Given adding a shipping rule named "amount"
    And the shipping rule is inactive
    And the shipping rule has a condition amount from "50" to "150"
    Then the shipping rule should be invalid for my cart with carrier "Post"

  Scenario: Add a new amount shipping rule which is invalid
    Given adding a shipping rule named "amount"
    And the shipping rule is active
    And the shipping rule has a condition amount from "50" to "99"
    Then the shipping rule should be invalid for my cart with carrier "Post"
