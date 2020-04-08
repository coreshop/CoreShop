@domain @product
Feature: Adding a new Product
  In order to extend my catalog
  I want to create a new product with different quantity restrictions

  Background:
    Given the site operates on a store in "Austria"
    Given the site has a currency "Euro" with iso "EUR"
    Given I am in country "Austria"
    Given the site has a product "Shoe" priced at 100

  Scenario: Create a product with a minimum quantity to order
    Given the product "Shoe" has a minimum order quantity of "100"
    And I add the product "Shoe" to my cart from add-to-cart-form
    Then there should be a violation message in my add-to-cart-form with message "You need to order at least 100 units of Shoe."
