@product @product_unit
Feature: Adding a new Product
  In order to extend my catalog
  I want to create a new product with different units

  Scenario: Create a new unit
    Given the site has a product-unit "Pieces"
    And the site has a product-unit "Carton"
    And the site has a product-unit "Palette"
    Then there should be a unit "Pieces"

  Scenario: Create a product with a default unit
    Given the site has a product-unit "Pieces"
    And the site has a product "Shoe 2" priced at 200
    And the product has the default unit "Pieces"
    Then the products default unit should be unit "Pieces"

  Scenario: Create a product with a additional units
    Given the site has a product-unit "Pieces"
    And the site has a product-unit "Carton"
    And the site has a product-unit "Palette"
    And the site has a product "Shoe 2" priced at 200
    And the product has the default unit "Pieces"
    And the product has and additional unit "Carton" with conversion rate "24"
    And the product has and additional unit "Palette" with conversion rate "200"
    Then the product should have and additional unit "Carton" with conversion rate "24"
    Then the product should have and additional unit "Palette" with conversion rate "200"
