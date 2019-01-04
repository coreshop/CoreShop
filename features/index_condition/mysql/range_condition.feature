@index_condition @index_condition_mysql @index_condition_mysql_range
Feature: In order to have an abstraction for index and filters
  we have a range condition class

  Scenario: Create a range condition and render it
    Given there is a range condition with field-name "XY" from "1" to "100"
    Then the condition rendered for "mysql" should look like "`XY` >= 1 AND `XY` <= 100"
