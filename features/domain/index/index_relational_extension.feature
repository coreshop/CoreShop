@domain @index
Feature: Adding a object index
  In order to make my catalog searchable
  I want to create a new index with relational extensions

  Background:
    Given there is a pimcore class "RelationalTest"
    And the definition has a checkbox field "enabled"
    And the definition has a localized input field "name"
    And the definitions parent class is set to "\CoreShop\Behat\Model\Index\TestIndex"
    And the site has a index "relational_extension" for behat-class "RelationalTest" with type "mysql"
    And  the index has following fields:
      | key              | name             | type     | getter         | columnType | interpreter       |
      | booleanTest      | booleanTest      | checkbox |                | BOOLEAN    | custom_relational |
    Then the index should have relational columns "src, dest, fieldname, type, src_virtualObjectId, custom_col"

  Scenario: Create a new index and the extension should create fields
    Given there is an instance of behat-class "RelationalTest" with key "test1"
     And the object-instance has following values:
      | key         | value | type     |
      | enabled     | true  | checkbox |
    Then the index relational column "custom_col" for object-instance "test1" should have value "blub"
