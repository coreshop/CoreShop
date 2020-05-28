@domain @product
Feature: Adding a new Product
  In order to extend my catalog
  The catalog has a price-rule for a category
  I want to create a new product

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has two categories "Shoes" and "Coats"
    And the site has a category "Sneakers"
    And the category "Sneakers" is child of category "Shoes"
    And the site has a product "Shoe" priced at 100
    And it is in category "Shoes"
    And the site has a product "Shoe 2" priced at 150
    And it is in category "Shoes"
    And the site has a product "Jacket" priced at 400
    And it is in category "Coats"
    And the site has a product "Sneaker" priced at 3500
    And it is in category "Sneakers"

  Scenario: Add a new category product price rule which is valid
    Given adding a product price rule named "category-discount"
    And the price rule is active
    And the price rule has a condition categories with category "Shoes"
    Then the price rule should be valid for product "Shoe"

  Scenario: Add a new category product price rule which is invalid
    Given adding a product price rule named "category-discount"
    And the price rule is active
    And the price rule has a condition categories with category "Shoes"
    And the price rule should be invalid for product "Jacket"

  Scenario: Add a new category product price rule for a child category which is invalid
    Given adding a product price rule named "category-discount"
    And the price rule is active
    And the price rule has a condition categories with category "Shoes"
    And the price rule should be invalid for product "Sneaker"

  Scenario: Add a new category product price rule for a child category which is valid
    Given adding a product price rule named "category-discount"
    And the price rule is active
    And the price rule has a condition categories with category "Shoes" and it is recursive
    And the price rule should be valid for product "Sneaker"
