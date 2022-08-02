@ui @cart
Feature: Adding a product to the cart with different store prices

    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store
        And the site has a currency "Sterling" with iso "GBP"
        And the site has a country "Great Britain" with currency "GBP"
        And the currency "GBP" is valid for store "Austria"
        And the site has a store "Great Britain" with country "Great Britain" and currency "GBP"
        And the site has a product "Mug" priced at 10000 for store "Austria"
        And the product is active and published and available
        And the product is priced at 8999 for store "Great Britain"

    Scenario: Adding a product to the cart in the default store
        Given I change my current store to "Austria"
        When I add product "Mug" to the cart
        Then I should see "Mug" with unit price "€100.00" in my cart

    Scenario: Adding a product with a different currency to the cart
        Given I change my current store to "Austria"
        And the currency "EUR" has a exchange-rate to currency "GBP" of "0.5"
        When I switch to currency "GBP"
        When I add product "Mug" to the cart
        Then I should see "Mug" with unit price "£50.00" in my cart

    Scenario: Adding a product to the cart in the other store
        Given I change my current store to "Great Britain"
        When I add product "Mug" to the cart
        Then I should see "Mug" with unit price "£89.99" in my cart
