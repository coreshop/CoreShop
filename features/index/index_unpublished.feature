@index @index_unpublished @index_unpublished_object
Feature: Adding a object index
  In order to make my catalog searchable
  I want to create a new index
  But when the user unpublishes the object, the object should be removed from the index

  Background:
    Given there is a pimcore class "Test"
    And the definition has a checkbox field "enabled"
    And the definition has a localized input field "name"
    And the definitions parent class is set to "\CoreShop\Behat\Model\Index\TestEnableIndex"
    And the site has a index "enabled_index" for behat-class "Test" with type "mysql"
    And there is an instance of behat-class "Test" with key "test1"
    And the object-instance has following values:
      | key     | value     | type      |
      | name    | Test Name | localized |

  Scenario: The object instance is enabled but unpublished
    Given the object-instance has following values:
      | key     | value     | type      |
      | enabled | true      | checkbox  |
    Then the index should not have indexed the object

  Scenario: The object instance is enabled and published
    Given the object-instance has following values:
      | key     | value     | type      |
      | enabled | true      | checkbox  |
    And the object-instance is published
    Then the index should have indexed the object

  Scenario: The object instance is disabled and published
    Given the object-instance has following values:
      | key     | value     | type      |
      | enabled | false     | checkbox  |
    And the object-instance is published
    Then the index should not have indexed the object

  Scenario: The object instance is disabled and unpublished
    Given the object-instance has following values:
      | key     | value     | type      |
      | enabled | false     | checkbox  |
    Then the index should not have indexed the object