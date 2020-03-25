@order @domain
Feature: Create a new order

  Background:
    Given the site operates on a store in "Austria"
    And the site operates on locale "en"
    And the site has a tax rate "AT" with "20%" rate
    And the site has a tax rule group "AT"
    And the tax rule group has a tax rule for country "Austria" with tax rate "AT"
    And the site has a product "T-Shirt" priced at 2000
    And the product has the tax rule group "AT"
    And the site has a customer "some-customer@something.com"
    And the customer "some-customer@something.com" has an address with country "Austria", "4600", "Wels", "Freiung", "9-11/N3"

    And the cart belongs to customer "some-customer@something.com"

  Scenario: Create a new order and delete the customer
    Given I add the product "T-Shirt" to my cart
    And the cart ships to customer "some-customer@something.com" address with postcode "4600"
    And the cart invoices to customer "some-customer@something.com" address with postcode "4600"
    And I create an order from my cart
    Then It should throw an error deleting the customer "some-customer@something.com"

  Scenario: Just delete an Customer Account
    Given the site has a customer "some-other@something.com"
    And the customer "some-other@something.com" has an address with country "Austria", "4600", "Wels", "Freiung", "9-11/N3"
    Then It should not throw an error deleting the customer "some-other@something.com"
