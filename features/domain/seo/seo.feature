@seo @domain
Feature: Adding a new Product with SEO information
  In order to make my catalog more findable
  I want to create a new product and add SEO Informations

  Scenario: Create a new product with a name
    Given the site has a product "Shoe"
    Then the product should have meta title "Shoe"

  Scenario: Create a new product with a description
    Given the site has a product "Shoe"
    And the products short description is "It is a simple Shoe"
    Then the product should have meta description "It is a simple Shoe"

  Scenario: Create a new product with a name and a meta title
    Given the site has a product "Shoe"
    And the products meta title is "Blue Shoe"
    Then the product should have meta title "Blue Shoe"

  Scenario: Create a new product with a description and a meta description
    Given the site has a product "Shoe"
    And the products short description is "It is a simple Shoe"
    And the products meta description is "A nice new Blue Shoe"
    Then the product should have meta description "A nice new Blue Shoe"


