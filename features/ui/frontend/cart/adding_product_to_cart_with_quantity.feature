@ui @cart
Feature: Adding a product of given quantity to the cart

    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store
        And the site has a product "TShirt" priced at 10000
        And the product is active and published and available for store "Austria"

    Scenario: Adding a simple product to the cart
        When I add 5 of this product to the cart
        Then I should be on the cart summary page
        And I should be notified that the product has been successfully added
        And I should see "TShirt" with quantity 5 in my cart
