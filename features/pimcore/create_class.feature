@pimcore
Feature: Adding a new pimcore Class

  Scenario: Create a new pimcore class
    Given there is a pimcore class "Setting"
    Then there should be a pimcore class "Setting"

  Scenario: Create a new pimcore class with a input field
    Given there is a pimcore class "Setting"
    Then there should be a pimcore class "Setting"
    And the definition has a input field "name"
    Then the definition should have a field named "name"