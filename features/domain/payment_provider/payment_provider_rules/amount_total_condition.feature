@domain @payment_provider
Feature: Adding a new Payment Provider Rule
  In order to calculate payment provider
  I'll create a new payment-provider-rule
  with an amount condition based on the total value of the cart

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And adding a cart price rule named "100% discount"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has a action discount-percent with 100% discount
    And the site has a product "Shoe" priced at 10000
    And I add the product "Shoe" to my cart
    And There is a payment provider "Bankwire" using factory "Bankwire"

  Scenario: Add a new amount payment-provider-rule which is valid
    Given adding a payment-provider-rule named "amount"
    And the payment-provider-rule is active
    And the payment-provider-rule has a condition amount from total "0" to "1"
    Then the payment-provider-rule should be valid for my cart with payment provider "Bankwire"
