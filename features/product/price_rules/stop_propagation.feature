@product @product_price_rules @product_price_rules_stop_propagation
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
    And adding a product price rule named "discount 10"
    And the price rule is active
    And the price rule has priority "10"
    And the price rule has a action discount-percent with 10% discount
    And adding a product price rule named "discount 20"
    And the price rule is active
    And the price rule has priority "20"
    And the price rule has a action discount-percent with 20% discount

  Scenario: Let all product price rules apply
    Then the product "Shoe" should have the prices, price: "7000" and retail-price: "10000" and discount: "3000"
    And the product "Shoe 2" should have the prices, price: "10500" and retail-price: "15000" and discount: "4500"
    And the product "Jacket" should have the prices, price: "28000" and retail-price: "40000" and discount: "12000"

  Scenario: Let only the 10% rule apply
    Given the price rule "discount 10" is stop propagation
    And the product "Shoe" should have the prices, price: "9000" and retail-price: "10000" and discount: "1000"
    And the product "Shoe 2" should have the prices, price: "13500" and retail-price: "15000" and discount: "1500"
    And the product "Jacket" should have the prices, price: "36000" and retail-price: "40000" and discount: "4000"

  Scenario: Let only the 20% rule apply
    Given the price rule "discount 20" has priority "5"
    And the price rule "discount 20" is stop propagation
    And the product "Shoe" should have the prices, price: "8000" and retail-price: "10000" and discount: "2000"
    And the product "Shoe 2" should have the prices, price: "12000" and retail-price: "15000" and discount: "3000"
    And the product "Jacket" should have the prices, price: "32000" and retail-price: "40000" and discount: "8000"
