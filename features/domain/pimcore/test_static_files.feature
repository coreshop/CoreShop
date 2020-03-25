@pimcore @domain
Feature: In order for the pimcore backend to function
  It needs to load all registered CoreShop Static Resources

  Scenario: Test existence for admin js resources
    Then all admin js resources should exist

  Scenario: Test existence for admin css resources
    Then all admin css resources should exist

  Scenario: Test existence for editmode js resources
    Then all editmode js resources should exist

  Scenario: Test existence for editmode css resources
    Then all editmode css resources should exist
