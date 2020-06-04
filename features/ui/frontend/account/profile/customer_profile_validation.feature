@ui @customer_profile
Feature: Customer profile validation

    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store
        And the site has a customer "elon@musk.com" with password "cybertruck" and name "Elon" "Musk"
        And I am logged in as "elon@musk.com"

    Scenario: Check if the name is valid
        Then my name should be "Elon Musk"
        And my email should be "elon@musk.com"
