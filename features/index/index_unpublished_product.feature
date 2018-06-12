@index @index_unpublished @index_unpublished_product
Feature: Adding a product object index
  In order to make my catalog searchable
  I want to create a new index
  But when the user unpublishes the object, the object should be removed from the index

  Background:
    Given the site operates on a store in "Austria"
    And the site has a index "test" for class "CoreShopProduct" with type "mysql"
    And the site has a product "Shoe" priced at 100
    And the product is active
    And the product is published

  Scenario: The product is enabled and published
    Then the index should have indexed the product "Shoe"

  Scenario: The product is enabled and unpublished
    Given the product is not published
    Then the index should not have indexed the product "Shoe"

  Scenario: The object instance is not active and published
    Given the product is not active
    Then the index should not have indexed the product "Shoe"

  Scenario: The object instance is disabled and unpublished
    Given the product is not active
    Given the product is not published
    Then the index should not have indexed the product "Shoe"