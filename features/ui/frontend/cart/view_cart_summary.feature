@ui @cart
Feature: Viewing the cart summary

    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store

    Scenario: Viewing information about empty cart
        When I see the summary of my cart
        Then my cart should be empty
