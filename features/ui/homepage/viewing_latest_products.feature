@ui @homepage
Feature: Viewing a latest product list
    In order to be up-to-date with the newest products
    As a Visitor
    I want to be able to view a latest product list

    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store
        And the site has a tax rate "AT" with "20%" rate
        And the site has a tax rule group "AT"
        And the tax rule group has a tax rule for country "Austria" with tax rate "AT"
        And the site has a product "Shoes" priced at 1000
        And the product is active and published and available for store "Austria"
        And the product has the tax rule group "AT"
        And the site has a product "T-Shirt" priced at 2000
        And the product is active and published and available for store "Austria"
        And the product has the tax rule group "AT"
        And the site has a product "Shorts" priced at 3000
        And the product is active and published and available for store "Austria"
        And the product has the tax rule group "AT"

    Scenario: Viewing latest products
        When I check latest products
        Then I should see 3 products in the list

