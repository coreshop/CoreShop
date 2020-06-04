@ui @account_login
Feature: Login in to the store validation
    In order to avoid making mistakes when signing in to the store
    As a Visitor
    I want to be prevented from signing in with bad credentials

    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store
        And the site has a customer "dominik@example.com" with password "test"

    Scenario: Trying to sign in with bad credentials
        When I want to log in
        And I specify the username as "wrong@example.com"
        And I specify the password as "wrong"
        And I try to log in
        Then I should be notified about bad credentials
        And I should not be logged in

    Scenario: Trying to sign in after my account was deleted
        Given the customer "dominik@example.com" was deleted
        When I want to log in
        And I specify the username as "dominik@example.com"
        And I specify the password as "test"
        And I try to log in
        Then I should be notified about bad credentials
        And I should not be logged in
