@shipping @shipping_rules @shipping_rule_condition_currency
Feature: Adding a new Shipping Rule
  In order to calculate shipping
  I'll create a new shipping rule
  with an currency condition

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has a tax rate "AT" with "20%" rate
    And the site has a tax rule group "AT"
    And the tax rule group has a tax rule for country "Austria" with tax rate "AT"
    And the site has a product "Shoe" priced at 10000
    And the product has the tax rule group "AT"
    And I add the product "Shoe" to my cart
    And the site has a carrier "Post"

  Scenario: Add a new currency shipping rule which is valid
    Given adding a shipping rule named "currencies"
    And the shipping rule is active
    And the shipping rule has a condition currencies with currency "EUR"
    Then the shipping rule should be valid for my cart with carrier "Post"

  Scenario: Add a new currency shipping rule which is inactive
    Given adding a shipping rule named "currencies"
    And the shipping rule is inactive
    And the shipping rule has a condition currencies with currency "EUR"
    Then the shipping rule should be invalid for my cart with carrier "Post"

  Scenario: Add a new currency shipping rule which is invalid
    Given the site has a currency "YEN" with iso "YEN"
    And the site has a country "China" with currency "YEN"
    And adding a shipping rule named "currencies"
    And the shipping rule is active
    And the shipping rule has a condition currencies with currency "YEN"
    And I am using currency "YEN"
    Then the shipping rule should be invalid for my cart with carrier "Post"
