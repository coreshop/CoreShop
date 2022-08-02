@domain @index
Feature: Adding a object index
  In order to make my catalog fast searchable
  I want to create a new index with indexes

  Scenario: Create a new index and add a index to the table
    Given the site has a index "myindex" for class "CoreShopProduct" with type "mysql"
    And  the index has following fields:
    | key   | name            | type            | getter          | interpreter   | columnType   |
    | sku   | sku             | object          |                 |               | STRING       |
    | ean   | ean             | object          |                 |               | STRING       |
    | name  | internalName    | localizedfields | localizedfield  | localeMapping | STRING       |
    And the index has an index for columns "sku, ean"
    Then the index should have an index for "sku, ean"

  Scenario: Create a new index and tow indexes to the table
    Given the site has a index "myindex" for class "CoreShopProduct" with type "mysql"
    And  the index has following fields:
    | key   | name            | type            | getter          | interpreter   | columnType   |
    | sku   | sku             | object          |                 |               | STRING       |
    | ean   | ean             | object          |                 |               | STRING       |
    | ean   | ean2            | object          |                 |               | STRING       |
    | name  | internalName    | localizedfields | localizedfield  | localeMapping | STRING       |
    And the index has an index for columns "sku, ean"
    And the index has an index for columns "ean, ean2"
    Then the index should have an index for "sku, ean"
    Then the index should have an index for "ean, ean2"

  Scenario: Create a new index and add a localized index to the table
    Given the site has a index "myindex" for class "CoreShopProduct" with type "mysql"
    And  the index has following fields:
    | key   | name            | type            | getter          | interpreter   | columnType   |
    | sku   | sku             | object          |                 |               | STRING       |
    | ean   | ean             | object          |                 |               | STRING       |
    | name  | internalName    | localizedfields | localizedfield  | localeMapping | STRING       |
    And the index has an localized index for columns "internalName"
    Then the index should have an localized index for "internalName"
