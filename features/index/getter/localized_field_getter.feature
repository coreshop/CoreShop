@index @index_getter @index_getter_localized
Feature: Adding a object index
  In order to make my catalog searchable
  I want to create a new index where a field comes from an localized field

  Background:
    Given there is a pimcore class "LocalizedTest"
    And the definition has a checkbox field "enabled"
    And the definition has a localized input field "name"
    And the definition has a localized textarea field "shortDescription"
    And the definitions parent class is set to "\CoreShop\Behat\Model\Index\TestIndex"

  Scenario: Create a new index and add fields
    Given the site has a index "localized_getter" for behat-class "LocalizedTest" with type "mysql"
    And  the index has following fields:
      | key              | name             | type     | getter         | columnType | interpreter   |
      | shortDescription | shortDescription | textarea | localizedfield | STRING     | localeMapping |
    Then the index should have localized columns "shortDescription"

  Scenario: Create a new index, add fields and index some objects
    Given the site has a index "localized_getter" for behat-class "LocalizedTest" with type "mysql"
    And  the index has following fields:
      | key               | name             | type     | getter         | columnType   | interpreter   |
      | shortDescription  | shortDescription | textarea | localizedfield | STRING       | localeMapping |
    And there is an instance of behat-class "LocalizedTest" with key "test1"
    And the object-instance has following values:
      | key               | value                                                             | type      |
      | enabled           | true                                                              | checkbox  |
      | shortDescription  | Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam | localized |
    Then the index localized column "shortDescription" for object-instance should have value "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam"