@ui @ui_domain
Feature: Revise the payment of an order
  In order to retry a payment that did not go through
  As a customer
  I want the revise page to tell me what happened to my last payment

  Background:
    Given the site operates on a store in "Austria"
    And the store "Austria" is the default store
    And the site operates on locale "en"
    And the site has a tax rate "AT" with "20%" rate
    And the site has a tax rule group "AT"
    And the tax rule group has a tax rule for country "Austria" with tax rate "AT"
    And the site has a product "T-Shirt" priced at 2000
    And the product is active and published and available for store "Austria"
    And the product has the tax rule group "AT"
    And the site has a customer "some-customer@something.com"
    And the customer "some-customer@something.com" has an address with country "Austria", "4600", "Wels", "Freiung", "9-11/N3"
    And the cart belongs to customer "some-customer@something.com"
    And I add the product "T-Shirt" to my cart
    And the cart ships to customer "some-customer@something.com" address with postcode "4600"
    And the cart invoices to customer "some-customer@something.com" address with postcode "4600"
    And I create an order from my cart
    And the site has a payment provider "Bankwire" using factory "offline"

  Scenario: The revise page shows the state of the payment that was just attempted
    Given I create a payment for my order with payment provider "Bankwire"
    And I apply payment transition "fail" to latest order payment
    And I create a payment for my order with payment provider "Bankwire"
    And I apply payment transition "cancel" to latest order payment
    When I open the revise page of my order for the latest order payment
    Then the revise page should show the message "coreshop.ui.order.revise.transaction_cancelled"
    And the revise page should not show the message "coreshop.ui.order.revise.transaction_failed"

  Scenario: The revise page is not found for an unknown order token
    When I open the revise page for the order token "this-token-does-not-exist"
    Then the revise page should not be found
