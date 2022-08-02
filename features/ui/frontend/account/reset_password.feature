@ui @account_reset
Feature: Resetting a password
    In order to login to my account when I forgot my password
    As a Visitor
    I need to be able to reset my password

    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store

    Scenario: Resetting an account password
        Given the site has a customer "dominik@example.com" with password "test"
        When I want to reset password
        And I specify the email as "dominik@example.com"
        And I reset it
        Then the notification rule for "user" should have been fired
#
#    @ui @email
#    Scenario: Changing my account password with token I received
#        Given I have already received a resetting password email
#        When I follow link on my email to reset my password
#        And I specify my new password as "newp@ssw0rd"
#        And I confirm my new password as "newp@ssw0rd"
#        And I reset it
#        Then I should be notified that my password has been successfully reset
#        And I should be able to log in as "goodman@example.com" with "newp@ssw0rd" password
