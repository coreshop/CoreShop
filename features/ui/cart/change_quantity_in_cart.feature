@ui @cart
Feature: Changing quantity of a product in cart

    Background:
         Given the site operates on a store in "Austria"
        And the store "Austria" is the default store
        And the site has a product "TShirt" priced at 10000
        And the product is active and published and available for store "Austria"
        When I add this product to the cart

    Scenario: Increasing quantity of an item in cart
        Given I see the summary of my cart
        When I change "TShirt" quantity to 2
        Then I should see "TShirt" with quantity 2 in my cart

    Scenario: Increasing quantity of an item in cart beyond the threshold
        Given I see the summary of my cart
        When I change "TShirt" quantity to 20000
        Then I should see "TShirt" with quantity 20000 in my cart
