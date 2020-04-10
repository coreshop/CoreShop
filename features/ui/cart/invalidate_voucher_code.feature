@ui @cart @wip
Feature: Applying a valid voucher that gets invalidated after the cart changes

    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store
        And the site has a product "TShirt" priced at 10000
        And the product is active and published and available for store "Austria"
        And adding a cart price rule named "Easter Madness"
        And the cart rule is active
        And the cart rule is a voucher rule with code "EASTER"
        And the cart rule has a action discount with 20 in currency "EUR" off applied on total
        And the cart rule has a condition amount with value "1" to "100"

    Scenario: Invalidate Price Rule by adding another Product
        When I add this product to the cart
        And I apply the voucher code "EASTER"
        Then I should be notified that the voucher has been applied
        And I should see "TShirt" with unit price "€100.00" in my cart
        And my cart's total should be "€80.00"
        Given I add this product to the cart
        And my cart's total should be "€200.00"

    Scenario: Invalidate Price Rule by changing the quantity
        When I add this product to the cart
        And I apply the voucher code "EASTER"
        Then I should be notified that the voucher has been applied
        And I should see "TShirt" with unit price "€100.00" in my cart
        And my cart's total should be "€80.00"
        When I change "TShirt" quantity to 2
        Then my cart's total should be "€200.00"
