@ui @cart @wip
Feature: Adding a product of given quantity to the cart

    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store
        And the site has a product "TShirt" priced at 10000
        And the product is active and published and available for store "Austria"

    Scenario: Adding product with quantity price rule of type percentage-decrease in quantity 1 to cart
        Given adding a quantity price rule to this product named "default-product-quantity-price-rule" with calculation-behaviour "volume"
        And the quantity price rule is active
        And the quantity price rule has a range starting from 5 with behaviour percentage-decrease of 10%
        When I add this product to the cart
        Then I should be on the cart summary page
        And I should be notified that the product has been successfully added
        And I should see "TShirt" with unit price "€100.00" in my cart

    Scenario: Adding product with quantity price rule of type percentage-decrease in quantity 5 to cart
        Given adding a quantity price rule to this product named "default-product-quantity-price-rule" with calculation-behaviour "volume"
        And the quantity price rule is active
        And the quantity price rule has a range starting from 5 with behaviour percentage-decrease of 10%
        When I add 5 of this product to the cart
        Then I should be on the cart summary page
        And I should be notified that the product has been successfully added
        And I should see "TShirt" with unit price "€90.00" in my cart
        And I should see "TShirt" with total price "€450.00" in my cart

    Scenario: Adding product with quantity price rule of type amount-decrease in quantity 1 to cart
        Given adding a quantity price rule to this product named "default-product-quantity-price-rule" with calculation-behaviour "volume"
        And the quantity price rule is active
        And the quantity price rule has a range starting from 5 with behaviour amount-decrease of 2000 in currency "EUR"
        When I add this product to the cart
        Then I should be on the cart summary page
        And I should be notified that the product has been successfully added
        And I should see "TShirt" with unit price "€100.00" in my cart

    Scenario: Adding product with quantity price rule of type percentage-decrease in quantity 5 to cart
        Given adding a quantity price rule to this product named "default-product-quantity-price-rule" with calculation-behaviour "volume"
        And the quantity price rule is active
        And the quantity price rule has a range starting from 5 with behaviour amount-decrease of 2000 in currency "EUR"
        When I add 5 of this product to the cart
        Then I should be on the cart summary page
        And I should be notified that the product has been successfully added
        And I should see "TShirt" with unit price "€80.00" in my cart
        And I should see "TShirt" with total price "€400.00" in my cart

    Scenario: Adding product with quantity price rule of type fixed price in quantity 1 to cart
        Given adding a quantity price rule to this product named "default-product-quantity-price-rule" with calculation-behaviour "volume"
        And the quantity price rule is active
        And the quantity price rule has a range starting from 5 with behaviour fixed of 7000 in currency "EUR"
        When I add this product to the cart
        Then I should be on the cart summary page
        And I should be notified that the product has been successfully added
        And I should see "TShirt" with unit price "€100.00" in my cart

    Scenario: Adding product with quantity price rule of type fixed price in quantity 5 to cart
        Given adding a quantity price rule to this product named "default-product-quantity-price-rule" with calculation-behaviour "volume"
        And the quantity price rule is active
        And the quantity price rule has a range starting from 5 with behaviour fixed of 7000 in currency "EUR"
        When I add 5 of this product to the cart
        Then I should be on the cart summary page
        And I should be notified that the product has been successfully added
        And I should see "TShirt" with unit price "€70.00" in my cart
        And I should see "TShirt" with total price "€350.00" in my cart
