@ui @customer_profile
Feature: Edit address

  Background:
    Given the site operates on a store in "Austria"
    And the store "Austria" is the default store
    And the site has a customer "elon@musk.com" with password "cybertruck" and name "Elon" "Musk"
    And I am logged in as "elon@musk.com"
    And the customer "elon@musk.com" has an address with country "Austria", "4600", "Wels", "Freiung", "9-11/N3"

  Scenario: Edit my address
    When I open my addresses
    And I choose "Freiung" to edit
    And I change my street to "Blumensteig" and number "19/3"
    And I save my address
    Then the customer "elon@musk.com" address is country "Austria", "4600", "Wels", "Blumensteig", "19/3"

  Scenario: Add a address
    When I want to add a address information
    And my new Address is "Elon", "Twitter X", country "Austria", "1234", "Wien", "Herbststrasse", "16", "+45 5336445"
    And I save my address
    Then the customer "elon@musk.com" address is country "Austria", "1234", "Wien", "Herbststrasse", "16"

  Scenario: Delete a address
    When I want to add a address information
    And my new Address is "Elon", "Twitter X", country "Austria", "1160", "Wien", "Brunnengasse", "46", "+45 5336445"
    And I save my address
    Then I open my addresses
    And I want to delete my address with street "Brunnengasse"


