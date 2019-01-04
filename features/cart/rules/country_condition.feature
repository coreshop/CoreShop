@cart @cart_rules @cart_rule_condition_country
Feature: Adding a new cart rule
  In order to give the customer discounts
  based on the cart, we add a new rule
  with a country condition

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has a country "Germany" with currency "EUR"
    And the country "Germany" is valid for store "Austria"
    And the site has a customer "some-customer@something.com"
    And the customer "some-customer@something.com" has an address with country "Austria", "4600", "Wels", "Freiung", "9-11/N3"
    And the cart belongs to customer "some-customer@something.com"
    And the cart invoices to customer "some-customer@something.com" address with postcode "4600"

  Scenario: Add a new country cart price rule which is valid
    Given adding a cart price rule named "country"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has a condition countries with country "Austria"
    Then the cart rule should be valid for my cart

  Scenario: Add a new country cart price rule which is valid for another Country
    Given the customer "some-customer@something.com" has an address with country "Germany", "04600", "Wels", "Freiung", "9-11/N3"
    And the cart invoices to customer "some-customer@something.com" address with postcode "04600"
    And adding a cart price rule named "country"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has a condition countries with country "Germany"
    Then the cart rule should be valid for my cart

  Scenario: Add a new country cart price rule which is invalid
    Given adding a cart price rule named "country"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has a condition countries with country "Germany"
    Then the cart rule should be invalid for my cart