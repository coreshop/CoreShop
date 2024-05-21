@domain @product
Feature: Adding a new Product
  In order to extend my catalog
  the catalog has a price-rule for a guest
  I want to create a new product

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has a product "Shoe" priced at 100
    And adding a product price rule named "guest"
    And the price rule is active
    And the price rule has a condition guest
    And the site has a customer "some-customer@something.com"
    And the site has a guest "some-guest@something.com"

  Scenario: Add a new guest product price rule for a guest customer which is valid
    Given I am guest "some-guest@something.com"
    Then the price rule "guest" for product "Shoe" should be valid

  Scenario: Add a new guest product price rule for a cart without a customer which is valid
    Then the price rule "guest" for product "Shoe" should be valid

  Scenario: Add a new guest product price rule for a customer which is invalid
    Given I am customer "some-customer@something.com"
    Then the price rule "guest" for product "Shoe" should be invalid