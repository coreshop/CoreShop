@ui @cart @wip
Feature: Adding a product to cart with a unit

    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store
        And the site has a product "TShirt" priced at 10000
        And the product is active and published and available for store "Austria"
        And the site has a product-unit "Pieces"
        And the site has a product-unit "Carton"
        And the site has a product-unit "Palette"
        And the product has the default unit "Pieces"
        And the product has an additional unit "Carton" with conversion rate "24" and price 200000
        And the product has an additional unit "Palette" with conversion rate "200" and price 1800000

    Scenario: Adding product with one unit to the cart
        When I add this product in unit "Pieces" to the cart
        Then I should be on the cart summary page
        And I should be notified that the product has been successfully added
        And I should see product "TShirt" with unit "Pieces" in my cart
        And I should see product "TShirt" in unit "Pieces" with unit price "€100.00" in my cart

    Scenario: Adding products with two units to the cart
        When I add this product in unit "Carton" to the cart
        And I add this product in unit "Palette" to the cart
        Then I should see product "TShirt" with unit "Carton" in my cart
        And I should see product "TShirt" with unit "Palette" in my cart
        And I should see product "TShirt" in unit "Carton" with unit price "€2,000.00" in my cart
        And I should see product "TShirt" in unit "Palette" with unit price "€18,000.00" in my cart
