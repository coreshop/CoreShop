@domain @index_condition
Feature: In order to have an abstraction for index and filters
  we have a is null condition class

  Scenario: Create a is null condition and render it
    Given there is a is-null condition with field-name "XY"
    Then the condition rendered for "mysql" should look like "`XY` IS NULL"

  Scenario: Create a not is null condition and render it
    Given there is a is-not-null condition with field-name "XY"
    Then the condition rendered for "mysql" should look like "`XY` IS NOT NULL"

