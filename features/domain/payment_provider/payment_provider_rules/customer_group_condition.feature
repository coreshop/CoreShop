@domain @payment_provider
Feature: Adding a new Shipping Rule
  In order to calculate shipping
  I'll create a new payment-provider-rule
  with an customer group condition

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has a customer-group "Frequent Buyers"
    And the site has a customer "some-customer@something.com"
    And he is in customer-group "Frequent Buyers"
    And I am customer "some-customer@something.com"
    And the site has a product "Shoe" priced at 10000
    And I add the product "Shoe" to my cart
    And the cart belongs to customer "some-customer@something.com"
    And There is a payment provider "Bankwire" using factory "Bankwire"

  Scenario: Add a new customer-group payment-provider-rule which is valid
    Given adding a payment-provider-rule named "customer-groups"
    And the payment-provider-rule is active
    And the payment-provider-rule has a condition customer-groups with customer-group "Frequent Buyers"
    Then the payment-provider-rule should be valid for my cart with payment provider "Bankwire"

  Scenario: Add a new customer-group payment-provider-rule which is inactive
    Given adding a payment-provider-rule named "customer-groups"
    And the payment-provider-rule is inactive
    And the payment-provider-rule has a condition customer-groups with customer-group "Frequent Buyers"
    Then the payment-provider-rule should be invalid for my cart with payment provider "Bankwire"

  Scenario: Add a new amount payment-provider-rule which is invalid
    Given the site has a customer-group "New Customers"
    And the customer "some-customer@something.com" is in customer-group "New Customers"
    And adding a payment-provider-rule named "customer-groups"
    And the payment-provider-rule is active
    And the payment-provider-rule has a condition customer-groups with customer-group "Frequent Buyers"
    Then the payment-provider-rule should be invalid for my cart with payment provider "Bankwire"
