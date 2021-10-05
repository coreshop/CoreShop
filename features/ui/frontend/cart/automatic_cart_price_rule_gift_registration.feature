@ui @cart
Feature: Applying an automatic cart-price-rule with
         a gift product and fill out the registration form

    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store
        And the site has a product "TShirt" priced at 10000
        And the product is active and published and available for store "Austria"
        And adding a cart price rule named "Easter Madness"
        And the cart rule is active
        And the cart rule is not a voucher rule
        And the cart rule has a action gift-product with product "TShirt"

    Scenario: Applying an automatic cart rule with fixed discount to the cart
        When I want to register a new account
        And I specify the first name as "Bill"
        And I specify the last name as "Gates"
        And I specify the email as "bill@gates.com"
        And I confirm this email
        And I specify the password as "gates"
        And I specify the address first name as "Bill"
        And I specify the address last name as "Gates"
        And I specify the address street as "Gates-Street"
        And I specify the address number as "2"
        And I specify the address country as country "Austria"
        And I specify the address post code as "5020"
        And I specify the address city as "Salzburg"
        And I confirm this password
        And I accept the terms of service
        And I register this account
        But I should be logged in
