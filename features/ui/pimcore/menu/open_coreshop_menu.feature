@ui @ui_pimcore @menu
Feature: Test if I can open the CoreShop Menu

    Background: Sign in with email and password
        Given I log into the Pimcore backend

    Scenario:
        Given I open the CoreShop menu
        Then a Menu should be open
