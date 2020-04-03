@domain @cart
Feature: In Order to not allow
  customers to buy products that are unpublished
  CoreShop deletes those cart-items and
  notifies the customer

  Background:
    Given the site operates on a store in "Austria"
    And the site has a country "Germany" with currency "EUR"
    And the country "Germany" is active
    And the site has a tax rate "AT" with "20%" rate
    And the site has a tax rule group "AT"
    And the tax rule group has a tax rule for country "Austria" with tax rate "AT"
    And the site has a product "T-Shirt" priced at 2000
    And the product is published
    And the site has a product "T-Shirt Blue" priced at 2000
    And the product is published
    And the product has the tax rule group "AT"

  Scenario: Create a new cart and add a product and unpublish the product
    Given I add the product "T-Shirt" to my cart
    Given I add the product "T-Shirt Blue" to my cart
    Then there should be two products in my cart
    Given the product "T-Shirt" is not published
    And I refresh my cart
    Then there should be one product in my cart

  Scenario: Create a new cart and add a product and publish the product
    Given I add the product "T-Shirt" to my cart
    Then there should be one product in my cart
    Given the product "T-Shirt" is published
    Then there should be one product in my cart
