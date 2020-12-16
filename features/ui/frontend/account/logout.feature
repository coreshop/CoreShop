@ui @account_logout
Feature: Logout of the store

    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store
        And the site has a customer "dominik@example.com" with password "test"

    Scenario: Sign in with email and password
        When I want to log in
        And I specify the username as "dominik@example.com"
        And I specify the password as "test"
        And I log in
        Then I should be logged in
        When I log out
        Then I should not be logged in
