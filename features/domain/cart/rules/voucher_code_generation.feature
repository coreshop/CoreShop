@domain @cart
Feature: Adding a new cart rule with generated voucher codes

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"

  Scenario: Add a new voucher rule with generated codes
    Given adding a cart price rule named "codes"
    When I want to generate 100 codes with a length of 1 in alphanumeric characters for the cart rule
    Then the generation of the codes failed


  Scenario: Add a new voucher rule with generated codes
    Given adding a cart price rule named "codes"
    When I want to generate 5 codes with a length of 1 in alphanumeric characters for the cart rule
    Then the generation of the codes succeeded

