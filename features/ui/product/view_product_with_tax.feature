@ui @product
Feature: Viewing product detail with tax

    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store
        And the site has a tax rate "AT" with "20%" rate
        And the site has a tax rule group "AT"
        And the tax rule group has a tax rule for country "Austria" with tax rate "AT"

    Scenario: View product price with tax
        Given the site has a product "T-Shirt" priced at 10000
        And the product has the tax rule group "AT"
        And the product is active and published and available for store "Austria"
        When I open the product's detail page
        Then I should see the price "€120.00"
        And I should see "incl. 20% Tax" tax-rate
        And I should see "(€20.00)" tax

