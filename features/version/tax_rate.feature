@version @version_tax_rate
Feature: Support Pimcore Versions

  Background:
    Given the site operates on a store in "Austria"
    And the site operates on locale "en"
    And the site has a tax rate "AT20" with "20%" rate
    And the site has a tax rule group "AT20"
    And the site has a tax rate "AT10" with "10%" rate
    And the site has a tax rule group "AT10"
    And the tax rule group has a tax rule for country "Austria" with tax rate "AT20"
    And the site has a product "T-Shirt" priced at 2000
    And the product has the tax rule group "AT20"

  Scenario: Change Tax Rate
    Given I remember the product Version
    And the product has the tax rule group "AT10"
    Then I restore the remembered product Version
    And the product should have tax rule group "AT20"
