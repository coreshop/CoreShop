@product @domain
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
