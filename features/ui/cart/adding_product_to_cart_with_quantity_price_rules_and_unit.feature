@ui @cart @wip
Feature: Adding a product with a quantity price rule and units

    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store
        And the site has a product-unit "Pieces"
        And the site has a product-unit "Carton"
        And the site has a product-unit "Palette"
        And the site has a product "TShirt" priced at 10000
        And the product is active and published and available for store "Austria"
        And the product has the default unit "Pieces"
        And the product has an additional unit "Carton" with conversion rate "24" and price 200000
        And the product has an additional unit "Palette" with conversion rate "200" and price 1500000
        And adding a quantity price rule to this product named "default-product-quantity-price-rule" with calculation-behaviour "volume"
        And the quantity price rule is active
        And the quantity price rule has a range starting from 5 with behaviour percentage-decrease of 10%
        And the price range is only valid for unit "Pieces"
        And the quantity price rule has a range starting from 10 with behaviour percentage-decrease of 20%
        And the price range is only valid for unit "Pieces"
        And the quantity price rule has a range starting from 20 with behaviour percentage-decrease of 10%
        And the price range is only valid for unit "Carton"
        And the quantity price rule has a range starting from 40 with behaviour percentage-decrease of 20%
        And the price range is only valid for unit "Carton"
        And the quantity price rule has a range starting from 100 with behaviour percentage-decrease of 10%
        And the price range is only valid for unit "Palette"
        And the quantity price rule has a range starting from 200 with behaviour percentage-decrease of 20%
        And the price range is only valid for unit "Palette"

    Scenario: Adding product with quantity price rule and unit
        Given the quantity price rule has a range starting from 5 with behaviour percentage-decrease of 10%
        And the price range is only valid for unit "Pieces"
        When I add this product in unit "Pieces" to the cart
        Then I should be on the cart summary page
        And I should be notified that the product has been successfully added
        And I should see product "TShirt" with unit "Pieces" in my cart
        And I should see product "TShirt" in unit "Pieces" with unit price "€100.00" in my cart

    Scenario: Adding product with quantity price rule and unit in quantity 5
        When I add 5 of this product in unit "Carton" to the cart
        Then I should be on the cart summary page
        And I should be notified that the product has been successfully added
        And I should see product "TShirt" in unit "Carton" with unit price "€2,000.00" in my cart
        And I should see product "TShirt" in unit "Carton" with total price "€10,000.00" in my cart

    Scenario: Adding product with quantity price rule and unit in quantity 20
        When I add 20 of this product in unit "Carton" to the cart
        Then I should be on the cart summary page
        And I should be notified that the product has been successfully added
        And I should see product "TShirt" in unit "Carton" with unit price "€1,800.00" in my cart
        And I should see product "TShirt" in unit "Carton" with total price "€36,000.00" in my cart
