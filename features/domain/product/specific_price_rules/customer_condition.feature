@product @domain
Feature: Adding a new Product
  In order to extend my catalog
  the product has a specific-price-rule for a customer
  I want to create a new product

  Background:
    Given the site operates on a store in "Austria"
    Given the site has a currency "Euro" with iso "EUR"
    Given I am in country "Austria"
    Given the site has a customer "some-customer@something.com"
    Given I am customer "some-customer@something.com"
    Given the site has a product "Shoe" priced at 100
    Then the product "Shoe" should be priced at "100"
    Then I should be logged in with email "some-customer@something.com"

  Scenario: Add a new customer product specific price rule which is valid
    Given adding a product specific price rule to product "Shoe" named "customer-discount"
    And the specific price rule is active
    And the specific price rule has a condition customers with customer "some-customer@something.com"
    Then the specific price rule should be valid for product "Shoe"

  Scenario: Add a new customer product specific price rule which is invalid
    Given the site has a customer "some-other-customer@something.com"
    Given adding a product specific price rule to product "Shoe" named "customer-discount"
    And the specific price rule is active
    And the specific price rule has a condition customers with customer "some-other-customer@something.com"
    Then the specific price rule should be invalid for product "Shoe"
