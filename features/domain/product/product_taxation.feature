@product @domain
Feature: Adding a new Product
  In order to extend my catalog
  I want to create a new product
  and give it a tax rule

   Background:
    Given the site operates on a store in "Austria"
    And the site has a tax rate "AT" with "20%" rate
    And the site has a tax rule group "AT"
    And the tax rule group has a tax rule for country "Austria" with tax rate "AT"
    And the site has a product "T-Shirt" priced at 2000
    And the product has the tax rule group "AT"

  Scenario: Test product tax rule group
    Then the product "T-Shirt" should have tax rule group "AT"

  Scenario: Test product price with tax
    Then the product "T-Shirt" should be priced at "2000"
    And the product "T-Shirt" retail-price should be "2000"
    And the product "T-Shirt" should be priced at "2400" including tax
    And the product "T-Shirt" retail-price should be "2400" including tax
