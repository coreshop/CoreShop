@ui @customer_profile
Feature: Validate Edit Profile

    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store
        And the site has a customer "spacex@musk.com"
        And the site has a customer "elon@musk.com" with password "cybertruck" and name "Elon" "Musk"
        And I am logged in as "elon@musk.com"

    Scenario: Trying to remove first name
        When I want to change my personal information
        And I remove the first name
        And I save my personal information
        Then I should be notified that the firstname is required
        Then my name should still be "Elon Musk"

    Scenario: Trying to remove last name name
        When I want to change my personal information
        And I remove the last name
        And I save my personal information
        Then I should be notified that the lastname is required
        Then my name should still be "Elon Musk"

#   Not Possible until this is merged: https://github.com/symfony/panther/pull/328
#    Scenario: Trying to remove email
#        When I want to change my personal information
#        And I remove the email
#        And I also remove the confirm email
#        And I save my personal information
#        Then I should be notified that the email is required
#        Then my email should still be "elon@musk.com"
#
#    Scenario: Trying to use a wrong confirm email
#        When I want to change my personal information
#        And I remove the email
#        And I save my personal information
#        Then I should be notified that the email does not match
#        Then my email should still be "elon@musk.com"
#
#    Scenario: Trying to change my email to an invalid value
#        When I want to change my personal information
#        And I specify the new email as "spacex"
#        And I confirm this email
#        And I save my personal information
#        Then I should be notified that the email is invalid
#        Then my email should still be "elon@musk.com"
#
#    Scenario: Trying to change my email to an existing value
#        When I want to change my personal information
#        And I specify the new email as "spacex@musk.com"
#        And I confirm this email
#        And I save my personal information
#        Then I should be notified that the email is already used
#        Then my email should still be "elon@musk.com"
