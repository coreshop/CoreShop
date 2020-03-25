@order @domain
Feature: Create a new order and add a shipment

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
    And I add the product "T-Shirt" to my cart
    And the cart belongs to customer "some-customer@something.com"
    And the cart ships to customer "some-customer@something.com" address with postcode "4600"
    And the cart invoices to customer "some-customer@something.com" address with postcode "4600"
    And I create an order from my cart

  Scenario: Create full shipment
    Given I create a shipment for my order
    And I apply order shipment transition "request_shipment" to my order
    And I apply shipment transition "ship" to latest order shipment
    Then the order shipping state should be "shipped"

  Scenario: Create partial shipment
    Given I add the product "T-Shirt" to my cart
    And I create an order from my cart
    And I create a shipment for my order
    And I apply order shipment transition "request_shipment" to my order
    And I apply shipment transition "ship" to latest order shipment
    Then the order shipping state should be "partially_shipped"

  Scenario: Create two partial shipments
    Given I add the product "T-Shirt" to my cart
    And I create an order from my cart
    And I create a shipment for my order
    And I apply order shipment transition "request_shipment" to my order
    And I apply shipment transition "ship" to latest order shipment
    And I create another shipment for my order
    And I apply shipment transition "ship" to latest order shipment
    Then the order shipping state should be "shipped"
