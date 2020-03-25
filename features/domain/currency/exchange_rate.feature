@currency @domain
Feature: Adding a new Currency
  In Order to increase my sales
  I want to add a new currency with an exchange-rate

  Background:
    Given the site operates on a store in "Austria"

  Scenario: Create a new currency for country US
    Given the site has a currency "USD" with iso "USD"
    And the site has a country "USA" with currency "USD"
    And the currency has a exchange-rate to currency "EUR" of "1.1"
    Then price "100" of currency "USD" should exchange to price "110" in currency "EUR"
    Then price "100" of currency "EUR" should exchange to price "91" in currency "USD"
