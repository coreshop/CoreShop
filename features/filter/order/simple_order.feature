@filter @filter_order
Feature: Adding a object index
  In order to make my catalog searchable
  I want to create a new index and order it by a specific attribute

  Background:
    Given the site operates on a store in "Austria"
    And the site has a index "myindex" for class "CoreShopProduct" with type "mysql"
    And  the index has following fields:
      | key   | name            | type            | getter          | interpreter   | columnType   |
      | sku   | sku             | object          |                 |               | STRING       |
      | ean   | ean             | object          |                 |               | STRING       |
      | name  | internalName    | localizedfields | localizedfield  | localeMapping | STRING       |
    And the site has a filter "myfilter" for index "myindex"

  Scenario: Adding multiple products to the index and sort them by name ASC
    And the site has a product "A Shoe" priced at 100
    And the products ean is "123456"
    And the products sku is "654321"
    And the product is active
    And the product is published
    And the site has a product "B Shoe" priced at 100
    And the products ean is "987654"
    And the products sku is "456789"
    And the product is active
    And the product is published
    Then if I query the filter with a simple order for field "name" and direction "ASC" I should get two products "A Shoe" and "B Shoe"

  Scenario: Adding multiple products to the index and sort them by name DESC
    And the site has a product "B Shoe" priced at 100
    And the products ean is "123456"
    And the products sku is "654321"
    And the product is active
    And the product is published
    And the site has a product "A Shoe" priced at 100
    And the products ean is "987654"
    And the products sku is "456789"
    And the product is active
    And the product is published
    Then if I query the filter with a simple order for field "name" and direction "DESC" I should get two products "B Shoe" and "A Shoe"
