@ui @product
Feature: Viewing a product variant details

    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store
        And the site has a attribute group "Color"
        And the site has a color attribute "red" with hex code "#FF0000" in attribute group with sorting 1
        And the site has a color attribute "blue" with hex code "#0000FF" in attribute group with sorting 2
        And the site has a color attribute "green" with hex code "#00FF00" in attribute group with sorting 3
        And the site has a attribute group "Size"
        And the site has a value attribute "S" in attribute group with sorting 1
        And the site has a value attribute "M" in attribute group with sorting 2
        And the site has a value attribute "L" in attribute group with sorting 3
        And the site has a value attribute "XL" in attribute group with sorting 4
        And the site has a attribute group "Season"
        And the site has a value attribute "Winter" in attribute group with sorting 1
        And the site has a value attribute "Summer" in attribute group with sorting 2
        And the site has a product "T-Shirt"
        And the product is active and published and available for store "Austria"
        And the product has variants for all values of attribute group "Color" and attribute group "Size" and attribute group "Season"
        When I open the variant's detail page
        Then I should see the product name "T-Shirt red XL Winter"

    Scenario: View product and select other variant
        When I click on attribute color "green"
        And attribute value "S" is not selected
        And attribute value "M" is not selected
        And attribute value "L" is not selected
        And attribute value "XL" is not selected
        And attribute value "Summer" is not selected
        And attribute value "Winter" is not selected
        When I click on attribute value "S"
        And attribute value "Summer" is not selected
        And attribute value "Winter" is not selected
        When I click on attribute value "Winter"
        Then I should be on the detail page for variant with key "t-shirt-green-s-winter"
        When I click on attribute color "blue"
        When I click on attribute value "XL"
        When I click on attribute value "Summer"
        Then I should be on the detail page for variant with key "t-shirt-blue-xl-summer"