@ui @ui_pimcore @menu
Feature: Count CoreShop Menu Items

    Background: Sign in with email and password
        Given I log into the Pimcore backend

    Scenario:
        Given I open the CoreShop menu
        Then the opened Menu should have 13 items
