@tracking @tracking_tag_manager_classic
Feature: In order to track ecommerce sales
  we track them using Google Tag Manager Classic

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
    And I add the product "T-Shirt" to my cart
    And the cart ships to customer "some-customer@something.com" address with postcode "4600"
    And the cart invoices to customer "some-customer@something.com" address with postcode "4600"
    And I create an order from my cart

  Scenario: Track Checkout Complete
    Then tracking my order checkout complete with tracker "google-gtm-classic-ecommerce" should generate:
      """
      window.dataLayer = window.dataLayer || [];dataLayer.push({"transactionId":%DataObject(order, {"method" : "getId"});,"transactionAffiliation":24,"transactionTotal":24,"transactionTax":null,"transactionShipping":0,"transactionCurrency":"EUR","transactionProducts":[{"id":%DataObject(orderItem, {"method": "getId"});,"name":"T-Shirt","category":"","price":24,"quantity":1}]});
      """