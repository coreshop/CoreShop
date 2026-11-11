@domain @shipping
Feature: Adding a new Shipping Rule
  In order to reuse shipping rules
  I'll create a new shipping rule
  with a shipping-rule condition

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

  Scenario: Add a new shipping rule which reuses a valid shipping rule
    Given adding a shipping rule named "amount"
    And the shipping rule is active
    And the shipping rule has a condition amount from "50" to "150"
    And adding a shipping rule named "reuse"
    And the shipping rule is active
    And the shipping rule has a condition shipping-rule with shipping rule "amount"
    Then the shipping rule "reuse" should be valid for my cart with carrier "Post"
    And the shipping rule "reuse" should be saveable without a circular reference

  Scenario: Add a new shipping rule which reuses an invalid shipping rule
    Given adding a shipping rule named "amount"
    And the shipping rule is active
    And the shipping rule has a condition amount from "50" to "119"
    And adding a shipping rule named "reuse"
    And the shipping rule is active
    And the shipping rule has a condition shipping-rule with shipping rule "amount"
    Then the shipping rule "reuse" should be invalid for my cart with carrier "Post"

  Scenario: Add a new shipping rule which reuses itself
    Given adding a shipping rule named "self"
    And the shipping rule is active
    And the shipping rule has a condition shipping-rule with shipping rule "self"
    Then the shipping rule "self" should be invalid for my cart with carrier "Post"
    And the shipping rule "self" should not be saveable because of a circular reference

  Scenario: Add two shipping rules which reuse each other
    Given adding a shipping rule named "first"
    And the shipping rule is active
    And adding a shipping rule named "second"
    And the shipping rule is active
    And the shipping rule "first" has a condition shipping-rule with shipping rule "second"
    And the shipping rule "second" has a condition shipping-rule with shipping rule "first"
    Then the shipping rule "first" should be invalid for my cart with carrier "Post"
    And the shipping rule "second" should be invalid for my cart with carrier "Post"
    And the shipping rule "first" should not be saveable because of a circular reference
    And the shipping rule "second" should not be saveable because of a circular reference

  Scenario: Add three shipping rules which reuse each other in a circle
    Given adding a shipping rule named "first"
    And the shipping rule is active
    And adding a shipping rule named "second"
    And the shipping rule is active
    And adding a shipping rule named "third"
    And the shipping rule is active
    And the shipping rule "first" has a condition shipping-rule with shipping rule "second"
    And the shipping rule "second" has a condition shipping-rule with shipping rule "third"
    And the shipping rule "third" has a condition shipping-rule with shipping rule "first"
    Then the shipping rule "first" should be invalid for my cart with carrier "Post"
    And the shipping rule "third" should not be saveable because of a circular reference

  Scenario: Add a shipping rule which reuses itself inside a nested condition
    Given adding a shipping rule named "first"
    And the shipping rule is active
    And adding a shipping rule named "second"
    And the shipping rule is active
    And the shipping rule "first" has a condition nested with operator "and" with shipping rule "second"
    And the shipping rule "second" has a condition shipping-rule with shipping rule "first"
    Then the shipping rule "first" should be invalid for my cart with carrier "Post"
    And the shipping rule "first" should not be saveable because of a circular reference

  Scenario: A shipping rule reusing a circle of other shipping rules is still saveable
    Given adding a shipping rule named "first"
    And the shipping rule is active
    And adding a shipping rule named "second"
    And the shipping rule is active
    And adding a shipping rule named "reuse"
    And the shipping rule is active
    And the shipping rule "first" has a condition shipping-rule with shipping rule "second"
    And the shipping rule "second" has a condition shipping-rule with shipping rule "first"
    And the shipping rule "reuse" has a condition shipping-rule with shipping rule "first"
    Then the shipping rule "reuse" should be invalid for my cart with carrier "Post"
    And the shipping rule "reuse" should be saveable without a circular reference
