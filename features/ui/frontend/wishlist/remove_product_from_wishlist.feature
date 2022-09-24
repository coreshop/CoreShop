@ui @wishlist
Feature: Removing cart item from wishlist

    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store
        And the site has a product "TShirt" priced at 10000
        And the product is active and published and available for store "Austria"
        And I add this product to the wishlist

    Scenario: Removing wishlist item
        When I see the summary of my wishlist
        And I remove product "TShirt" from the wishlist
        Then my wishlist should be empty
