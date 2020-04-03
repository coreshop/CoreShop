@domain @order
Feature: Create a new order
  I create a invoice and cancel it
  I create another invoice which then gets invoiced
  The order invoice state should be completed

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
    And There is a payment provider "Bankwire" using factory "Bankwire"

  Scenario: Create order with payment and shipment which still is completed
    Given I create an order from my cart
    And I apply transition "confirm" to my order
    And I create a payment for my order with payment provider "Bankwire" and amount 2400
    And I apply payment transition "complete" to latest order payment
    And I create a invoice for my order
    And I apply order invoice transition "request_invoice" to my order
    And I apply invoice transition "cancel" to latest order invoice
    Then the order invoice state should be "ready"
    And I create another invoice for my order
    And I apply invoice transition "complete" to latest order invoice
    Then the order invoice state should be "invoiced"
    And the order payment state should be "paid"
    And the order state should be "confirmed"
