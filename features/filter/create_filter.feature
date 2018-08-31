@filter
Feature: Adding a filter for an index
  In order to make my catalog searchable
  I want to create a new index with an filter and add conditions

  Background:
    Given the site operates on a store in "Austria"
    And the site has a product "Shoe" priced at 100

  Scenario: Create a new filter
    Given the site has a index "myindex" for class "CoreShopProduct" with type "mysql"
    And the site has a filter "myfilter" for index "myindex"
    Then there should be a filter with name "myfilter"
