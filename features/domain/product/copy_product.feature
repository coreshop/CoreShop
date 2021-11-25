@domain @product
Feature: Adding a new Product and copy it

  Background:
    Given the site operates on a store in "Austria"
    Given the site has a product "Shoe" priced at 100

  Scenario: Copy the product
    Given I copy the product
    Then the product and the copied-object should have it's own price
