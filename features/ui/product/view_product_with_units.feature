@ui @product
Feature: Viewing product details with units

    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store
        Given the site has a product-unit "Pieces"
        And the site has a product-unit "Carton"
        And the site has a product-unit "Palette"

    Scenario: View product price with a units
        Given the site has a product "T-Shirt" priced at 10000
        And the product is active and published and available for store "Austria"
        And the product has the default unit "Pieces"
        And the product has an additional unit "Carton" with conversion rate "24" and price 200000
        And the product has an additional unit "Palette" with conversion rate "200" and price 1500000
        When I open the product's detail page
        Then I should see the price "€100.00"
        Then I should see the price "€2,000.00" for unit "carton"
        Then I should see the price "€15,000.00" for unit "palette"

