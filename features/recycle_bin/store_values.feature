@recycle_bin @recycle_bin_store_values
Feature: In order to support Pimcore Recycle Bin, we have to serialize Store Values and merge them back

  Background:
    Given the site operates on a store in "Austria"
    And the site has a country "Germany" with currency "EUR"
    And the site has a store "Germany" with country "Germany" and currency "EUR"
    And the site operates on locale "en"
    And the site has a product "Shoe" priced at 100
    And the products price is 100 for store "Austria"
    And the products price is 200 for store "Germany"

  Scenario: Change Prices
    Given I recycle the product
    Then the recycled product does not exist anymore
    Given I restore the recycled product

    Then I am in store "Austria"
    And the product should be priced at "100"

    Then I am in store "Germany"
    And the product should be priced at "200"
