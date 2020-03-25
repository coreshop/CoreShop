@product @domain
Feature: Adding a new Product
  In order to extend my catalog
  the product has a specific-price-rule for a customer group
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

  Scenario: Add a new customer-group product specific price rule which is valid
    Given adding a product specific price rule to product "Shoe" named "customer-group-discount"
    And the specific price rule is active
    And the specific price rule has a condition customer-groups with customer-group "Frequent Buyers"
    Then the specific price rule should be valid for product "Shoe"

  Scenario: Add a new customer-group product specific price rule which is invalid
    Given the site has a customer-group "New Customers"
    And the customer "some-customer@something.com" is in customer-group "New Customers"
    And adding a product specific price rule to product "Shoe" named "customer-group-discount"
    And the specific price rule is active
    And the specific price rule has a condition customer-groups with customer-group "Frequent Buyers"
    Then the specific price rule should be invalid for product "Shoe"
