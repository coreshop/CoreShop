@country @country_address_format
Feature: Every country uses different formatting for addresses
  Therefore we store that inside the country it self

  Background:
    Given the site operates on a store in "Austria"
    And the countries address format is "%Text(street); %Text(number);\n%Text(postcode); %Text(city);\n%DataObject(country,{'method' : 'getName'});"

  Scenario: Format the address to given format
    Given there is an address with country "Austria", "4600", "Wels", "Freiung", "9-11/N3"
    Then the address should format to "Freiung 9-11/N3\n4600 Wels\nAustria"