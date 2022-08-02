@ui @product @wip
Feature: Viewing a product variant details

    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store
        And the site has a attribute group "Color"
        And the site has a color attribute "red" with hex code "#FF0000" in attribute group
        And the site has a color attribute "blue" with hex code "#0000FF" in attribute group
        And the site has a color attribute "green" with hex code "#00FF00" in attribute group
        And the site has a attribute group "Size"
        And the site has a value attribute "S" in attribute group
        And the site has a value attribute "M" in attribute group
        And the site has a value attribute "L" in attribute group
        And the site has a value attribute "XL" in attribute group
        And the site has a product "T-Shirt"
        And the product is active and published and available for store "Austria"
        And the product has variants for all values of attribute group "Color" and attribute group "Size"
        When I open the variant's detail page
        Then I should see the product name "T-Shirt red XL"

    Scenario: View product and select other variant
        When I click on attribute color "green"
        And attribute value "S" is not selected
        And attribute value "M" is not selected
        And attribute value "L" is not selected
        And attribute value "XL" is not selected
        When I click on attribute value "S"
        Then I should be on the detail page for variant with key "t-shirt-green-s"