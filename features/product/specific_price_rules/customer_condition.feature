@product
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
    Then the product "Shoe" should be priced at 100
    Then I should be logged in with email "some-customer@something.com"

  Scenario: Add a new country product specific price rule which is valid
    Given adding a product specific price rule to product "Shoe" named "customer-discount"
    Given the specific price rule "customer-discount" is active
    Given the specific price rule "customer-discount" has a condition customers with customer "some-customer@something.com"
    Then the specific price rule "customer-discount" for product "Shoe" should be valid

  Scenario: Add a new country product specific price rule which is invalid
    Given adding a product specific price rule to product "Shoe" named "customer-discount"
    Given the specific price rule "customer-discount" is active
    Given the site has a customer "some-other-customer@something.com"
    Given the specific price rule "customer-discount" has a condition customers with customer "some-other-customer@something.com"
    Then the specific price rule "customer-discount" for product "Shoe" should be invalid