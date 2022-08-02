@domain @filter
Feature: Adding a filter for an index
  In order to make my catalog searchable
  I want to create a filter with a category condition

  Background:
    Given the site operates on a store in "Austria"
    And the site has a index "myindex" for class "CoreShopProduct" with type "mysql"
    And  the index has following fields:
      | key   | name            | type            | getter          | interpreter   | columnType   |
      | sku   | sku             | object          |                 |               | STRING       |
      | ean   | ean             | object          |                 |               | STRING       |
      | name  | internalName    | localizedfields | localizedfield  | localeMapping | STRING       |

  Scenario: Create 3 products that will be filtered
    Given the site has a category "Shoes"
    And the site has a filter "myfilter" for index "myindex"
    And the filter has a category condition with category "Shoes"
    And the site has a product "Shoe" priced at 100
    And it is in category "Shoes"
    And the products sku is "SKU1"
    And the product is active
    And the product is published
    And the site has a product "Shoe 2" priced at 100
    And it is in category "Shoes"
    And the products sku is "SKU2"
    And the product is active
    And the product is published
    And the site has a product "Shoe 3" priced at 100
    And it is in category "Shoes"
    And the products sku is "SKU3"
    And the product is active
    And the product is published
    Then the filter should have 3 items

  Scenario: Create 3 products with sub categories that will be filtered
    Given the site has a category "Shoes"
    And the site has a category "Male"
    And the category is child of category "Shoes"
    And the site has a category "Female"
    And the category is child of category "Shoes"
    And the site has a filter "myfilter" for index "myindex"
    And the filter has a category condition with category "Shoes" and it includes all subcategories
    And the site has a product "Shoe" priced at 100
    And it is in category "Male"
    And the products sku is "SKU1"
    And the product is active
    And the product is published
    And the site has a product "Shoe 2" priced at 100
    And it is in category "Female"
    And the products sku is "SKU2"
    And the product is active
    And the product is published
    And the site has a product "Shoe 3" priced at 100
    And it is in category "Shoes"
    And the products sku is "SKU3"
    And the product is active
    And the product is published
    Then the filter should have 3 items

  Scenario: Create 3 products with sub categories that will be filtered without subcategories
    Given the site has a category "Shoes"
    And the site has a category "Male"
    And the category is child of category "Shoes"
    And the site has a category "Female"
    And the category is child of category "Shoes"
    And the site has a filter "myfilter" for index "myindex"
    And the filter has a category condition with category "Shoes"
    And the site has a product "Shoe" priced at 100
    And it is in category "Male"
    And the products sku is "SKU1"
    And the product is active
    And the product is published
    And the site has a product "Shoe 2" priced at 100
    And it is in category "Female"
    And the products sku is "SKU2"
    And the product is active
    And the product is published
    And the site has a product "Shoe 3" priced at 100
    And it is in category "Shoes"
    And the products sku is "SKU3"
    And the product is active
    And the product is published
    Then the filter should have 1 item
