@index_condition @index_condition_mysql @index_condition_mysql_like
Feature: In order to have an abstraction for index and filters
  we have a like condition class

  Scenario: Create a like both condition and render it
    Given there is a like condition with field-name "XY" and pattern "both" and value "blub"
    Then the condition rendered for "mysql" should look like "`XY` LIKE '%blub%'"

  Scenario: Create a like right condition and render it
    Given there is a like condition with field-name "XY" and pattern "right" and value "blub"
    Then the condition rendered for "mysql" should look like "`XY` LIKE 'blub%'"

  Scenario: Create a like left condition and render it
    Given there is a like condition with field-name "XY" and pattern "left" and value "blub"
    Then the condition rendered for "mysql" should look like "`XY` LIKE '%blub'"

  Scenario: Create a not-like both condition and render it
    Given there is a not-like condition with field-name "XY" and pattern "both" and value "blub"
    Then the condition rendered for "mysql" should look like "`XY` NOT LIKE '%blub%'"

  Scenario: Create a not-like right condition and render it
    Given there is a not-like condition with field-name "XY" and pattern "right" and value "blub"
    Then the condition rendered for "mysql" should look like "`XY` NOT LIKE 'blub%'"

  Scenario: Create a not-like left condition and render it
    Given there is a not-like condition with field-name "XY" and pattern "left" and value "blub"
    Then the condition rendered for "mysql" should look like "`XY` NOT LIKE '%blub'"
