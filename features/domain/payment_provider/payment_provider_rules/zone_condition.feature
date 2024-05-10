@domain @payment_provider
Feature: Adding a new Payment Provider Rule
  In order to calculate payment provider
  I'll create a new payment-provider-rule
  with an zone condition

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And the site has a zone "Europe"
    And the country "Austria" is in zone "Europe"
    And the site has a customer "some-customer@something.com"
    And the customer "some-customer@something.com" has an address with country "Austria", "4600", "Wels", "Freiung", "9-11/N3"
    And the site has a product "Shoe" priced at 10000
    And I add the product "Shoe" to my cart
    And the cart ships to customer "some-customer@something.com" first address
    And the cart invoices to customer "some-customer@something.com" address with postcode "4600"
    And There is a payment provider "Bankwire" using factory "Bankwire"

  Scenario: Add a new zone payment-provider-rule which is valid
    Given adding a payment-provider-rule named "zones"
    And the payment-provider-rule is active
    And the payment-provider-rule has a condition zones with zone "Europe"
    Then the payment-provider-rule should be valid for my cart with payment provider "Bankwire"

  Scenario: Add a new zone payment-provider-rule which is inactive
    Given adding a payment-provider-rule named "zones"
    And the payment-provider-rule is inactive
    And the payment-provider-rule has a condition zones with zone "Europe"
    Then the payment-provider-rule should be invalid for my cart with payment provider "Bankwire"

  Scenario: Add a new zone payment-provider-rule which is invalid
    Given the site has a zone "America"
    And adding a payment-provider-rule named "zones"
    And the payment-provider-rule is active
    And the payment-provider-rule has a condition zones with zone "America"
    Then the payment-provider-rule should be invalid for my cart with payment provider "Bankwire"
