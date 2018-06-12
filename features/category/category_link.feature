@category @category_link
Feature: In order that a customer can visit the category page
  The website needs to create a URL

  Background:
    Given the site operates on a store in "Austria"
    And the site has a category "Shirts"

  Scenario: Create URL for product
    Then the generated url for object should be "/en/shop/shirts~c%id"
