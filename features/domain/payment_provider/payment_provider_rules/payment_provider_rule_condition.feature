@domain @payment_provider
Feature: Adding a new payment-provider-rule
  In order to reuse payment-provider-rules
  I'll create a new payment-provider-rule
  with a payment-provider-rule condition

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has a product "Shoe" priced at 10000
    And I add the product "Shoe" to my cart
    And There is a payment provider "Bankwire" using factory "Bankwire"

  Scenario: Add a new payment-provider-rule which reuses a valid payment-provider-rule
    Given adding a payment-provider-rule named "amount"
    And the payment-provider-rule is active
    And the payment-provider-rule has a condition amount from "50" to "150"
    And adding a payment-provider-rule named "reuse"
    And the payment-provider-rule is active
    And the payment-provider-rule has a condition payment-provider-rule with payment-provider-rule "amount"
    Then the payment-provider-rule "reuse" should be valid for my cart with payment provider "Bankwire"
    And the payment-provider-rule "reuse" should be saveable without a circular reference

  Scenario: Add a new payment-provider-rule which reuses itself
    Given adding a payment-provider-rule named "self"
    And the payment-provider-rule is active
    And the payment-provider-rule has a condition payment-provider-rule with payment-provider-rule "self"
    Then the payment-provider-rule "self" should be invalid for my cart with payment provider "Bankwire"
    And the payment-provider-rule "self" should not be saveable because of a circular reference

  Scenario: Add two payment-provider-rules which reuse each other
    Given adding a payment-provider-rule named "first"
    And the payment-provider-rule is active
    And adding a payment-provider-rule named "second"
    And the payment-provider-rule is active
    And the payment-provider-rule "first" has a condition payment-provider-rule with payment-provider-rule "second"
    And the payment-provider-rule "second" has a condition payment-provider-rule with payment-provider-rule "first"
    Then the payment-provider-rule "first" should be invalid for my cart with payment provider "Bankwire"
    And the payment-provider-rule "first" should not be saveable because of a circular reference
    And the payment-provider-rule "second" should not be saveable because of a circular reference
