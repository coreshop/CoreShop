@ui @account_login
Feature: Signing in to the store

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

    Scenario: Sign in ignores a redirect target pointing to a foreign host
        When I want to log in with "//example.com" as redirect target
        And I specify the username as "dominik@example.com"
        And I specify the password as "test"
        And I log in
        Then I should be logged in
