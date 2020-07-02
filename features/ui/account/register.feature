@ui @account_register
Feature: Account registration
    In order to make future purchases with ease
    As a Visitor
    I need to be able to create an account in the store

    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store

    Scenario: Registering a new account
        When I want to register a new account
        And I specify the first name as "Bill"
        And I specify the last name as "Gates"
        And I specify the email as "bill@gates.com"
        And I confirm this email
        And I specify the password as "gates"
        And I specify the address first name as "Bill"
        And I specify the address last name as "Gates"
        And I specify the address street as "Gates-Street"
        And I specify the address number as "2"
        And I specify the address country as country "Austria"
        And I specify the address post code as "5020"
        And I specify the address city as "Salzburg"
        And I confirm this password
        And I accept the terms of service
        And I register this account
        But I should be logged in
