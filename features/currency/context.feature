@currency @currency_context
Feature: Adding a new Currency
  In Order to increase my sales
  I want to create a new currency
  The customer then changes the currency

  Background:
    Given the site operates on a store in "Austria"
    Given the site has a currency "USD" with iso "USD"
    And the site has a country "USA" with currency "USD"
    And the country "USA" is active
    And the country "USA" is valid for store "Austria"

  Scenario: Site should be using currency "EUR"
    Then the site should be using currency "EUR"

  Scenario: Site should be using currency "USD"
    And I am using currency "USD"
    Then the site should be using currency "USD"

  Scenario: Customer comes from Country "USA" and should use currency "USD"
    And I am in country "USA"
    Then the site should be using currency "USD"

  Scenario: Customer comes from Country "USA" and should use currency "EUR"
    Given the country "USA" is invalid for store "Austria"
    And I am in country "USA"
    Then the site should be using currency "EUR"