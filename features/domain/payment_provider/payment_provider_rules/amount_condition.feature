@domain @payment_provider
Feature: Adding a new payment-provider-rule
  In order to calculate payment provider
  I'll create a new payment-provider-rule
  with an amount condition

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has a product "Shoe" priced at 10000
    And I add the product "Shoe" to my cart
    And There is a payment provider "Bankwire" using factory "Bankwire"

  Scenario: Add a new amount payment-provider-rule which is valid
    Given adding a payment-provider-rule named "amount"
    And the payment-provider-rule is active
    And the payment-provider-rule has a condition amount from "50" to "150"
    Then the payment-provider-rule should be valid for my cart with payment provider "Bankwire"

  Scenario: Add a new amount payment-provider-rule which is inactive
    Given adding a payment-provider-rule named "amount"
    And the payment-provider-rule is inactive
    And the payment-provider-rule has a condition amount from "50" to "150"
    Then the payment-provider-rule should be invalid for my cart with payment provider "Bankwire"

  Scenario: Add a new amount shipping rule which is invalid
    Given adding a payment-provider-rule named "amount"
    And the payment-provider-rule is active
    And the payment-provider-rule has a condition amount from "50" to "99"
    Then the payment-provider-rule should be invalid for my cart with payment provider "Bankwire"

