@ui @cart
Feature: Loosing cart after log out

    Background:
         Given the site operates on a store in "Austria"
        And the store "Austria" is the default store
        And the site has a product "TShirt" priced at 10000
        And the product is active and published and available for store "Austria"
        And I am a logged in customer
        When I add this product to the cart

    Scenario: Log out
        When I log out
        And I see the summary of my cart
        Then my cart should be empty
