@product_quantity_price_rules
Feature: Adding a new product with a simple quantity price rule

  Background:
    Given the site operates on a store in "Austria"
    Given the site has a currency "Euro" with iso "EUR"
    Given I am in country "Austria"
    Given the site has a product "Shoe" priced at 10000
    Then the product "Shoe" should be priced at "10000"

  Scenario: Add a quantity price rule with no conditions
    Given adding a quantity price rule to product "Shoe" named "default-product-quantity-price-rule"
    And the quantity price rule is active
    And the quantity price rule has a range from 0 to 10 with behaviour percentage-decrease of 10%
    Then the quantity price rule should be valid for product "Shoe"
    And the product "Shoe" should be priced at "10000"
    Given I add the product "Shoe" to my cart
    And the cart total should be "9000" including tax
    And the cart total should be "9000" excluding tax
