@domain @cart
Feature: Adding a new cart rule
  In order to give the customer discounts
  based on the cart, we add a new rule
  with a store condition

  Background:
    Given the site operates on a store in "Austria"
    And the site has a product "Shoe" priced at 100
    And the site has a currency "YEN" with iso "YEN"
    And the site has a country "China" with currency "YEN"
    And the site has a store "Asia" with country "China" and currency "Yen"

  Scenario: Add a new store cart price rule which is valid
    Given adding a cart price rule named "store"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has a condition stores with store "Austria"
    Then the cart rule should be valid for my cart

  Scenario: Add a new store cart price rule which is invalid
    Given adding a cart price rule named "store"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has a condition stores with store "Asia"
    Then the cart rule should be invalid for my cart

  Scenario: Add a new store cart price rule which is valid for another store
    Given my cart uses store "Asia"
    Given adding a cart price rule named "store"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has a condition stores with store "Asia"
    Then the cart rule should be valid for my cart
