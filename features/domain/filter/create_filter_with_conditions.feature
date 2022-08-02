@domain @filter
Feature: Adding a filter for an index
  In order to make my catalog searchable
  I want to create a new index with an filter

  Background:
    Given the site operates on a store in "Austria"
    And the site has a product "Shoe" priced at 100

  Scenario: Create a new filter with conditions
    Given the site has a index "myindex" for class "CoreShopProduct" with type "mysql"
    And  the index has following fields:
      | key   | name            | type            | getter          | interpreter   | columnType   |
      | sku   | sku             | object          |                 |               | STRING       |
      | ean   | ean             | object          |                 |               | STRING       |
      | name  | internalName    | localizedfields | localizedfield  | localeMapping | STRING       |
    And the site has a filter "myfilter" for index "myindex"
    And the filter has following conditions:
      | type   | label        | field        |
      | select | SKU Select   | sku          |
      | select | EAN Select   | ean          |
      | select | Name Select  | internalName |
    Then there should be a filter with name "myfilter"
    And the filter should have 3 conditions
