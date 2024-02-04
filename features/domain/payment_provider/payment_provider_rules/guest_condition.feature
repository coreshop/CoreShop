@domain @payment_provider
Feature: Adding a new Payment Provider Rule
  In order to calculate payment provider
  I'll create a new payment-provider-rule
  with a guest condition

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And There is a payment provider "Bankwire" using factory "Bankwire"
    And the site has a product "Shoe" priced at 10000
    And adding a payment-provider-rule named "guest"
    And the payment-provider-rule is active
    And the payment-provider-rule has a condition guest
    And the site has a customer "some-customer@something.com"
    And the site has a guest "some-guest@something.com"

  Scenario: Add a new guest payment-provider-rule for a guest customer which is valid
    Given I am guest "some-guest@something.com"
    And I add the product "Shoe" to my cart
    Then the payment-provider-rule should be valid for my cart with payment provider "Bankwire"

  Scenario: Add a new guest payment-provider-rule for a cart without a customer which is valid
    Given I add the product "Shoe" to my cart
    Then the payment-provider-rule should be valid for my cart with payment provider "Bankwire"

  Scenario: Add a new guest payment-provider-rule for a customer which is invalid
    Given I am customer "some-customer@something.com"
    And I add the product "Shoe" to my cart
    Then the payment-provider-rule should be invalid for my cart with payment provider "Bankwire"
