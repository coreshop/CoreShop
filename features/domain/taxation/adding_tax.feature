@domain @taxation
Feature: Adding a new Tax
  In Order to tax my products
  I want to create a new tax rate

  Scenario: Create a new tax rate
    Given the site has a tax rate "AT" with "20%" rate
    Then there should be a tax rate "AT" with "20%" rate

  Scenario: Create a new tax rate and set it active
    Given the site has a tax rate "AT" with "20%" rate
    And the tax rate "AT" is active
    Then there should be a tax rate "AT" with "20%" rate
