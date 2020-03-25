@pimcore @domain
Feature: Adding a new pimcore collection

  Scenario: Create a new pimcore fieldcollection
    Given there is a pimcore field-collection "Colors"
    Then there should be a pimcore field-collection "Colors"

  Scenario: Create a new pimcore field-collection with a input field
    Given there is a pimcore field-collection "Attributes"
    And the definition has a input field "name"
    Then the definition should have a field named "name"
