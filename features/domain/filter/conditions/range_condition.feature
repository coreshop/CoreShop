@domain @filter
Feature: Adding a filter for an index
  In order to make my catalog searchable
  I want to create a filter with a range condition

  Background:
    Given the site operates on a store in "Austria"
    And the site has a index "myindex" for class "CoreShopProduct" with type "mysql"
    And  the index has following fields:
      | key    | name   | type   | getter | interpreter   | columnType   |
      | weight | weight | object |        |               | INTEGER      |
    And the site has a filter "myfilter" for index "myindex"
    And the filter has following conditions:
      | type  | label  | field  |
      | range | Weight | weight |

  Scenario: Create 3 products that will be filtered
    Given the site has a product "Shoe" priced at 100
    And the product weighs 10kg
    And the product is active
    And the product is published
    And the site has a product "Shoe 2" priced at 100
    And the product weighs 20kg
    And the product is active
    And the product is published
    And the site has a product "Shoe 3" priced at 100
    And the product weighs 30kg
    And the product is active
    And the product is published
    Then the filter should have 3 items with params:
        | key        | value  |
        | weight-min | 10     |
        | weight-max | 50     |

  Scenario: Create 3 products that will be filtered for a result of 1
    Given the site has a product "Shoe" priced at 100
    And the product weighs 10kg
    And the product is active
    And the product is published
    And the site has a product "Shoe 2" priced at 100
    And the product weighs 20kg
    And the product is active
    And the product is published
    And the site has a product "Shoe 3" priced at 100
    And the product weighs 30kg
    And the product is active
    And the product is published
    Then the filter should have 1 item with params:
        | key        | value  |
        | weight-min | 10     |
        | weight-max | 19     |

  Scenario: Create 3 products that will be filtered for a result of 2
    Given the site has a product "Shoe" priced at 100
    And the product weighs 10kg
    And the product is active
    And the product is published
    And the site has a product "Shoe 2" priced at 100
    And the product weighs 20kg
    And the product is active
    And the product is published
    And the site has a product "Shoe 3" priced at 100
    And the product weighs 30kg
    And the product is active
    And the product is published
    Then the filter should have 2 items with params:
        | key        | value  |
        | weight-min | 10     |
        | weight-max | 20     |
