@ui @ui_pimcore @menu @wip
Feature: Test if I can open the CoreShop Menu

    Background: Sign in with email and password
        Given I log into the Pimcore backend

    Scenario:
        Given I open the CoreShop menu
        And I hover over the Menu Item with Name "Price Rules"
        Then Two Menus should be opened
