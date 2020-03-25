@filter @domain
Feature: Adding a filter for an index
  In order to make my catalog searchable
  I want to create a filter with a multiselect condition

  Background:
    Given the site operates on a store in "Austria"
    And the site has a index "myindex" for class "CoreShopProduct" with type "mysql"
    And  the index has following fields:
      | key   | name            | type            | getter          | interpreter   | columnType   |
      | sku   | sku             | object          |                 |               | STRING       |
      | ean   | ean             | object          |                 |               | STRING       |
      | name  | internalName    | localizedfields | localizedfield  | localeMapping | STRING       |
    And the site has a filter "myfilter" for index "myindex"
    And the filter has following conditions:
      | type        | label           | field        |
      | multiselect | SKU Mulitselect | sku          |

  Scenario: Create 3 products that will be filtered
    Given the site has a product "Shoe" priced at 100
    And the products sku is "SKU1"
    And the product is active
    And the product is published
    And the site has a product "Shoe 2" priced at 100
    And the products sku is "SKU2"
    And the product is active
    And the product is published
    And the site has a product "Shoe 3" priced at 100
    And the products sku is "SKU3"
    And the product is active
    And the product is published
    Then the filter should have the values for multiselect condition "sku":
      | value   |
      | SKU1    |
      | SKU2    |
      | SKU3    |

  Scenario: Create 3 products that will be filtered for a specific SKU
    Given the site has a product "Shoe" priced at 100
    And the products sku is "SKU1"
    And the product is active
    And the product is published
    And the site has a product "Shoe 2" priced at 100
    And the products sku is "SKU2"
    And the product is active
    And the product is published
    And the site has a product "Shoe 3" priced at 100
    And the products sku is "SKU3"
    And the product is active
    And the product is published
    Then the filter should have 2 items for value "SKU1,SKU2" in field "sku[]"
    Then the filter should have 3 items for value "SKU1,SKU2,SKU3" in field "sku[]"
