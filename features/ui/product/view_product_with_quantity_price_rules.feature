@ui @product
Feature: Viewing product details with quantity price rules

    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store

    Scenario: View product with quantity price rule
        Given the site has a product "T-Shirt" priced at 10000
        And the product is active and published and available for store "Austria"
        And adding a quantity price rule to this product named "default-product-quantity-price-rule" with calculation-behaviour "volume"
        And the quantity price rule is active
        And the quantity price rule has a range starting from 5 with behaviour percentage-decrease of 10%
        When I open the product's detail page
        Then I should see the price "€100.00"
        And I should see one quantity price rule with price "€90.00"
        And I should see the quantity price rule 1 starting from "5"

    Scenario: View product with quantity price rules having multiple ranges
        Given the site has a product "T-Shirt" priced at 10000
        And the product is active and published and available for store "Austria"
        And adding a quantity price rule to this product named "default-product-quantity-price-rule" with calculation-behaviour "volume"
        And the quantity price rule is active
        And the quantity price rule has a range starting from 5 with behaviour percentage-decrease of 10%
        And the quantity price rule has a range starting from 10 with behaviour percentage-decrease of 20%
        When I open the product's detail page
        Then I should see the price "€100.00"
        And I should see the quantity price rule 1 starting from "5"
        And I should see the quantity price rule 2 starting from "10"
        And I should see the quantity price rule 2 with price "€80.00"
