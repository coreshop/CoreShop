@domain @product
Feature: Adding a new Product
  In order to increase my sales, I created catalog rules for products

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has two categories "Shoes" and "Coats"
    And the site has a product "Shoe" priced at 10000
    And it is in category "Shoes"
    And the site has a product "Shoe 2" priced at 15000
    And it is in category "Shoes"
    And the site has a product "Jacket" priced at 40000
    And it is in category "Coats"
    Then the product "Shoe" should be priced at "10000"
    Then the product "Shoe 2" should be priced at "15000"
    Then the product "Jacket" should be priced at "40000"

  Scenario: Add a new discount rule with 20 percent discount for all products
    Given adding a product price rule named "discount"
    And the price rule is active
    And the price rule has a action discount-percent with 20% discount
    Then the price rule should be valid for product "Shoe"
    And the price rule should be valid for product "Shoe 2"
    And the price rule should be valid for product "Jacket"
    And the product "Shoe" should have the prices, price: "8000" and retail-price: "10000" and discount: "2000"
    And the product "Shoe 2" should have the prices, price: "12000" and retail-price: "15000" and discount: "3000"
    And the product "Jacket" should have the prices, price: "32000" and retail-price: "40000" and discount: "8000"

  Scenario: Add a new catalog discount rule with 20 euro off for all products
    Given adding a product price rule named "discount"
    And the price rule is active
    And the price rule has a action discount with 20 in currency "EUR" off
    Then the price rule should be valid for product "Shoe"
    And the price rule should be valid for product "Shoe 2"
    And the price rule should be valid for product "Jacket"
    And the product "Shoe" should have the prices, price: "8000" and retail-price: "10000" and discount: "2000"
    And the product "Shoe 2" should have the prices, price: "13000" and retail-price: "15000" and discount: "2000"
    And the product "Jacket" should have the prices, price: "38000" and retail-price: "40000" and discount: "2000"

  Scenario: Add a new catalog discount rule with 20 euro off for products in "Shoes" category
    Given adding a product price rule named "discount"
    And the price rule is active
    And the price rule has a action discount with 20 in currency "EUR" off
    And the price rule has a condition categories with category "Shoes"
    Then the price rule should be valid for product "Shoe"
    And the price rule should be valid for product "Shoe 2"
    And the price rule should be invalid for product "Jacket"
    And the product "Shoe" should have the prices, price: "8000" and retail-price: "10000" and discount: "2000"
    And the product "Shoe 2" should have the prices, price: "13000" and retail-price: "15000" and discount: "2000"
    And the product "Jacket" should have the prices, price: "40000" and retail-price: "40000" and discount: "0"


  Scenario: Add a discount-price rule with a price of 80 for all products
    Given adding a product price rule named "discount"
    And the price rule is active
    And the price rule has a action discount-price of 80 in currency "EUR"
    Then the price rule should be valid for product "Shoe"
    And the price rule should be valid for product "Shoe 2"
    And the price rule should be valid for product "Jacket"
    And the product "Shoe" should have the prices, price: "8000" and discount-price: "8000" and retail-price: "10000" and discount: "0"
    And the product "Shoe 2" should have the prices, price: "8000" and discount-price: "8000" and retail-price: "15000" and discount: "0"
    And the product "Jacket" should have the prices, price: "8000" and discount-price: "8000" and retail-price: "40000" and discount: "0"

  Scenario: Add a price rule with a retail price of 80 for all products
    Given adding a product price rule named "discount"
    And the price rule is active
    And the price rule has a action price of 80 in currency "EUR"
    Then the price rule should be valid for product "Shoe"
    Then the price rule should be valid for product "Shoe 2"
    Then the price rule should be valid for product "Jacket"
    And the product "Shoe" should have the prices, price: "8000" and retail-price: "8000" and discount: "0"
    And the product "Shoe 2" should have the prices, price: "8000" and retail-price: "8000" and discount: "0"
    And the product "Jacket" should have the prices, price: "8000" and retail-price: "8000" and discount: "0"
