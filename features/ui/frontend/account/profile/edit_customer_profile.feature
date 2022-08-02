@ui @customer_profile
Feature: Edit profile

    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store
        And the site has a customer "elon@musk.com" with password "cybertruck" and name "Elon" "Musk"
        And I am logged in as "elon@musk.com"

    Scenario: Edit first name and check if the name is valid
        When I want to change my personal information
        And I specify the new first name as "Lina"
        And I save my personal information
        Then my name should be "Lina Musk"

    Scenario: Edit last name and check if the name is valid
        When I want to change my personal information
        And I specify the new last name as "Muscle"
        And I save my personal information
        Then my name should be "Elon Muscle"
