@domain @index_condition
Feature: In order to have an abstraction for index and filters
  we have a concat condition class

  Scenario: Create a new contact
    Given there is a compare condition with field-name "XY" operator "=" and value "blub" with identifier "eq1"
    Given there is a compare condition with field-name "ZX" operator "!=" and value "blub" with identifier "eq2"
    And there is a concat condition with field-name "test" operator "AND" and conditions "eq1,eq2"
    Then the condition rendered for "mysql" should look like "(`XY` = 'blub' AND `ZX` != 'blub')"

  Scenario: Create a new nested contact
    Given there is a compare condition with field-name "XY" operator "=" and value "blub" with identifier "eq1"
    Given there is a compare condition with field-name "ZX" operator "!=" and value "blub" with identifier "eq2"
    Given there is a compare condition with field-name "ZY" operator "=" and value "blub" with identifier "eq3"
    And there is a concat condition with field-name "test" operator "AND" and conditions "eq1,eq2" with identifier "concat1"
    And there is a concat condition with field-name "blub" operator "OR" and conditions "concat1,eq3"
    Then the condition rendered for "mysql" should look like "((`XY` = 'blub' AND `ZX` != 'blub') OR `ZY` = 'blub')"
