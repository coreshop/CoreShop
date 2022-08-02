@ui @product
Feature: Viewing product details with a discount

    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store

    Scenario: View product price with discount
        Given the site has a product "T-Shirt" priced at 10000
        And the product is active and published and available for store "Austria"
        And adding a product specific price rule to this product named "Special Discount"
        And the specific price rule is active
        And the specific price rule has a action discount with 10 in currency "EUR" off
        When I open the product's detail page
        Then I should see the price "€90.00"
        And I should see the original price "€100.00"
        And I should see the discount of "(-€10.00)"

