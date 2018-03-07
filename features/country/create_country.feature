@country
Feature: Create a new country
  In Order to allow customers from different countries
  I need to create countries

  Scenario: Create a new country
    Given the site has a currency "Euro" with iso "Euro"
    And the site has a country "Austria" with currency "EUR"
    Then there should be a country "Austria"
    And the country "Austria" should use currency "EUR"

  Scenario: Create two new countries
    Given the site has a currency "Euro" with iso "Euro"
    And the site has a country "Austria" with currency "EUR"
    And the site has a country "Germany" with currency "EUR"
    Then there should be a country "Austria"
    And the country "Austria" should use currency "EUR"
    Then there should be a country "Germany"
    And the country "Germany" should use currency "EUR"

  Scenario: Create two new countries with different currencies
    Given the site has a currency "Euro" with iso "Euro"
    Given the site has a currency "USD" with iso "USD"
    And the site has a country "Austria" with currency "EUR"
    And the site has a country "USA" with currency "USD"
    Then there should be a country "Austria"
    And the country "Austria" should use currency "EUR"
    Then there should be a country "USA"
    And the country "USA" should use currency "USD"
