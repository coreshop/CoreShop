@domain @product
Feature: Adding a new product with a simple quantity price rule

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has a product "Shoe" priced at 10000
    And the site has a product-unit "Pieces"
    And the site has a product-unit "Carton"
    And the site has a product-unit "Palette"
    And the product has the default unit "Pieces"
    And the product has an additional unit "Carton" with conversion rate "24"
    And the product has an additional unit "Palette" with conversion rate "200"

  Scenario: Add a quantity price rule with no conditions
    Given adding a quantity price rule to product "Shoe" named "default-product-quantity-price-rule" and with calculation-behaviour "volume"
    And the quantity price rule is active
    And the quantity price rule has a range starting from 0 with behaviour percentage-decrease of 10% for unit "Pieces"
    And the quantity price rule has a range starting from 0 with behaviour percentage-decrease of 10% for unit "Carton"
    And the quantity price rule has a range starting from 0 with behaviour percentage-decrease of 10% for unit "Palette"
    And the product has a variant "Shoe Red"
    And I copy the products unit-definitions and quantity-price-rules to all variants
