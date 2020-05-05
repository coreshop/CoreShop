@ui @ui_precision @product
Feature: Viewing a product details

    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store

    Scenario: View product price
        Given the site has a product "T-Shirt" priced at 100123456
        And the product is active and published and available for store "Austria"
        When I open the product's detail page
        Then I should see the price "€100.12"


    Scenario: View product price with rounding
        Given the site has a product "T-Shirt" priced at 100556567
        And the product is active and published and available for store "Austria"
        When I open the product's detail page
        Then I should see the price "€100.56"

