@cli @cli_install_demo
Feature: Load sample data feature
    In order to have sample data in CoreShop
    As a Developer
    I want to run a command that loads sample data

    Scenario: Running install sample data command
        When I run CoreShop Install Fixtures Data command
        And I confirm loading Fixtures Data command
        And I run CoreShop Install Demo Data command
        And I confirm loading Demo Data command
        Then the command should finish successfully
