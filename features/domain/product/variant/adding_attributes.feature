@domain @product @variant
Feature: Adding a Attributes

  Background:
    Given the site operates on a store in "Austria"
    
  Scenario: Create a Color Attribute Group
    Given the site has a attribute group "Color"
    And the site has a color attribute "red" with hex code "#FF0000" in attribute group
    And the site has a color attribute "blue" with hex code "#0000FF" in attribute group
    And the site has a color attribute "green" with hex code "#00FF00" in attribute group
    Then the attribute group "Color" should have 3 attributes

  Scenario: Create a Value Attribute Group
    Given the site has a attribute group "Size"
    And the site has a value attribute "S" in attribute group
    And the site has a value attribute "M" in attribute group
    And the site has a value attribute "L" in attribute group
    And the site has a value attribute "XL" in attribute group
    And the site has a value attribute "XXL" in attribute group
    Then the attribute group "Size" should have 5 attributes

  Scenario: Create multiple Attribute Group
    Given the site has a attribute group "Size"
    And the site has a value attribute "S" in attribute group
    And the site has a value attribute "M" in attribute group
    And the site has a value attribute "L" in attribute group
    And the site has a value attribute "XL" in attribute group
    And the site has a value attribute "XXL" in attribute group
    Then the attribute group "Size" should have 5 attributes
    Given the site has a attribute group "Color"
    And the site has a color attribute "red" with hex code "#FF0000" in attribute group
    And the site has a color attribute "blue" with hex code "#0000FF" in attribute group
    And the site has a color attribute "green" with hex code "#00FF00" in attribute group
    Then the attribute group "Color" should have 3 attributes
    Given the site has a attribute group "Season"
    And the site has a value attribute "Winter" in attribute group
    And the site has a value attribute "Summer" in attribute group
    Then the attribute group "Season" should have 2 attributes