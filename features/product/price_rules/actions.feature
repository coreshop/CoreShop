@product
Feature: Adding a new Product
  In order to increase my sales, I created catalog rules for products

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has two categories "Shoes" and "Coats"
    And the site has a product "Shoe" priced at 100
    And it is in category "Shoes"
    And the site has a product "Shoe 2" priced at 150
    And it is in category "Shoes"
    And the site has a product "Jacket" priced at 400
    And it is in category "Coats"
    Then the product "Shoe" should be priced at 100
    Then the product "Shoe 2" should be priced at 150
    Then the product "Jacket" should be priced at 400

  Scenario: Add a new discount rule with 20 percent discount for all products
    Given adding a product price rule named "discount"
    And the price rule is active
    And the price rule has a action discount-percent with 20% discount
    Then the price rule should be valid for product "Shoe"
    And the price rule should be valid for product "Shoe 2"
    And the price rule should be valid for product "Jacket"
    And the product "Shoe" should have the prices, price: "80" and retail-price: "100" and discount: "20"
    And the product "Shoe 2" should have the prices, price: "120" and retail-price: "150" and discount: "30"
    And the product "Jacket" should have the prices, price: "320" and retail-price: "400" and discount: "80"

  Scenario: Add a new catalog discount rule with 20 euro off for all products
    Given adding a product price rule named "discount"
    And the price rule is active
    And the price rule has a action discount with 20 in currency "EUR" off
    Then the price rule should be valid for product "Shoe"
    And the price rule should be valid for product "Shoe 2"
    And the price rule should be valid for product "Jacket"
    And the product "Shoe" should have the prices, price: "80" and retail-price: "100" and discount: "20"
    And the product "Shoe 2" should have the prices, price: "130" and retail-price: "150" and discount: "20"
    And the product "Jacket" should have the prices, price: "380" and retail-price: "400" and discount: "20"

  Scenario: Add a new catalog discount rule with 20 euro off for products in "Shoes" category
    Given adding a product price rule named "discount"
    And the price rule is active
    And the price rule has a action discount with 20 in currency "EUR" off
    And the price rule has a condition categories with category "Shoes"
    Then the price rule should be valid for product "Shoe"
    And the price rule should be valid for product "Shoe 2"
    And the price rule should be invalid for product "Jacket"
    And the product "Shoe" should have the prices, price: "80" and retail-price: "100" and discount: "20"
    And the product "Shoe 2" should have the prices, price: "130" and retail-price: "150" and discount: "20"
    And the product "Jacket" should have the prices, price: "400" and retail-price: "400" and discount: "0"


  Scenario: Add a discount-price rule with a price of 80 for all products
    Given adding a product price rule named "discount"
    And the price rule is active
    And the price rule has a action discount-price of 80 in currency "EUR"
    Then the price rule should be valid for product "Shoe"
    And the price rule should be valid for product "Shoe 2"
    And the price rule should be valid for product "Jacket"
    And the product "Shoe" should have the prices, price: "80" and discount-price: "80" and retail-price: "100" and discount: "0"
    And the product "Shoe 2" should have the prices, price: "80" and discount-price: "80" and retail-price: "150" and discount: "0"
    And the product "Jacket" should have the prices, price: "80" and discount-price: "80" and retail-price: "400" and discount: "0"

  Scenario: Add a price rule with a retail price of 80 for all products
    Given adding a product price rule named "discount"
    And the price rule is active
    And the price rule has a action price of 80 in currency "EUR"
    Then the price rule should be valid for product "Shoe"
    Then the price rule should be valid for product "Shoe 2"
    Then the price rule should be valid for product "Jacket"
    And the product "Shoe" should have the prices, price: "80" and retail-price: "80" and discount: "0"
    And the product "Shoe 2" should have the prices, price: "80" and retail-price: "80" and discount: "0"
    And the product "Jacket" should have the prices, price: "80" and retail-price: "80" and discount: "0"