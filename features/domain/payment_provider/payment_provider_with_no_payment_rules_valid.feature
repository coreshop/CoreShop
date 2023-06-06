@domain @payment_provider
Feature: Add a new payment provider without rules should be valid

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has a product "Shoe" priced at 10000
    And I add the product "Shoe" to my cart
    And There is a payment provider "Bankwire" using factory "Bankwire"

  Scenario: Payment Provider with not payment rule should be valid
    Given I add the product "Shoe" to my cart
    Then there should be a payment provider "Bankwire"

  Scenario: Payment Provider with one payment rule should be valid
    Given adding a payment-provider-rule named "creditdards"
    And the payment-provider-rule "creditdards" is active
    And the payment-provider-rule has a condition products with product "Shoe"
    And the payment-provider-rule belongs to payment provider "Bankwire"
    And I add the product "Shoe" to my cart
    Then the payment provider "Bankwire" should be valid for my cart

