@domain @version
Feature: In order to support Pimcore Versioning, we have to serialize Store Values and merge them back

  Background:
    Given the site operates on a store in "Austria"
    And the site has a country "Germany" with currency "EUR"
    And the site has a store "Germany" with country "Germany" and currency "EUR"
    And the site operates on locale "en"
    And the site has a product "Shoe" priced at 100
    And the products price is 100 for store "Austria"
    And the products price is 200 for store "Germany"

  Scenario: Change Prices
    Given I remember the product Version
    And the products price is 1000 for store "Austria"
    And the products price is 2000 for store "Germany"
    Then the product should be priced at "1000"
    Then I am in store "Germany"
    And the product should be priced at "2000"
    Then I restore the remembered product Version

    Then I am in store "Austria"
    And the product should be priced at "100"

    Then I am in store "Germany"
    And the product should be priced at "200"

  Scenario: Restore Version, but don't save it
     Given I remember the product Version
     And the products price is 1000 for store "Austria"
     And the products price is 2000 for store "Germany"
     And I restore the remembered product Version

     Then I am in store "Austria"
     And the version should be priced at "1000"
     And the product should be priced at "100"

     Then I am in store "Germany"
     And the version should be priced at "2000"
     And the product should be priced at "200"
