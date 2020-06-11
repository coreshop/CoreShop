@ui @ui_pimcore @tax_rule @wip
Feature: Test if I can open the Tax Rule Groups Panel
    Scenario:
        Given I am a logged in admin
        And I open Pimcore
        And I open resource "coreshop.taxation", "tax_rule_group"
        Then tax-rule-groups tab is open
