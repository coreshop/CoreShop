@product @domain
Feature: In order that a customer can visit the product page
  The website needs to create a URL

  Background:
    Given the site operates on a store in "Austria"
    Given the site has a product "Shoe" priced at 100
    Then the product "Shoe" should be priced at "100"

  Scenario: Create URL for product
    Then the generated url for object should be "/en/shop/shoe~p%id"
