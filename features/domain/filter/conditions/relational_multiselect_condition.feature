@filter @domain
Feature: Adding a filter for an index
  In order to make my catalog searchable
  I want to create a filter with a select condition

  Background:
    Given the site operates on a store in "Austria"
    And the site has a manufacturer "CoreShop"
    And the site has a manufacturer "CoreShop 2"
    And the site has a index "myindex" for class "CoreShopProduct" with type "mysql"
    And  the index has following fields:
      | key          | name     | type    | getter | interpreter | columnType   |
      | manufacturer | producer | object  |        | object      | STRING       |
    And the site has a filter "myfilter" for index "myindex"
    And the filter has following conditions:
      | type                   | label        | field    |
      | relational_multiselect | Manufacturer | producer |

  Scenario: Create 3 products that will be filtered
    Given the site has a product "Shoe" priced at 100
    And the products sku is "SKU1"
    And the products has manufacturer "CoreShop"
    And the product is active
    And the product is published
    And the site has a product "Shoe 2" priced at 100
    And the products sku is "SKU2"
    And the products has manufacturer "CoreShop"
    And the product is active
    And the product is published
    And the site has a product "Shoe 3" priced at 100
    And the products sku is "SKU3"
    And the products has manufacturer "CoreShop"
    And the product is active
    And the product is published
    Then the filter should have 1 values with count 3 for relational_multiselect condition "producer"
    And the filter should have 3 items

  Scenario: Create 3 products that will be filtered for a specific manufacturer
    Given the site has a product "Shoe" priced at 100
    And the products sku is "SKU1"
    And the products has manufacturer "CoreShop"
    And the product is active
    And the product is published
    And the site has a product "Shoe 2" priced at 100
    And the products sku is "SKU2"
    And the products has manufacturer "CoreShop 2"
    And the product is active
    And the product is published
    And the site has a product "Shoe 3" priced at 100
    And the products sku is "SKU3"
    And the products has manufacturer "CoreShop 2"
    And the product is active
    And the product is published
    Then the filter should have 1 item for manufacturer "CoreShop" in field "producer[]"
    Then the filter should have 2 items for manufacturer "CoreShop 2" in field "producer[]"
