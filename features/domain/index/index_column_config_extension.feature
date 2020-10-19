@index
Feature: Adding a object index
  In order to make my catalog searchable
  I want to create a new index with a column and a special config

  Background:
    Given there is a pimcore class "ColumnExtensionTest"
    And the definition has a checkbox field "enabled"
    And the definition has a localized input field "name"
    And the definitions parent class is set to "\CoreShop\Behat\Model\Index\TestIndex"

  Scenario: Create a new index and the extension should create fields
    Given the site has a index "extension_column_config" for behat-class "ColumnExtensionTest" with type "mysql"
    And  the index has following fields:
      | key              | name             | type     | getter         | columnType | interpreter       |
      | decimalTest      | decimalTest      | decimal  |                | DOUBLE     |                   |
    Then the index should have a column "decimalTest" of type "decimalTest NUMERIC(20, 20) DEFAULT NULL"
