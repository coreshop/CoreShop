@ui @category
Feature: Viewing a product details

  Background:
    Given the site operates on a store in "Austria"
    And the store "Austria" is the default store
    And the site has two categories "Shoes" and "Coats"
    And the site has a category "Sneakers"
    And the site has a product "Shoe" priced at 12000
    And the product is active and published and available for store "Austria"
    And it is in category "Shoes"
    And the site has a product "Shoe 2" priced at 15000
    And the product is active and published and available for store "Austria"
    And it is in category "Shoes"
    And the site has a product "Jacket" priced at 40000
    And the product is active and published and available for store "Austria"
    And it is in category "Coats"
    And the site has a product "Sneaker" priced at 350000
    And the product is active and published and available for store "Austria"
    And it is in category "Sneakers"

  Scenario: Viewing latest products
    When I check latest products
    Then I should see 4 products in the list

  Scenario: View Categories
