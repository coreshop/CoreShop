@ui @account_register
Feature: Account registration
    In order to avoid making mistakes when registering account
    As a Visitor
    I want to be prevented from creating an account without required fields

    Background:
    Given the site operates on a store in "Austria"
    And the store "Austria" is the default store

    Scenario: Trying to register a new account with email that has been already used
        Given the site has a customer "bill@gates.com" with password "test"
        When I want to register a new account
        And I specify the first name as "Bill"
        And I specify the last name as "Gates"
        And I specify the email as "bill@gates.com"
        And I confirm this email
        And I specify the password as "gates"
        And I confirm this password
        And I specify the address first name as "Bill"
        And I specify the address last name as "Gates"
        And I specify the address street as "Gates-Street"
        And I specify the address number as "2"
        And I specify the address country as country "Austria"
        And I specify the address post code as "5020"
        And I specify the address city as "Salzburg"
        And I accept the terms of service
        And I register this account
        Then I should be notified that the email is already used
        And I should not be logged in

    Scenario: Trying to register a new account without specifying first name
        When I want to register a new account
        And I specify the last name as "Gates"
        And I specify the email as "bill@gates.com"
        And I confirm this email
        And I specify the password as "gates"
        And I confirm this password
        And I specify the address first name as "Bill"
        And I specify the address last name as "Gates"
        And I specify the address street as "Gates-Street"
        And I specify the address number as "2"
        And I specify the address country as country "Austria"
        And I specify the address post code as "5020"
        And I specify the address city as "Salzburg"
        And I accept the terms of service
        And I register this account
        Then I should be notified that the firstname is required
        And I should not be logged in

    Scenario: Trying to register a new account without specifying last name
        When I want to register a new account
        And I specify the first name as "Bill"
        And I specify the email as "bill@gates.com"
        And I confirm this email
        And I specify the password as "gates"
        And I confirm this password
        And I specify the address first name as "Bill"
        And I specify the address last name as "Gates"
        And I specify the address street as "Gates-Street"
        And I specify the address number as "2"
        And I specify the address country as country "Austria"
        And I specify the address post code as "5020"
        And I specify the address city as "Salzburg"
        And I accept the terms of service
        And I register this account
        Then I should be notified that the lastname is required
        And I should not be logged in

    Scenario: Trying to register a new account without specifying password
        When I want to register a new account
        And I specify the first name as "Bill"
        And I specify the last name as "Gates"
        And I specify the email as "bill@gates.com"
        And I confirm this email
        And I specify the address first name as "Bill"
        And I specify the address last name as "Gates"
        And I specify the address street as "Gates-Street"
        And I specify the address number as "2"
        And I specify the address country as country "Austria"
        And I specify the address post code as "5020"
        And I specify the address city as "Salzburg"
        And I accept the terms of service
        And I register this account
        Then I should be notified that the password is required
        And I should not be logged in

    Scenario: Trying to register a new account without confirming password
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
        And I accept the terms of service
        And I register this account
        Then I should be notified that the password do not match
        And I should not be logged in

    Scenario: Trying to register a new account without specifying email
        When I want to register a new account
        And I specify the first name as "Bill"
        And I specify the last name as "Gates"
        And I specify the password as "gates"
        And I specify the address first name as "Bill"
        And I specify the address last name as "Gates"
        And I specify the address street as "Gates-Street"
        And I specify the address number as "2"
        And I specify the address country as country "Austria"
        And I specify the address post code as "5020"
        And I specify the address city as "Salzburg"
        And I accept the terms of service
        And I register this account
        Then I should be notified that the email is required
        And I should not be logged in
