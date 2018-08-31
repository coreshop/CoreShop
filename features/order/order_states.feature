@order @order_states
Feature: Create a new order and change states

  Background:
    Given the site operates on a store in "Austria"
    And the site operates on locale "en"
    And the site has a tax rate "AT" with "20%" rate
    And the site has a tax rule group "AT"
    And the tax rule group has a tax rule for country "Austria" with tax rate "AT"
    And the tax rule group is valid for store "Austria"
    And the site has a product "T-Shirt" priced at 2000
    And the product has the tax rule group "AT"
    And the site has a customer "some-customer@something.com"
    And the customer "some-customer@something.com" has an address with country "Austria", "4600", "Wels", "Freiung", "9-11/N3"
    And I add the product "T-Shirt" to my cart
    And the cart belongs to customer "some-customer@something.com"
    And the cart ships to customer "some-customer@something.com" address with postcode "4600"
    And the cart invoices to customer "some-customer@something.com" address with postcode "4600"
    And I create an order from my cart

  Scenario: Test order states
    Then the order state should be "new"
    And I should be able to apply transition "confirm" to my order
    And I should be able to apply transition "cancel" to my order
    And I should not be able to apply transition "complete" to my order
    And I should not be able to apply transition "create" to my order

  Scenario: Test order payment states
    Then the order payment state should be "awaiting_payment"
    And I should not be able to apply payment transition "request_payment" to my order
    And I should not be able to apply payment transition "partially_refund" to my order
    And I should not be able to apply payment transition "refund" to my order
    And I should be able to apply payment transition "partially_pay" to my order
    And I should be able to apply payment transition "cancel" to my order
    And I should be able to apply payment transition "pay" to my order

  Scenario: Test order shipping states
    Then the order shipping state should be "new"
    And I should be able to apply shipping transition "request_shipment" to my order
    And I apply order shipment transition "request_shipment" to my order
    And I should be able to apply shipping transition "partially_ship" to my order
    And I should be able to apply shipping transition "cancel" to my order
    And I should be able to apply shipping transition "ship" to my order

  Scenario: Test order invoice states
    Then the order invoice state should be "new"
    And I should be able to apply invoice transition "request_invoice" to my order
    And I apply order invoice transition "request_invoice" to my order
    And I should be able to apply invoice transition "partially_invoice" to my order
    And I should be able to apply invoice transition "cancel" to my order
    And I should be able to apply invoice transition "invoice" to my order
