@ui @customer_profile
Feature: Changing a customer password

    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store
        And the site has a customer "elon@musk.com" with password "cybertruck"
        And I am logged in as "elon@musk.com"

    Scenario: Trying to change my password with a wrong current password
        When I want to change my password
        And I specify the current password as "spacex"
        And I specify the new password as "paypal"
        And I confirm this password as "paypal"
        And I save my new password
        Then I should be notified that provided password is different than the current one

    Scenario: Trying to change my password with a wrong confirmation password
        When I want to change my password
        And I specify the current password as "cybertruck"
        And I specify the new password as "spacex"
        And I confirm this password as "paypal"
        And I save my new password
        Then I should be notified that the entered passwords do not match
