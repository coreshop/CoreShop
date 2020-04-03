@domain @index
Feature: Adding a object index
  In order to make my catalog searchable
  I want to create a new index with boolean values

  Background:
    Given there is a pimcore class "BooleanTest"
    And the definition has a checkbox field "enabled"
    And the definition has a checkbox field "booleanTest"
    And the definition has a localized input field "name"
    And the definitions parent class is set to "\CoreShop\Behat\Model\Index\TestIndex"

  Scenario: Create a new index and add fields
    Given the site has a index "boolean" for behat-class "BooleanTest" with type "mysql"
    And  the index has following fields:
      | key              | name             | type     | getter         | columnType | interpreter   |
      | booleanTest      | booleanTest      | checkbox |                | BOOLEAN    |               |
    And there is an instance of behat-class "BooleanTest" with key "test1"
    And the object-instance has following values:
      | key         | value | type     |
      | enabled     | true  | checkbox |
      | booleanTest | false | checkbox |
    And there is an instance of behat-class "BooleanTest" with key "test2"
    And the object-instance has following values:
      | key         | value | type     |
      | enabled     | true  | checkbox |
      | booleanTest | true  | checkbox  |
    And there is an instance of behat-class "BooleanTest" with key "test3"
    And the object-instance has following values:
      | key         | value | type     |
      | enabled     | true  | checkbox |
      | booleanTest | null  | checkbox  |
    Then the index column "booleanTest" for object-instance "test1" should have value "0"
    Then the index column "booleanTest" for object-instance "test2" should have value "1"
    Then the index column "booleanTest" for object-instance "test3" should have value "0"
