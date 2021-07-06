@ui @ui_pimcore @login
Feature: Signing in to the Pimcore Backend

    Scenario: Sign in with email and password
        When I want to log into Pimcore backend
        And I specify the username as "admin"
        And I specify the password as "coreshop"
        And I log in
        Then I should be logged in
