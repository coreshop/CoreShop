@country @country_context
Feature: The visitor is always linked to a country
  So, In Order to find the right country, there is the CountryContext

  Background:
    Given the site operates on a store in "Austria"

  Scenario: Test store default country
    Then I should be in country "Austria"

  Scenario: Manually overriding default country with not allowed country for store
    Given the site has a currency "Swiss Franc" with iso "CHF"
    And the site has a country "Switzerland" with currency "CHF"
    And I am in country "Switzerland"
    Then I still should be in country "Austria"

  Scenario: Manually overriding default country with allowed country for store
    Given the site has a currency "Swiss Franc" with iso "CHF"
    And the site has a country "Switzerland" with currency "CHF"
    And the country is valid for store "Austria"
    And I am in country "Switzerland"
    Then I should be in country "Switzerland"
