@currency
Feature: Adding a new Currency
  In Order to increase my sales
  I want to create a new currency

  Background:
    Given the site operates on a store in "Austria"

  Scenario: Create a new currency
    Given the site has a currency "USD" with iso "USD"
    And the site has a country "USA" with currency "USD"
    And the country "USA" is active
    And the country "USA" is valid for store "Austria"
    Then the store "Austria" should have "2" currencies
