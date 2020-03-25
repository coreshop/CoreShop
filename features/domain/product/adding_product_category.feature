@product @domain
Feature: Adding a new Product and a category
  In order to extend my catalog
  I want to create a new product and a category

  Scenario: Create a new product with a specific price rule for category
    Given the site has a product "Shoe 2" priced at 200
    Given the site has a category "Shoes"
    Given the product "Shoe 2" is in category "Shoes"
    Then the product "Shoe 2" should be in category "Shoes"
