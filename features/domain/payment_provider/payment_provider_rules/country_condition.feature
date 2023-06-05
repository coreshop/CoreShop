@domain @payment_provider
Feature: Adding a new Payment Provider Rule
  In order to calculate shipping
  I'll create a new payment-provider-rule
  with an country condition

  Background:
    Given the site operates on a store in "Austria"
    And the site has a customer "some-customer@something.com"
    And the customer "some-customer@something.com" has an address with country "Austria", "4600", "Wels", "Freiung", "9-11/N3"
    And I am customer "some-customer@something.com"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has a product "Shoe" priced at 10000
    And I add the product "Shoe" to my cart
    And the cart belongs to customer "some-customer@something.com"
    And the cart ships to customer "some-customer@something.com" first address
    And the cart invoices to customer "some-customer@something.com" address with postcode "4600"
    And There is a payment provider "Bankwire" using factory "Bankwire"

  Scenario: Add a new country payment-provider-rule which is valid
    Given adding a payment-provider-rule named "country"
    And the payment-provider-rule is active
    And the payment-provider-rule has a condition countries with country "Austria"
    Then the payment-provider-rule should be valid for my cart with payment provider "Bankwire"

  Scenario: Add a new country payment-provider-rule which is inactive
    Given adding a payment-provider-rule named "country"
    And the payment-provider-rule is inactive
    And the payment-provider-rule has a condition countries with country "Austria"
    Then the payment-provider-rule should be invalid for my cart with payment provider "Bankwire"

  Scenario: Add a new country payment-provider-rule which is invalid
    Given adding a payment-provider-rule named "amount"
    And the payment-provider-rule is active
    And the site has a country "Germany" with currency "EUR"
    And the customer "some-customer@something.com" has an address with country "Austria", "4720", "Anytown", "Anystreet", "9"
    And the cart ships to customer "some-customer@something.com" address with postcode "4720"
    And the payment-provider-rule has a condition countries with country "Germany"
    Then the payment-provider-rule should be invalid for my cart with payment provider "Bankwire"

  Scenario: Add a new country payment-provider-rule which is valid for a different country
    Given adding a payment-provider-rule named "amount"
    And the payment-provider-rule is active
    And the site has a country "Germany" with currency "EUR"
    And the customer "some-customer@something.com" has an address with country "Germany", "47200", "Anytown", "Anystreet", "9"
    And the cart invoices to customer "some-customer@something.com" address with postcode "47200"
    And the payment-provider-rule has a condition countries with country "Germany"
    Then the payment-provider-rule should be valid for my cart with payment provider "Bankwire"
