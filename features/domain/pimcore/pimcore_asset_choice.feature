@domain @pimcore
Feature: Resolve Pimcore assets from submitted asset ids

  Background:
    Given the site has an asset "logo.png"

  Scenario: Resolve the asset when the form receives the asset id as string
    When I submit the asset id as string to the asset choice form
    Then the asset should have been resolved
