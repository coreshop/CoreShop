@tier_pricing
Feature: Adding a new Product with a tier price

  Background:
    Given the site operates on a store in "Austria"
    Given the site has a currency "Euro" with iso "EUR"
    Given I am in country "Austria"
    Given the site has a product "Shoe" priced at 10000
    Then the product "Shoe" should be priced at "10000"

  Scenario: Add a tier range with no conditions
    Given adding a product specific tier price rule to product "Shoe" named "default-tier"
    And the specific tier price rule is active
    And the specific tier price rule has a range from 0 to 10 with behaviour percentage-decrease of 10%
    Then the specific tier price rule should be valid for product "Shoe"
    And the product "Shoe" should be priced at "10000"
    Given I add the product "Shoe" to my cart
    And the cart total should be "9000" including tax
    And the cart total should be "9000" excluding tax
