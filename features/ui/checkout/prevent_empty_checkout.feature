@ui @checkout
Feature: Ability to complete the checkout

  Background:
    Given the site operates on a store in "Austria"
    And the store "Austria" is the default store
    And I am a logged in customer

  Scenario: Try opening address checkout step
    Given I try to open the address checkout step
    Then I should be on the cart summary page

  Scenario: Try opening shipping checkout step
    Given I try to open the shipping checkout step
    Then I should be on the cart summary page

  Scenario: Try opening payment checkout step
    Given I try to open the payment checkout step
    Then I should be on the cart summary page

  Scenario: Try opening payment checkout step
    Given I try to open the summary checkout step
    Then I should be on the cart summary page

