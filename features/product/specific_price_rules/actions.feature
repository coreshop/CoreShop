@product @product_specific_price_rules @product_specific_price_rules_actions
Feature: Adding a new Product
  In order to extend my catalog
  In order to increase my sales, I created some rules with discounts

  Background:
    Given the site operates on a store in "Austria"
    Given the site has a currency "Euro" with iso "EUR"
    Given I am in country "Austria"
    Given the site has a product "Shoe" priced at 10000
    Then the product "Shoe" should be priced at "10000"

  Scenario: Add a new discount rule with 20 percent discount
    Given adding a product specific price rule to product "Shoe" named "discount"
    And the specific price rule is active
    And the specific price rule has a action discount-percent with 20% discount
    Then the specific price rule should be valid for product "Shoe"
    And the product "Shoe" should be priced at "8000"
    And the product "Shoe" discount should be "2000"
    And the product "Shoe" retail-price should be "10000"

  Scenario: Add a new discount rule with 20 euro off
    Given adding a product specific price rule to product "Shoe" named "discount"
    And the specific price rule is active
    And the specific price rule has a action discount with 20 in currency "EUR" off
    Then the specific price rule should be valid for product "Shoe"
    And the product "Shoe" should be priced at "8000"
    And the product "Shoe" discount should be "2000"
    And the product "Shoe" retail-price should be "10000"

  Scenario: Add a discount-price rule with a price of 80
    Given adding a product specific price rule to product "Shoe" named "discount"
    And the specific price rule is active
    And the specific price rule has a action discount-price of 80 in currency "EUR"
    Then the specific price rule should be valid for product "Shoe"
    And the product "Shoe" should have the prices, price: "8000" and discount-price: "8000" and retail-price: "10000" and discount: "0"

  Scenario: Add a price rule with a retail price of 80
    Given adding a product specific price rule to product "Shoe" named "discount"
    And the specific price rule is active
    And the specific price rule has a action price of 80 in currency "EUR"
    Then the specific price rule should be valid for product "Shoe"
    And the product "Shoe" should be priced at "8000"
    And the product "Shoe" discount should be "0"
    And the product "Shoe" retail-price should be "8000"