@domain @category
Feature: Create a new category
  In Order to structure my products
  I want to create some categories

  Background:
    Given the site operates on a store in "Austria"

  Scenario: Create a new category
    And the site has a category "Shirts"
    Then there should be a category "Shirts"

  Scenario: Create a new category and add a product
    And the site has a category "Shirts"
    And the site has a product "CoreShop Shirt" priced at 2000
    And it is in category "Shirts"
    Then the product "CoreShop Shirt" should be in category "Shirts"

  Scenario: Create a new category with a sub-category
    And the site has a category "Shirts"
    And the site has a category "Men"
    And the category is child of category "Shirts"
    Then the category "Men" should be child of category "Shirts"
