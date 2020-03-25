@index @domain
Feature: Adding a object index
  In order to make my catalog searchable
  I want to create a new index

  Background:
    Given the site operates on a store in "Austria"
    And the site has a product "Shoe" priced at 100

  Scenario: Create a new index
    Given the site has a index "myindex" for class "CoreShopProduct" with type "mysql"
    Then there should be a index "myindex"

  Scenario: Create a new index and add fields
    Given the site has a index "myindex" for class "CoreShopProduct" with type "mysql"
    And  the index has following fields:
    | key   | name            | type            | getter          | interpreter   | columnType   |
    | sku   | sku             | object          |                 |               | STRING       |
    | ean   | ean             | object          |                 |               | STRING       |
    | name  | internalName    | localizedfields | localizedfield  | localeMapping | STRING       |
    Then the index should have columns "ean, sku"
    And the index should have localized columns "internalName"
