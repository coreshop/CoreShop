@theme
Feature: In order to allow multishop with different designs
  we allow each store a different template

  Background:
    Given the site operates on a store in "Austria"

  Scenario: Default Store theme
    Then the current theme name should be "standard"

  Scenario: Store Theme
    Given the site has a country "Germany" with currency "EUR"
    And the country "Germany" is active
    And the site has a store "Germany" with country "Germany" and currency "EUR"
    And the store "Germany" uses theme "germany"
    And I am in store "Germany"
    Then the current theme name should be "germany"

  Scenario: Multiple Stores with one active
    Given the site has a country "Germany" with currency "EUR"
    And the country "Germany" is active
    And the site has a store "Germany" with country "Germany" and currency "EUR"
    And the store "Germany" uses theme "germany"
    Then the current theme name should be "standard"

