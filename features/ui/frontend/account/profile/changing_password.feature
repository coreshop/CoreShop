@ui @customer_profile
Feature: Changing a customer password

    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store
        And the site has a customer "elon@musk.com" with password "cybertruck"
        And I am logged in as "elon@musk.com"

    Scenario: Changing my password
        When I want to change my password
        And I change password from "cybertruck" to "spacex"
        And I save my new password
        Then I should be notified that my password has been successfully changed
