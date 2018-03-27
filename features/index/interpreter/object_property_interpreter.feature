@index @index_interpreter @index_interpreter_object_property
Feature: Adding a object index
  In order to make my catalog searchable
  I want to create a new index and add objects and need to interpret some informations

  Background:
    Given there is a pimcore class "InterpreterTest"
    And the definition has a checkbox field "enabled"
    And the definition has a localized input field "name"
    And the definition has a href field "objectHref"
    And the definitions parent class is set to "\CoreShop\Behat\Model\Index\TestIndex"
    And the site operates on a store in "Austria"
    And the site has a index "myindex" for behat-class "InterpreterTest" with type "mysql"
    And  the index has following fields:
      | key        | name       | type    | getter | interpreter    | interpreterConfig        | columnType |
      | objectHref | objectHref | href    |        | objectProperty | {"property": "fullPath"} | STRING     |

  Scenario: Adding a simple object to the index
    Given there is an instance of behat-class "InterpreterTest" with key "test1"
    And the object-instance has following values:
      | key        | value  | type      |
      | enabled    | true   | checkbox  |
      | name       | test   | localized |
      | objectHref | 1      | href      |
    Then the index column "objectHref" for object-instance should have value "/"