@domain @payment_provider
Feature: Adding a new Payment Rule
  In order to calculate shipping
  I'll create a new payment-provider-rule
  with an category condition

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has two categories "Shoes" and "Coats"
    And the site has a category "Sneakers"
    And the category "Sneakers" is child of category "Shoes"
    And the site has a product "Shoe" priced at 10000
    And it is in category "Shoes"
    And the site has a product "Jacket" priced at 400
    And it is in category "Coats"
    And the site has a product "Sneaker" priced at 3500
    And it is in category "Sneakers"
    And There is a payment provider "Bankwire" using factory "Bankwire"

  Scenario: Add a new category payment-provider-rule which is valid
    Given adding a payment-provider-rule named "category"
    And the payment-provider-rule is active
    And the payment-provider-rule has a condition categories with category "Shoes"
    And I add the product "Shoe" to my cart
    Then the payment-provider-rule should be valid for my cart with payment provider "Bankwire"

  Scenario: Add a new category payment-provider-rule which is inactive
    Given adding a payment-provider-rule named "category"
    And the payment-provider-rule is inactive
    And the payment-provider-rule has a condition categories with category "Shoes"
    And I add the product "Shoe" to my cart
    Then the payment-provider-rule should be invalid for my cart with payment provider "Bankwire"

  Scenario: Add a new category payment-provider-rule which is invalid
    Given adding a payment-provider-rule named "category"
    And the payment-provider-rule is active
    And the payment-provider-rule has a condition categories with category "Shoes"
    And I add the product "Jacket" to my cart
    Then the payment-provider-rule should be invalid for my cart with payment provider "Bankwire"

  Scenario: Add a new category payment-provider-rule with two products which is valid
    Given adding a payment-provider-rule named "category"
    And the payment-provider-rule is active
    And the payment-provider-rule has a condition categories with categories "Coats", "Shoes"
    And I add the product "Jacket" to my cart
    And I add the product "Shoe" to my cart
    Then the payment-provider-rule should be valid for my cart with payment provider "Bankwire"

  Scenario: Add a new category payment-provider-rule with two products which is valid
    Given adding a payment-provider-rule named "category"
    And the payment-provider-rule is active
    And the payment-provider-rule has a condition categories with category "Coats"
    And I add the product "Jacket" to my cart
    And I add the product "Shoe" to my cart
    Then the payment-provider-rule should be valid for my cart with payment provider "Bankwire"

  Scenario: Add a new category payment-provider-rule which includes all subcategory and is invalid
    Given adding a payment-provider-rule named "category"
    And the payment-provider-rule is active
    And the payment-provider-rule has a condition categories with category "Shoes"
    And I add the product "Sneaker" to my cart
    Then the payment-provider-rule should be invalid for my cart with payment provider "Bankwire"

  Scenario: Add a new category payment-provider-rule which includes all subcategory and is valid
    Given adding a payment-provider-rule named "category"
    And the payment-provider-rule is active
    And the payment-provider-rule has a condition categories with category "Shoes" and it is recursive
    And I add the product "Sneaker" to my cart
    Then the payment-provider-rule should be valid for my cart with payment provider "Bankwire"
