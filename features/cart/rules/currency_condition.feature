@cart @cart_rules @cart_rule_condition_currency
Feature: Adding a new cart rule
  In order to give the customer discounts
  based on the cart, we add a new rule
  with a currency condition

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And the site has a currency "USD" with iso "USD"
    And the site has a country "USA" with currency "USD"
    And the currency is valid for store "Austria"
    And I am in country "Austria"

  Scenario: Add a new currency cart rule which is valid
    Given adding a cart price rule named "currency"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has a condition currencies with currency "EUR"
    Then the cart rule should be valid for my cart

  Scenario: Add a new currency cart rule which is invalid
    Given my cart uses currency "USD"
    And adding a cart price rule named "currency"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has a condition currencies with currency "EUR"
    Then the cart rule should be invalid for my cart