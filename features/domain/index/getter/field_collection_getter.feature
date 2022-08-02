@domain @index
Feature: Adding a object index
  In order to make my catalog searchable
  I want to create a new index where a field comes from an fieldcollection

  Background:
    Given there is a pimcore field-collection "Collection"
    And the definition has a input field "name"
    And the definition has a input field "val"
    And there is a pimcore class "FieldcollectionTest"
    And the definition has a field-collection field "collection" for field-collection "Collection"
    And the definition has a checkbox field "enabled"
    And the definition has a localized input field "name"
    And the definitions parent class is set to "\CoreShop\Behat\Model\Index\TestIndex"

  Scenario: Create a new index and add fields
    Given the site has a index "collection_getter" for behat-class "FieldcollectionTest" with type "mysql"
    And  the index has following fields:
      | key   | name      | type             | getter          | columnType   | getterConfig                      | configuration                        |
      | name  | col_name  | fieldcollections | fieldcollection | STRING       | {"collectionField": "collection"} | {"className": "Collection"} |
      | val   | col_val   | fieldcollections | fieldcollection | STRING       | {"collectionField": "collection"} | {"className": "Collection"} |
    Then the index should have columns "col_name, col_val"


  Scenario: Create a new index, add fields and index some objects
    Given the site has a index "collection_getter" for behat-class "FieldcollectionTest" with type "mysql"
    And  the index has following fields:
      | key   | name      | type             | getter          | columnType   | getterConfig                      | configuration                        |
      | name  | col_name  | fieldcollections | fieldcollection | STRING       | {"collectionField": "collection"} | {"className": "Collection"} |
      | val   | col_val   | fieldcollections | fieldcollection | STRING       | {"collectionField": "collection"} | {"className": "Collection"} |
    And there is an instance of behat-class "FieldcollectionTest" with key "test1"
    And the object-instance has following values:
      | key         | value                                                                                                | type       |
      | enabled     | true                                                                                                 | checkbox   |
      | name        | test                                                                                                 | localized  |
      | collection  | {"type": "Collection", "values": [{"name": "val1", "val": "val"}, {"name": "val2", "val": "val"}]}   | collection |
    Then the index column "col_name" for object-instance should have value ",val1,val2,"
    And the index column "col_val" for object-instance should have value ",val,val,"
