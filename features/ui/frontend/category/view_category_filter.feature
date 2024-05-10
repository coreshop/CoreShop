@ui @category
Feature: Filter and Indexes

  Background:
    Given the site operates on a store in "Austria"
    And the site has a configuration
    And the store "Austria" is the default store
    And the site has two categories "Shoes" and "Coats"
    And the site has a category "Sneakers"
    And the site has a product "Shoe" priced at 12000
    And the product is active and published and available for store "Austria"
    And it is in category "Shoes"
    And the site has a product "Shoe 2" priced at 15000
    And the product is active and published and available for store "Austria"
    And it is in category "Shoes"
    And the site has a product "Jacket" priced at 40000
    And the product is active and published and available for store "Austria"
    And it is in category "Coats"
    And the site has a product "Rain Coat" priced at 25000
    And the product is active and published and available for store "Austria"
    And it is in category "Coats"
    And the site has a product "Winter Coat" priced at 73000
    And the product is active and published and available for store "Austria"
    And it is in category "Coats"
    And the site has a product "Sneaker" priced at 350000
    And the product is active and published and available for store "Austria"
    And it is in category "Sneakers"



  Scenario: latest products should still be
    When I check latest products
    Then I should see 6 products in the list

  Scenario: Filter with category list
    Given the site has a index "myindex" for class "CoreShopProduct" with type "mysql"
    And  the index has following fields:
      | key   | name            | type            | getter          | interpreter   | columnType   |
      | sku   | sku             | object          |                 |               | STRING       |
      | ean   | ean             | object          |                 |               | STRING       |
      | name  | internalName    | localizedfields | localizedfield  | localeMapping | STRING       |
    And the site has a filter "myfilter" for index "myindex"
    And the filter has a condition with label "Topseller" and type "select" and a preselect for "Jacket"
    And the filter gets added to category "Coats"
    And I generate index "myindex" for products
    When I switch to category "Coats" on left menu
    Then I should see 1 products in the category list
    And I should see a filter with label "Topseller"
    And I select filter option "Rain Coat"
    Then I should see 1 products in the category list

  Scenario: Filter with search field
    Given the site has a index "myindex" for class "CoreShopProduct" with type "mysql"
    And  the index has following fields:
      | key   | name            | type            | getter          | interpreter   | columnType   |
      | sku   | sku             | object          |                 |               | STRING       |
      | ean   | ean             | object          |                 |               | STRING       |
      | name  | internalName    | localizedfields | localizedfield  | localeMapping | STRING       |
    And the site has a filter "myfilter_search" for index "myindex"
    And the filter has a condition with label "Search" and type "search" on field "internalName"
    And the filter gets added to category "Coats"
    And I generate index "myindex" for products
    When I switch to category "Coats" on main menu
    And I type in search field "coat"
    Then I should see 2 products in the category list
