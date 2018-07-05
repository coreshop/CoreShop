@shipping @shipping_rules @shipping_rule_actions
Feature: Adding a new Shipping Rule
  In order to calculate shipping
  I'll create a new shipping rule
  with different actions

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has a tax rate "AT" with "20%" rate
    And the site has a tax rule group "AT"
    And the tax rule group has a tax rule for country "Austria" with tax rate "AT"
    And the tax rule group is valid for store "Austria"
    And the site has a product "Shoe" priced at 10000
    And the product has the tax rule group "AT"
    And I add the product "Shoe" to my cart
    And the site has a carrier "Post"
    And adding a shipping rule named "fixed"
    And the shipping rule is active
    And the shipping rule has a action price of 20 in currency "EUR"
    And the shipping rule belongs to carrier "Post"
    Then the shipping rule should be valid for my cart with carrier "Post"

  Scenario: Creating a Shipping Rule with a fixed price
    Then shipping for my cart with carrier "Post" should be priced at "2000"

  Scenario: Creating a Shipping Rule with a fixed price plus and additional amount
    Given the shipping rule has a action additional-amount of 5 in currency "EUR"
    Then shipping for my cart with carrier "Post" should be priced at "2500"

  Scenario: Creating a Shipping Rule with a fixed price plus and additional percent
    Given the shipping rule has a action additional-percent of 10%
    Then shipping for my cart with carrier "Post" should be priced at "2200"

  Scenario: Creating a Shipping Rule with a fixed price plus and discount amount
    Given the shipping rule has a action discount-amount of 5 in currency "EUR"
    Then shipping for my cart with carrier "Post" should be priced at "1500"

  Scenario: Creating a Shipping Rule with a fixed price plus and discount percent
    Given the shipping rule has a action discount-percent of 10%
    Then shipping for my cart with carrier "Post" should be priced at "1800"