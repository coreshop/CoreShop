@ui @product
Feature: Viewing a product details using it's link

    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store

    Scenario: View product via link
        Given the site has a product "T-Shirt"
        And the product is active and published and available for store "Austria"
        When I open the page "en/shop/t-shirt~p%id%" for this product
        Then I should be on the product's detail page
        Then I should see the product name "T-Shirt"

