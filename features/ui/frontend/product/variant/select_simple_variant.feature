@ui @product @wip
Feature: Viewing a product variant details

    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store
        And the site has a attribute group "Color"
        And the site has a color attribute "red" with hex code "#FF0000" in attribute group
        And the site has a color attribute "blue" with hex code "#0000FF" in attribute group
        And the site has a color attribute "green" with hex code "#00FF00" in attribute group
        And the site has a product "T-Shirt"
        And the product is active and published and available for store "Austria"
        And the product is allowed attribute group "Color"
        And the product has a variant "red"
        And the variant uses attribute color "red"
        And the variant is published
        And the product has a variant "blue"
        And the variant uses attribute color "blue"
        And the variant is published
        And the product has a variant "green"
        And the variant uses attribute color "green"
        And the variant is published
        When I open the variant's detail page
        Then I should see the product name "green"

    Scenario: View product and select other variant
        When I click on attribute color "red"
        Then I should be on the detail page for variant with key "red"