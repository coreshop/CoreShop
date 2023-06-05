@domain @payment_provider
Feature: Adding a new Payment Provider Rule
  In order to calculate payment provider
  I'll create a new payment-provider-rule
  with different actions

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has a product "Shoe" priced at 10000
    And I add the product "Shoe" to my cart
    And There is a payment provider "Bankwire" using factory "Bankwire"
    And adding a payment-provider-rule named "fixed"
    And the payment-provider-rule is active
    And the payment-provider-rule has a action price of 100 in currency "EUR"
    And the payment-provider-rule belongs to payment provider "Bankwire"
    Then the payment-provider-rule should be valid for my cart with payment provider "Bankwire"

  Scenario: Creating a Payment Provider Rule with a fixed price
    Then payment for my cart with payment provider "Bankwire" should be priced at "10000"

  Scenario: Creating a Payment Provider Rule with a fixed price plus and additional amount
    Given the payment-provider-rule has a action additional-amount of 5 in currency "EUR"
    Then payment for my cart with payment provider "Bankwire" should be priced at "10500"

  Scenario: Creating a Payment Provider Rule with a fixed price plus and additional percent
    Given the payment-provider-rule has a action additional-percent of 10%
    Then payment for my cart with payment provider "Bankwire" should be priced at "11000"

  Scenario: Creating a Payment Provider Rule with a fixed price plus and discount amount
    Given the payment-provider-rule has a action discount-amount of 5 in currency "EUR"
    Then payment for my cart with payment provider "Bankwire" should be priced at "9500"

  Scenario: Creating a Payment Provider Rule with a fixed price plus and discount percent
    Given the payment-provider-rule has a action discount-percent of 10%
    Then payment for my cart with payment provider "Bankwire" should be priced at "9000"
