@ui @ui_pimcore @tax_rate
Feature: Test if I can open the Tax Rates Panel
    Scenario:
        Given I am a logged in admin
        And I open Pimcore
        And I open resource "coreshop.taxation", "tax_item"
        Then tax-rates tab is open
