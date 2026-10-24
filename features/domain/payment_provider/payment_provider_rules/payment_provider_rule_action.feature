@domain @payment_provider
Feature: Adding a new Payment Provider Rule
  In order to reuse payment provider rule prices
  I'll create a new payment-provider-rule
  with a payment-provider-rule action

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has a product "Shoe" priced at 10000
    And I add the product "Shoe" to my cart
    And There is a payment provider "Bankwire" using factory "Bankwire"
    And adding a payment-provider-rule named "base"
    And the payment-provider-rule is active
    And the payment-provider-rule has a action price of 100 in currency "EUR"
    And adding a payment-provider-rule named "fixed"
    And the payment-provider-rule is active
    And the payment-provider-rule has a action price of 5 in currency "EUR"
    And the payment-provider-rule belongs to payment provider "Bankwire"

  Scenario: Creating a Payment Provider Rule which reuses the price of another Payment Provider Rule
    Given the payment-provider-rule "fixed" has a action payment-provider-rule with payment-provider-rule "base"
    Then payment for my cart with payment provider "Bankwire" should be priced at "10500"
    And the payment-provider-rule "fixed" should be saveable without a circular reference

  Scenario: Creating a Payment Provider Rule which reuses its own price
    Given the payment-provider-rule "fixed" has a action payment-provider-rule with payment-provider-rule "fixed"
    Then payment for my cart with payment provider "Bankwire" should be priced at "500"
    And the payment-provider-rule "fixed" should not be saveable because of a circular reference

  Scenario: Creating two Payment Provider Rules which reuse each others price
    Given the payment-provider-rule "fixed" has a action payment-provider-rule with payment-provider-rule "base"
    And the payment-provider-rule "base" has a action payment-provider-rule with payment-provider-rule "fixed"
    Then payment for my cart with payment provider "Bankwire" should be priced at "10500"
    And the payment-provider-rule "fixed" should not be saveable because of a circular reference
    And the payment-provider-rule "base" should not be saveable because of a circular reference
