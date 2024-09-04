@domain @cart
Feature: Adding a new Product Specific Price Rule
  to a Product with a standard unit
  which the rules applies to the standard unit

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has a product-unit "Pieces"
    And the site has a product-unit "Carton"
    And the site has a product-unit "Palette"
    And the site has a product "Shoe" priced at 10000
    And the product has the default unit "Pieces"
    And the product has an additional unit "Carton" with conversion rate "24" and price 200000

  Scenario: Add a discount-price rule with a price of 80 of unit "Pieces"
    Given adding a product specific price rule to product "Shoe" named "discount"
    And the specific price rule is active
    And the specific price rule has a action discount-price of 80 in currency "EUR"
    And the product "Shoe" should have the prices, price: "8000" and discount-price: "8000" and retail-price: "10000" and discount: "0"
    And I add the product "Shoe" with unit "Pieces" to my cart
    Then the cart total should be "8000" including tax
    And the cart total should be "8000" excluding tax

  Scenario: Add a price rule with a price of 80 of unit "Pieces"
    Given adding a product specific price rule to product "Shoe" named "discount"
    And the specific price rule is active
    And the specific price rule has a action price of 80 in currency "EUR"
    And the product "Shoe" should have the prices, price: "8000" and discount-price: "0" and retail-price: "8000" and discount: "0"
    And I add the product "Shoe" with unit "Pieces" to my cart
    Then the cart total should be "8000" including tax
    And the cart total should be "8000" excluding tax

  Scenario: Add a discount-price rule with a price of 80 of unit "Pieces" and getting unit "Carton" to the cart
    Given adding a product specific price rule to product "Shoe" named "discount"
    And the specific price rule is active
    And the specific price rule has a action discount-price of 80 in currency "EUR"
    And the product "Shoe" should have the prices, price: "8000" and discount-price: "8000" and retail-price: "10000" and discount: "0"
    And I add the product "Shoe" with unit "Carton" to my cart
    Then the cart total should be "200000" including tax
    And the cart total should be "200000" excluding tax

  Scenario: Add a price rule with a price of 80 of unit "Pieces" and getting unit "Carton" to the cart
    Given adding a product specific price rule to product "Shoe" named "discount"
    And the specific price rule is active
    And the specific price rule has a action price of 80 in currency "EUR"
    And the product "Shoe" should have the prices, price: "8000" and discount-price: "0" and retail-price: "8000" and discount: "0"
    And I add the product "Shoe" with unit "Carton" to my cart
    Then the cart total should be "200000" including tax
    And the cart total should be "200000" excluding tax
