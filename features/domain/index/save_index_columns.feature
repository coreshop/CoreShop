@domain @index
Feature: Saving the columns of an index
  In order to index the same field in different ways
  As an administrator
  I want to save several columns on the same field, each with its own name

  Background:
    Given the site operates on a store in "Austria"
    And the site has a product "Shoe" priced at 100
    And the site has a index "myindex" for class "CoreShopProduct" with type "mysql"

  Scenario: Columns on the same field are all saved
    When I save the index with following columns:
      | name       | key | type   | interpreter | columnType |
      | sku        | sku | object |             | STRING     |
      | skuSoundex | sku | object | soundex     | STRING     |
    Then the index should have column definitions "sku, skuSoundex"
    And the index should have columns "sku, skuSoundex"

  Scenario: Saving an index again keeps its columns
    Given I save the index with following columns:
      | name       | key | type   | interpreter | columnType |
      | sku        | sku | object |             | STRING     |
      | skuSoundex | sku | object | soundex     | STRING     |
    When I save the index again without changes
    Then the index should have column definitions "sku, skuSoundex"
    And the index column definitions should have kept their ids

  Scenario: Removing a column keeps the columns behind it
    Given I save the index with following columns:
      | name   | key | type   | interpreter | interpreterConfig       | columnType |
      | ean    | ean | object |             |                         | STRING     |
      | hasSku | sku | object | expression  | {"expression": "value"} | STRING     |
      | sku    | sku | object |             |                         | STRING     |
    When I save the index with following columns:
      | name   | key | type   | interpreter | interpreterConfig       | columnType |
      | hasSku | sku | object | expression  | {"expression": "value"} | STRING     |
      | sku    | sku | object |             |                         | STRING     |
    Then the index should have column definitions "hasSku, sku"
    And the index should have columns "hasSku, sku"

  Scenario: Column names have to be unique within an index
    Given I save the index with following columns:
      | name | key | type   | interpreter | columnType |
      | sku  | sku | object |             | STRING     |
    When I try to save the index with following columns:
      | name | key | type   | interpreter | columnType |
      | sku  | sku | object |             | STRING     |
      | SKU  | ean | object |             | STRING     |
    Then saving the index should fail because the column name "SKU" is used more than once
    And the index should have column definitions "sku"
