@index @domain
Feature: Adding a object index
  In order to make my catalog searchable
  I want to create a new index where a field comes from an brick

  Background:
    Given there is a pimcore class "BrickTest"
    And the definition has a brick field "attributes"
    And the definition has a checkbox field "enabled"
    And the definition has a localized input field "name"
    And the definitions parent class is set to "\CoreShop\Behat\Model\Index\TestIndex"
    And there is a pimcore brick "Attribute"
    And the definition has a input field "name"
    And the definition has a input field "val"
    And the definition is allowed for behat-class "BrickTest" in field "attributes"

  Scenario: Create a new index and add fields
    Given the site has a index "brick_getter" for behat-class "BrickTest" with type "mysql"
    And  the index has following fields:
      | key   | name            | type         | getter   | columnType   | getterConfig                 | configuration                              |
      | name  | brick_name      | objectbricks | brick    | STRING       | {"brickField": "attributes"} | {"key": "name", "className": "Attribute"}  |
      | val   | brick_val       | objectbricks | brick    | STRING       | {"brickField": "attributes"} | {"key": "val", "className": "Attribute"}   |
    Then the index should have columns "brick_name, brick_val"

  Scenario: Create a new index, add fields and index some objects
    Given the site has a index "brick_getter" for behat-class "BrickTest" with type "mysql"
    And  the index has following fields:
      | key   | name            | type         | getter   | columnType   | getterConfig                 | configuration                              |
      | name  | brick_name      | objectbricks | brick    | STRING       | {"brickField": "attributes"} | {"key": "name", "className": "Attribute"}  |
      | val   | brick_val       | objectbricks | brick    | STRING       | {"brickField": "attributes"} | {"key": "val", "className": "Attribute"}   |
    And there is an instance of behat-class "BrickTest" with key "test1"
    And the object-instance has following values:
      | key         | value                                                              | type            |
      | enabled     | true                                                               | checkbox        |
      | name        | test                                                               | localized       |
      | attributes  | {"type": "Attribute", "values": {"name": "val1", "val": "val2"}}   | brick           |
    Then the index column "brick_name" for object-instance should have value "val1"
    And the index column "brick_val" for object-instance should have value "val2"
