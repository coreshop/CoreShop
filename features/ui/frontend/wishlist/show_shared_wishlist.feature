@ui @wishlist
Feature: Adding a product to the wishlist
    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store
        And the site has a product "TShirt" priced at 10000
        And the product is active and published and available for store "Austria"
        When I add this product to the wishlist
        Then I should be on the wishlist summary page

    Scenario: Show shared Wishlist
        Given I visit the share wishlist link
        Then this wishlist item should have name "TShirt"