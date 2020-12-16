@ui @ui_pimcore @state
Feature: Test if I can open the States Panel
    Scenario:
        Given I am a logged in admin
        And I open Pimcore
        And I open resource "coreshop.address", "state"
        Then states tab is open
