@ui @cart
Feature: Getting an cart-price-rule with a voucher code

    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store
        And the site has a product "TShirt" priced at 10000
        And the product is active and published and available for store "Austria"
        And adding a cart price rule named "Combinable"
        And the cart rule has a action discount with 50 in currency "EUR" off applied on total
        And the cart rule is active
        And the cart rule is a voucher rule with code "COMBINABLE"
        And adding a cart price rule named "Not Combinable"
        And the cart rule has a action discount with 50 in currency "EUR" off applied on total
        And the cart rule is active
        And the cart rule is a voucher rule with code "NOT_COMBINABLE"
        And adding a product price rule named "not-combinable-with-cart-price-rule"
        And the price rule has a action discount-percent with 10% discount
        And the price rule has a condition not combinable with cart rule "Not Combinable"
        And the price rule is active

    Scenario: Applying a voucher rule where the product price rule is combinable
        Given I add this product to the cart
        And I apply the voucher code "COMBINABLE"
        Then I should be notified that the voucher has been applied
        And I should see "TShirt" with unit price "€90.00" in my cart
        And my cart's total should be "€40.00"

    Scenario: Applying a voucher rule where the product price rule is not combinable
        Given I add this product to the cart
        And I apply the voucher code "NOT_COMBINABLE"
        Then I should be notified that the voucher has been applied
        And I should see "TShirt" with unit price "€100.00" in my cart
        And my cart's total should be "€50.00"

