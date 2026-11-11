@domain @product
Feature: Having the ability to inherit store values

  Background:
    Given I enable pimcore inheritance
    And I enable inheritance for class "CoreShopProduct"
    And I enable variants for class "CoreShopProduct"
    And the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has a country "Germany" with currency "EUR"
    And the site has a store "Germany" with country "Germany" and currency "EUR"
    And the site has a product "Shoe" priced at 100
    And the products price is 100 for store "Austria"
    And the product has a variant "Shoe Green"

  Scenario: Product should have different price
    Given the products price is 200 for store "Germany"
    And the variants price is 250 for store "Germany"
    And the variants price is 150 for store "Austria"
    Then the product should be priced at "100"
    And the variant should be priced at "150"
    Then I am in store "Germany"
    And the product should be priced at "200"
    And the variant should be priced at "250"

  Scenario: Product inherits the variant price
    Given the products price is 200 for store "Germany"
    And the product should be priced at "100"
    And the variant should be priced at "100"
    Then I am in store "Germany"
    And the product should be priced at "200"
    And the variant should be priced at "200"

  Scenario: Changing the inherited store values of a variant keeps the price of the product
    Given the variants inherited price is changed to 250 for store "Austria"
    And the product is reloaded from the database
    And the variant is reloaded from the database
    Then the product should be priced at "100"
    And the variant should be priced at "250"

  Scenario: Changing the inherited store values of a variant keeps the unit prices of both products
    Given the site has a product-unit "Pieces"
    And the site has a product-unit "Carton"
    And the products price is 200 for store "Germany"
    And the product has the default unit "Pieces"
    And the product has an additional unit "Carton" with conversion rate "24" and price 2000
    And the variants inherited price is changed to 250 for store "Germany"
    And the product is reloaded from the database
    And the variant is reloaded from the database
    Then I am in store "Germany"
    And the product should be priced at "200"
    And the variant should be priced at "250"
    And the product should have a unit price of "2000" for unit "Carton" in store "Germany"
    And the variant should have a unit price of "2000" for unit "Carton" in store "Germany"
