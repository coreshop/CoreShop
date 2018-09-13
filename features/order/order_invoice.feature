@order @order_invoice
Feature: Create a new order and add a invoice

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
    And the cart ships to customer "some-customer@something.com" address with postcode "4600"
    And the cart invoices to customer "some-customer@something.com" address with postcode "4600"
    And the cart belongs to customer "some-customer@something.com"

  Scenario: Create full invoice
    Given I create an order from my cart
    And I create a invoice for my order
    And I apply order invoice transition "request_invoice" to my order
    And I apply invoice transition "complete" to latest order invoice
    Then the order invoice state should be "invoiced"

  Scenario: Create partial invoice
    Given I add the product "T-Shirt" to my cart
    And I create an order from my cart
    And I create a invoice for my order
    And I apply order invoice transition "request_invoice" to my order
    And I apply invoice transition "complete" to latest order invoice
    Then the order invoice state should be "partially_invoiced"

  Scenario: Create two partial invoices
    Given I add the product "T-Shirt" to my cart
    And I create an order from my cart
    And I create a invoice for my order
    And I apply order invoice transition "request_invoice" to my order
    And I apply invoice transition "complete" to latest order invoice
    And I create another invoice for my order
    And I apply invoice transition "complete" to latest order invoice
    Then the order invoice state should be "invoiced"