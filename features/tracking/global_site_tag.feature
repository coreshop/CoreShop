@tracking @tracking_google_site_tag
Feature: In order to track ecommerce sales
  we track them using Google Site Tag Manager

  Background:
    Given the site operates on a store in "Austria"
    And the site operates on locale "en"
    And the site has a tax rate "AT" with "20%" rate
    And the site has a tax rule group "AT"
    And the tax rule group has a tax rule for country "Austria" with tax rate "AT"
    And the site has a product "T-Shirt" priced at 2000
    And the product has the tax rule group "AT"

  Scenario: Track Product Impression
    Then tracking product impression with tracker "google-gtag-enhanced-ecommerce" should generate:
      """
      gtag('event', 'view_item_list', {"items":[{"id":%DataObject(product, {"method": "getId"});,"name":"T-Shirt","category":"","price":24,"quantity":1,"currency":"EUR"}]});
      """

  Scenario: Track Product
    Then tracking product with tracker "google-gtag-enhanced-ecommerce" should generate:
      """
      gtag('event', 'view_item', {"items":[{"id":%DataObject(product, {"method": "getId"});,"name":"T-Shirt","category":"","price":24,"quantity":1,"currency":"EUR"}]});
      """

  Scenario: Track Cart Add
    Then tracking cart-add for my cart with product with tracker "google-gtag-enhanced-ecommerce" should generate:
      """
      gtag('event', 'add_to_cart', {"items":[{"id":%DataObject(product, {"method": "getId"});,"name":"T-Shirt","category":"","price":24,"quantity":1,"currency":"EUR"}]});
      """

  Scenario: Track Cart Add
    Then tracking cart-remove for my cart with product with tracker "google-gtag-enhanced-ecommerce" should generate:
      """
      gtag('event', 'remove_from_cart', {"items":[{"id":%DataObject(product, {"method": "getId"});,"name":"T-Shirt","category":"","price":24,"quantity":1,"currency":"EUR"}]});
      """

  Scenario: Track Checkout Step
    Given I add the product "T-Shirt" to my cart
    Then tracking checkout step for my cart with tracker "google-gtag-enhanced-ecommerce" should generate:
      """
      gtag('event', 'checkout_progress', []);
      """

  Scenario: Track Checkout Complete
    Given the site has a customer "some-customer@something.com"
    And the customer "some-customer@something.com" has an address with country "Austria", "4600", "Wels", "Freiung", "9-11/N3"
    And the cart belongs to customer "some-customer@something.com"
    And I add the product "T-Shirt" to my cart
    And the cart ships to customer "some-customer@something.com" address with postcode "4600"
    And the cart invoices to customer "some-customer@something.com" address with postcode "4600"
    And I create an order from my cart
    Then tracking my order checkout complete with tracker "google-gtag-enhanced-ecommerce" should generate:
      """
      gtag('event', 'purchase', {"id":%DataObject(order, {"method": "getId"});,"affiliation":24,"total":24,"tax":null,"shipping":0,"currency":"EUR","items":[{"id":%DataObject(orderItem, {"method": "getId"});,"name":"T-Shirt","category":"","price":24,"quantity":1,"currency":"EUR"}]});
      """