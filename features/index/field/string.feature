@index @index_field @index_field_string
Feature: Adding a object index
  In order to make my catalog searchable
  I want to create a new string column

  Background:
    Given there is a pimcore class "TestIndexFields"
    And the definitions parent class is set to "\CoreShop\Behat\Model\Index\TestIndexFields"

  Scenario: Create a new index and add integer Field
    Given the site has a index "myindex" for behat-class "TestIndexFields" with type "mysql"
    And  the index has following fields:
    | key      | name     | type   | getter | interpreter | columnType |
    | string   | string   | object |        |             | STRING     |
    Then the index should have columns "string"