@pimcore @domain
Feature: Adding a new pimcore Class

  Scenario: Create a new pimcore class
    Given there is a pimcore class "Setting"
    Then there should be a pimcore class "Setting"

  Scenario: Create a new pimcore class with a input field
    Given there is a pimcore class "Setting"
    And the definition has a input field "name"
    Then the definition should have a field named "name"

  Scenario: Create a new pimcore class with a parent class set
    Given there is a pimcore class "IndexTest"
    And the definition has a checkbox field "enabled"
    And the definition has a localized input field "name"
    And the definitions parent class is set to "\CoreShop\Behat\Model\Index\TestIndex"
    Then an instance of definition should implement "\CoreShop\Behat\Model\Index\TestIndex"
    Then an instance of definition should implement "\CoreShop\Component\Index\Model\IndexableInterface"

  Scenario: Create a new pimcore class with a brick field
    Given there is a pimcore class "BrickTest"
    And the definition has a brick field "attributes"
    And there is a pimcore brick "Attribute"
    And the definition has a input field "name"
    And the definition has a input field "val"
    And the definition is allowed for behat-class "BrickTest" in field "attributes"
