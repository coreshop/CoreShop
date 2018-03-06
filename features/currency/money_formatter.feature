@currency @money_formatter
Feature: Formatting money in different currencies
  In order to displays currencies right, we need to format them properly

  Background:
    Given the site operates on a store in "Austria"

  Scenario: Format currency in EUR for language de and en
    Then the amount "100" of currency "EUR" in language "de" should be formatted "1,00 €"
    And the amount "100" of currency "EUR" in language "en" should be formatted "€1.00"

  Scenario: Format currency in USD for language de and en
    Given the site has a currency "USD" with iso "USD"
    And the site has a country "USA" with currency "USD"
    Then the amount "100" of currency "USD" in language "de" should be formatted "1,00 $"
    And the amount "100" of currency "USD" in language "en" should be formatted "$1.00"