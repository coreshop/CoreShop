@domain @product @variant
Feature: Adding a new Product with Variants

  Background:
    Given the site operates on a store in "Austria"
    And the site has a attribute group "Color"
    And the site has a color attribute "red" with hex code "#FF0000" in attribute group
    And the site has a color attribute "blue" with hex code "#0000FF" in attribute group
    And the site has a color attribute "green" with hex code "#00FF00" in attribute group

  Scenario: Create a new product with 3 variants
    Given the site has a product "Shoe" priced at 100
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
    Then the product should have 3 variants
