@ui @ui_pimcore @country
Feature: Test if I can open the Countries Panel
    Scenario:
        Given I am a logged in admin
        And I open Pimcore
        And I open resource "coreshop.address", "country"
        Then countries tab is open
