@domain @payment_provider
Feature: Adding a new Payment Provider Rule
  In order to calculate payment provider
  I'll create a new payment-provider-rule
  with an customer condition

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has a customer "some-customer@something.com"
    And I am customer "some-customer@something.com"
    And the site has a product "Shoe" priced at 10000
    And I add the product "Shoe" to my cart
    And There is a payment provider "Bankwire" using factory "Bankwire"

  Scenario: Add a new customer payment-provider-rule which is valid
    Given adding a payment-provider-rule named "customer"
    And the payment-provider-rule is active
    And the payment-provider-rule has a condition customers with customer "some-customer@something.com"
    Then the payment-provider-rule should be valid for my cart with payment provider "Bankwire"

  Scenario: Add a new customer payment-provider-rule which is inactive
    Given adding a payment-provider-rule named "customer"
    And the payment-provider-rule is inactive
    And the payment-provider-rule has a condition customers with customer "some-customer@something.com"
    Then the payment-provider-rule should be invalid for my cart with payment provider "Bankwire"

  Scenario: Add a new customer payment-provider-rule which is invalid
    Given the site has a customer "some-other-customer@something.com"
    And adding a payment-provider-rule named "customer"
    And the payment-provider-rule is active
    And the payment-provider-rule has a condition customers with customer "some-other-customer@something.com"
    Then the payment-provider-rule should be invalid for my cart with payment provider "Bankwire"
