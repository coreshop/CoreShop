@domain @payment_provider
Feature: Adding a new Shipping Rule
  In order to calculate payment provider
  I'll create a new payment-provider-rule
  with an product condition

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has a product "Shoe" priced at 10000
    And the site has a product "Jacket" priced at 400
    And There is a payment provider "Bankwire" using factory "Bankwire"

  Scenario: Add a new product payment-provider-rule which is valid
    Given adding a payment-provider-rule named "product"
    And the payment-provider-rule is active
    And the payment-provider-rule has a condition products with product "Shoe"
    And I add the product "Shoe" to my cart
    Then the payment-provider-rule should be valid for my cart with payment provider "Bankwire"

  Scenario: Add a new product payment-provider-rule which is inactive
    Given adding a payment-provider-rule named "product"
    And the payment-provider-rule is inactive
    And the payment-provider-rule has a condition products with product "Shoe"
    And I add the product "Shoe" to my cart
    Then the payment-provider-rule should be invalid for my cart with payment provider "Bankwire"

  Scenario: Add a new product payment-provider-rule which is invalid
    Given adding a payment-provider-rule named "product"
    And the payment-provider-rule is active
    And the payment-provider-rule has a condition products with product "Shoe"
    And I add the product "Jacket" to my cart
    Then the payment-provider-rule should be invalid for my cart with payment provider "Bankwire"

  Scenario: Add a new product payment-provider-rule with two products which is valid
    Given adding a payment-provider-rule named "product"
    And the payment-provider-rule is active
    And the payment-provider-rule has a condition products with products "Shoe", "Jacket"
    And I add the product "Jacket" to my cart
    And I add the product "Shoe" to my cart
    Then the payment-provider-rule should be valid for my cart with payment provider "Bankwire"

  Scenario: Add a new product payment-provider-rule with two products which is valid
    Given adding a payment-provider-rule named "product"
    And the payment-provider-rule is active
    And the payment-provider-rule has a condition products with product "Jacket"
    And I add the product "Jacket" to my cart
    And I add the product "Shoe" to my cart
    Then the payment-provider-rule should be valid for my cart with payment provider "Bankwire"
