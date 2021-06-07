@domain @index
Feature: Adding a object index
  In order to make my catalog searchable
  I want to create a new index and add products

  Background:
    Given the site operates on a store in "Austria"
    And the site has a index "myindex" for class "CoreShopProduct" with type "mysql"
    And  the index has following fields:
      | key   | name            | type            | getter          | interpreter   | columnType   |
      | sku   | sku             | object          |                 |               | STRING       |
      | ean   | ean             | object          |                 |               | STRING       |
      | name  | internalName    | localizedfields | localizedfield  | localeMapping | STRING       |

  Scenario: Adding a simple product to the index
    And the site has a product "Shoe" priced at 100
    And the products ean is "123456"
    And the products sku is "654321"
    And the product is active
    And the product is published
    Then the index should have indexed the product "Shoe"
    And the index column "ean" for product "Shoe" should have value "123456"
    And the index column "sku" for product "Shoe" should have value "654321"
    And the index localized column "internalName" for product "Shoe" should have value "Shoe"

  Scenario: Adding multiple products to the index
    And the site has a product "Shoe" priced at 100
    And the products ean is "123456"
    And the products sku is "654321"
    And the product is active
    And the product is published
    And the site has a product "Shoe 2" priced at 100
    And the products ean is "987654"
    And the products sku is "456789"
    And the product is active
    And the product is published
    Then the index should have indexed the product "Shoe"
    And the index should have indexed the product "Shoe 2"
    And the index column "ean" for product "Shoe 2" should have value "987654"
    And the index column "sku" for product "Shoe 2" should have value "456789"
    And the index localized column "internalName" for product "Shoe 2" should have value "Shoe 2"
