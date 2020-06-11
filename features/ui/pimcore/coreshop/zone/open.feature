@ui @ui_pimcore @zone @wip
Feature: Test if I can open the Zones Panel
    Scenario:
        Given I am a logged in admin
        And I open Pimcore
        And I open resource "coreshop.address", "zone"
        Then zones tab is open
