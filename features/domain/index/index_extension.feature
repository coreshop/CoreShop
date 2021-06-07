@domain @index
Feature: Adding a object index
  In order to make my catalog searchable
  I want to create a new index with extensions

  Scenario: Create a new index and the extension should create fields
    Given the site has a index "myindex" for class "CoreShopProduct" with type "mysql"
    Then the index should have columns "categoryIds, parentCategoryIds, stores"
