@domain @product
Feature: Adding a new Product
  In order to extend my catalog
  the catalog has a specific-price-rule for a guest
  I want to create a new product

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has a product "Shoe" priced at 100
    And adding a product specific price rule to product "Shoe" named "guest"
    And the specific price rule is active
    And the specific price rule has a condition guest
    And the site has a customer "some-customer@something.com"
    And the site has a guest "some-guest@something.com"

  Scenario: Add a new guest product price rule for a guest customer which is valid
    Given I am guest "some-guest@something.com"
    Then the specific price rule "guest" for product "Shoe" should be valid

  Scenario: Add a new guest product price rule for a cart without a customer which is valid
    Then the specific price rule "guest" for product "Shoe" should be valid

  Scenario: Add a new guest product price rule for a customer which is invalid
    Given I am customer "some-customer@something.com"
    Then the specific price rule "guest" for product "Shoe" should be invalid