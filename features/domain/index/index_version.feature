@domain @index
Feature: Adding a object index
  In order to make my catalog searchable
  I want to create a new index
  But when the user saves the object as version, the index should not change

  Background:
    Given there is a pimcore class "BooleanTest"
    And the definition has a checkbox field "enabled"
    And the definition has a checkbox field "booleanTest"
    And the definition has a localized input field "name"
    And the definitions parent class is set to "\CoreShop\Behat\Model\Index\TestIndex"
    And the site has a index "boolean" for behat-class "BooleanTest" with type "mysql"
    And  the index has following fields:
      | key         | name        | type     | getter | columnType | interpreter |
      | booleanTest | booleanTest | checkbox |        | BOOLEAN    |             |
    And there is an instance of behat-class "BooleanTest" with key "test1"
    And the object-instance has following values:
      | key         | value | type     |
      | enabled     | true  | checkbox |
      | booleanTest | false | checkbox |
    Then the index column "booleanTest" for object-instance "test1" should have value "0"

  Scenario: Save object normally
    Given the object-instance has following values:
      | key         | value | type     |
      | enabled     | true  | checkbox |
      | booleanTest | true  | checkbox |
    Then the index column "booleanTest" for object-instance "test1" should have value "1"

  Scenario: Save object as version, index should not update
    Given the object-instance has following values as version:
      | key         | value | type     |
      | enabled     | true  | checkbox |
      | booleanTest | true  | checkbox |
    Then the index column "booleanTest" for object-instance "test1" should have value "0"

  Scenario: Save object normally when index allows version changes
    Given the index allows version changes
    And the object-instance has following values:
      | key         | value | type     |
      | enabled     | true  | checkbox |
      | booleanTest | true  | checkbox |
    Then the index column "booleanTest" for object-instance "test1" should have value "1"

  Scenario: Save object as version when index allows version changes
    Given the index allows version changes
    And the object-instance has following values as version:
      | key         | value | type     |
      | enabled     | true  | checkbox |
      | booleanTest | true  | checkbox |
    Then the index column "booleanTest" for object-instance "test1" should have value "1"




