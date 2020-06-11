@ui @ui_pimcore @currency @wip
Feature: Test if I can open the Currencies Panel
    Scenario:
        Given I am a logged in admin
        And I open Pimcore
        And I open resource "coreshop.currency", "currency"
        Then currencies tab is open
