@ui @ui_pimcore @country
Feature: Test if I can create a new country

    Scenario:
        Given I am a logged in admin
        And I open Pimcore
        And I open the countries tab
        Then I create a new country named "Austria"
