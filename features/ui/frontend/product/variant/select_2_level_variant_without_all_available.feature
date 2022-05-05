@ui @product
Feature: Viewing a product variant details

    Background:
        Given the site operates on a store in "Austria"
        And the store "Austria" is the default store
        And the site has a attribute group "Color"
        And the site has a color attribute "red" with hex code "#FF0000" in attribute group
        And the site has a color attribute "blue" with hex code "#0000FF" in attribute group
        And the site has a attribute group "Size"
        And the site has a value attribute "S" in attribute group
        And the site has a value attribute "M" in attribute group
        And the site has a product "T-Shirt"
        And the product is active and published and available for store "Austria"
        And the product is allowed attribute group "Color"
        And the product is allowed attribute group "Size"
        And the product has a variant "blue-s"
        And the variant uses attribute color "blue" and attribute value "s"
        And the variant is published
        And the product has a variant "red-s"
        And the variant uses attribute color "red" and attribute value "s"
        And the variant is published
        And the product has a variant "red-m"
        And the variant uses attribute color "red" and attribute value "m"
        And the variant is published
        When I open the variant's detail page
        Then I should see the product name "red-m"

    Scenario: View product and select other variant
        When I click on attribute color "blue"
        Then attribute value "M" is disabled
        Then attribute value "S" is enabled
        And attribute color "red" is enabled
