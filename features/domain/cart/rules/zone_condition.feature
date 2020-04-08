@domain @cart
Feature: Adding a new cart rule
  In order to give the customer discounts
  based on the cart, we add a new rule
  with a zone condition

  Background:
    Given the site operates on a store in "Austria"
    And the site has a zone "Europe"
    And the site has a zone "America"
    And the country "Austria" is in zone "Europe"
    And I am in country "Austria"
    And the site has a country "Germany" with currency "EUR"
    And the country "Germany" is in zone "Europe"
    And the site has a currency "USD" with iso "USD"
    And the site has a country "USA" with currency "USD"
    And the country "USA" is in zone "America"
    And the site has a customer "some-customer@something.com"
    And the customer "some-customer@something.com" has an address with country "Austria", "4600", "Wels", "Freiung", "9-11/N3"
    And the cart belongs to customer "some-customer@something.com"
    And the cart invoices to customer "some-customer@something.com" address with postcode "4600"
    And the site has a zone "Asia"

  Scenario: Add a new zone cart price rule which is valid
    Given adding a cart price rule named "zone"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has a condition zones with zone "Europe"
    Then the cart rule should be valid for my cart

  Scenario: Add a new zone cart price rule which is valid for another zone
    Given the customer "some-customer@something.com" has an address with country "USA", "95014", "Cupertino", "One Infinite Loop", "1"
    And the cart invoices to customer "some-customer@something.com" address with postcode "95014"
    And adding a cart price rule named "zone"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has a condition zones with zone "America"
    Then the cart rule should be valid for my cart

  Scenario: Add a new zone cart price rule which is invalid
    Given adding a cart price rule named "zone"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has a condition zones with zone "Asia"
    Then the cart rule should be invalid for my cart
