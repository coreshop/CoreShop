@product
Feature: Adding a new Product
    In order to extend my catalog
    I want to create a new product

    #Background:
    #    Given the store operates on a single channel in "United States"
    #    And the store has a "Snake" configurable product
    #    And this product has "Ouroboros", "Boomslang" and "Bimini" variants
    #    And I am logged in as an administrator

    Scenario: Create a new product
        Given the site operates on a store in "Austria"
        Given the site has a product "Shoe" priced at 100
        Then Product should be priced 100
