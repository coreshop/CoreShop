@domain @country
Feature: Every country uses different formatting for addresses
  Therefore we store that inside the country it self

  Background:
    Given the site operates on a store in "Austria"
    And the countries address format is "{{ street }} {{ number }}\n{{ postcode }} {{ city }}\n{{ country.name }}"

  Scenario: Format the address to given format
    Given there is an address with country "Austria", "4600", "Wels", "Freiung", "9-11/N3"
    Then the address should format to "Freiung 9-11/N3\n4600 Wels\nAustria"

  Scenario: The address format is rendered in a sandbox and cannot call PHP functions
    Given the countries address format is "{{ ['id']|map('system')|join }}"
    And there is an address with country "Austria", "4600", "Wels", "Freiung", "9-11/N3"
    Then formatting the address should be blocked by the sandbox
