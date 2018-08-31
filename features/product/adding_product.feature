@product
Feature: Adding a new Product
  In order to extend my catalog
  I want to create a new product

  Background:
    Given the site operates on a store in "Austria"
    Given the site has a product "Shoe" priced at 100
    Then the product "Shoe" should be priced at "100"

  Scenario: Create a new product
    Given the site has a product "Shoe 2" priced at 200
    Then the product "Shoe 2" should be priced at "200"
