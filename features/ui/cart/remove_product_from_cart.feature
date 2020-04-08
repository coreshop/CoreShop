@ui @cart
Feature: Removing cart item from cart
    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store
        And the site has a product "TShirt" priced at 10000
        And the product is active and published and available for store "Austria"
        And I add this product to the cart

    Scenario: Removing cart item
        When I see the summary of my cart
        And I remove product "TShirt" from the cart
        Then my cart should be empty
        And my cart's total should be "€0.00"

    Scenario: Checking cart's total after removing one item
        And the site has a product "Mug" priced at 9999
        And the product is active and published and available for store "Austria"
        And I add this product to the cart
        And I remove product "TShirt" from the cart
        Then my cart's total should be "€99.99"
