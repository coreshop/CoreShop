@domain @optimistic_entity_lock
Feature: Locking an Entity to not overwrite it again

  Background:
    Given there is a pimcore class "Locking"
    And the definition has a input field "name"
    And the definition has a numeric integer field "optimisticLockVersion"
    And the definitions parent class is set to "\CoreShop\Behat\Model\Lock\OptimisticLock"
    And there is an instance of behat-class "Locking" with key "test1"
    And the object-instance has following values:
      | key               | value                                                             | type   |
      | name              | test                                                              | input  |

  Scenario:
    Given I successfully lock the object-instance with the current version
    And I reload the object-instance into object-instance-2
    And I change the object-instance-2 values:
      | key               | value                                                             | type   |
      | name              | test                                                              | input  |
    Then I unsuccessfully lock the object-instance with the current version

  Scenario:
    Given I successfully lock the object-instance with the current version
    And I reload the object-instance into object-instance-2
    And I change the object-instance-2 values:
      | key               | value                                                             | type   |
      | name              | test                                                              | input  |
    Then I successfully save versioned object-instance-2
    Then I unsuccessfully save versioned object-instance
