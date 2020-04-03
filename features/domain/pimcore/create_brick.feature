@domain @pimcore
Feature: Adding a new pimcore brick

  Scenario: Create a new pimcore brick
    Given there is a pimcore brick "Attributes"
    Then there should be a pimcore brick "Attributes"

  Scenario: Create a new pimcore brick with a input field
    Given there is a pimcore brick "Attributes"
    And the definition has a input field "name"
    Then the definition should have a field named "name"
