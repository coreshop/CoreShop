@ui @product
Feature: Viewing a product details using it's link

    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store

    Scenario: View product via link
        Given the site has a product "T-Shirt"
        And the product is active and published and available for store "Austria"
        When I open the page "en/t-shirt" for this product
        Then I should be on the product's detail page
        Then I should see the product name "T-Shirt"

    Scenario: View product via link and the same name
        Given the site has a product "T-Shirt" with key "tshirt-1"
        And the product is active and published and available for store "Austria"
        And the site has another product "T-Shirt" with key "tshirt-2"
        And the product is active and published and available for store "Austria"
        When I open the page "en/t-shirt" for this product
        Then I should be on the detail page for product with key "tshirt-1"
        Then I should see the product name "T-Shirt"
        When I open the page "en/t-shirt-1" for this product
        Then I should be on the detail page for product with key "tshirt-2"
        Then I should see the product name "T-Shirt"

