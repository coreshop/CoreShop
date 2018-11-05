@index_condition @index_condition_mysql @index_condition_mysql_compare
Feature: In order to have an abstraction for index and filters
  we have a compare condition class

  Scenario: Create a new compare eq condition and render it
    Given there is a compare condition with field-name "XY" operator "=" and value "blub"
    Then the condition rendered for "mysql" should look like "`XY` = 'blub'"

  Scenario: Create a new match condition and render it
    Given there is a match condition with field-name "XY" and value "blub"
    Then the condition rendered for "mysql" should look like "`XY` = 'blub'"

  Scenario: Create a new compare not equal condition and render it
    Given there is a compare condition with field-name "XY" operator "!=" and value "blub"
    Then the condition rendered for "mysql" should look like "`XY` != 'blub'"

  Scenario: Create a new not-match condition and render it
    Given there is a not-match condition with field-name "XY" and value "blub"
    Then the condition rendered for "mysql" should look like "`XY` <> 'blub'"

  Scenario: Create a new greater-than condition and render it
    Given there is a greater-than condition with field-name "XY" and value "1"
    Then the condition rendered for "mysql" should look like "`XY` > '1'"

  Scenario: Create a new greater-than-equal condition and render it
    Given there is a greater-than-equal condition with field-name "XY" and value "1"
    Then the condition rendered for "mysql" should look like "`XY` >= '1'"

  Scenario: Create a new lower-than condition and render it
    Given there is a lower-than condition with field-name "XY" and value "1"
    Then the condition rendered for "mysql" should look like "`XY` < '1'"

  Scenario: Create a new lower-than-equal condition and render it
    Given there is a lower-than-equal condition with field-name "XY" and value "1"
    Then the condition rendered for "mysql" should look like "`XY` <= '1'"

