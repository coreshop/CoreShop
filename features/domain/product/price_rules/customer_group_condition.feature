@domain @product
Feature: Adding a new Product
  In order to extend my catalog
  the catalog has a price-rule for a customer group
  I want to create a new product

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And the site has a customer-group "Frequent Buyers"
    And the site has a customer "some-customer@something.com"
    And it is in customer-group "Frequent Buyers"
    And I am customer "some-customer@something.com"
    And the site has a product "Shoe" priced at 100
    Then the product "Shoe" should be priced at "100"
    Then I should be logged in with email "some-customer@something.com"

  Scenario: Add a new customer-group category price rule which is valid
    Given adding a product price rule named "customer-group-discount"
    And the price rule is active
    And the price rule has a condition customer-groups with customer-group "Frequent Buyers"
    Then the price rule should be valid for product "Shoe"

  Scenario: Add a new customer-group category price rule which is invalid
    Given the site has a customer-group "New Customers"
    And the customer "some-customer@something.com" is in customer-group "New Customers"
    And adding a product price rule named "customer-group-discount"
    And the price rule is active
    And the price rule has a condition customer-groups with customer-group "Frequent Buyers"
    Then the price rule should be invalid for product "Shoe"
