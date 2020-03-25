@index_condition @domain
Feature: In order to have an abstraction for index and filters
  we have a in condition class

  Scenario: Create a new in condition and render it
    Given there is a in condition with field-name "XY" and values "1,2,5"
    Then the condition rendered for "mysql" should look like "`XY` IN ('1','2','5')"

  Scenario: Create a new not-in condition and render it
    Given there is a not-in condition with field-name "XY" and values "1,2,5"
    Then the condition rendered for "mysql" should look like "`XY` NOT IN ('1','2','5')"

