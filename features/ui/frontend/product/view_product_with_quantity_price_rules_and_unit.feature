@ui @product
Feature: Viewing product details with quantity price rules

    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store
        And the site has a product-unit "Pieces"
        And the site has a product-unit "Carton"
        And the site has a product-unit "Palette"
        And the site has a product "T-Shirt" priced at 10000
        And the product is active and published and available for store "Austria"
        And the product has the default unit "Pieces"
        And the product has an additional unit "Carton" with conversion rate "24" and price 200000
        And the product has an additional unit "Palette" with conversion rate "200" and price 1500000
        And adding a quantity price rule to this product named "default-product-quantity-price-rule" with calculation-behaviour "volume"
        And the quantity price rule is active

    Scenario: View product with quantity price rule
        Given the quantity price rule has a range starting from 5 with behaviour percentage-decrease of 10%
        And the price range is only valid for unit "Pieces"
        When I open the product's detail page
        Then I should see the price "€100.00"
        And I should see one quantity price rule with price "€90.00" for unit "Pieces"
        And I should see the quantity price rule 1 starting from "5" for unit "Pieces"

    Scenario: View product with quantity price rules having multiple ranges
        Given the quantity price rule has a range starting from 5 with behaviour percentage-decrease of 10%
        And the price range is only valid for unit "Pieces"
        And the quantity price rule has a range starting from 10 with behaviour percentage-decrease of 20%
        And the price range is only valid for unit "Pieces"
        When I open the product's detail page
        Then I should see the price "€100.00"
        And I should see the quantity price rule 1 starting from "5" for unit "Pieces"
        And I should see the quantity price rule 2 starting from "10" for unit "Pieces"
        And I should see the quantity price rule 2 with price "€80.00" for unit "Pieces"

    Scenario: View product with quantity price rules having multiple ranges for multiple units
        Given the quantity price rule has a range starting from 5 with behaviour percentage-decrease of 10%
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
        When I open the product's detail page
        Then I should see the price "€100.00"
        And I should see the quantity price rule 1 starting from "5" for unit "Pieces"
        And I should see the quantity price rule 2 starting from "10" for unit "Pieces"
        And I should see the quantity price rule 2 with price "€80.00" for unit "Pieces"
        And I should see the quantity price rule 1 starting from "20" for unit "Carton"
        And I should see the quantity price rule 2 starting from "40" for unit "Carton"
        And I should see the quantity price rule 2 with price "€1,600.00" for unit "Carton"
        And I should see the quantity price rule 1 starting from "100" for unit "Palette"
        And I should see the quantity price rule 2 starting from "200" for unit "Palette"
        And I should see the quantity price rule 2 with price "€12,000.00" for unit "Palette"
