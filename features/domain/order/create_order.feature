@order @domain
Feature: Create a new order

  Background:
    Given the site operates on a store in "Austria"
    And the site has a country "Germany" with currency "EUR"
    And the country "Germany" is active
    And the site operates on locale "en"
    And the site has a tax rate "AT" with "20%" rate
    And the site has a tax rule group "AT"
    And the tax rule group has a tax rule for country "Austria" with tax rate "AT"
    And the site has a product "T-Shirt" priced at 2000
    And the product has the tax rule group "AT"
    And the site has a customer "some-customer@something.com"
    And the customer "some-customer@something.com" has an address with country "Austria", "4600", "Wels", "Freiung", "9-11/N3"
    And the customer "some-customer@something.com" has an address with country "Germany", "04600", "Wels", "Freiung", "9-11/N3"
    And the cart belongs to customer "some-customer@something.com"

  Scenario: Create a new order and add a product
    Given I add the product "T-Shirt" to my cart
    And the cart ships to customer "some-customer@something.com" address with postcode "4600"
    And the cart invoices to customer "some-customer@something.com" address with postcode "4600"
    And I create an order from my cart
    Then there should be one product in my order
    And the notification rule for "order" should have been fired

   Scenario: Create a new order and add a product and calculate totals
    Given I add the product "T-Shirt" to my cart
    And the cart ships to customer "some-customer@something.com" address with postcode "4600"
    And the cart invoices to customer "some-customer@something.com" address with postcode "4600"
    And I create an order from my cart
    Then the order total should be "2400" including tax
    And the order total should be "2000" excluding tax
    And the notification rule for "order" should have been fired

  Scenario: Create a new order and add a product and calculate subtotals
    Given I add the product "T-Shirt" to my cart
    And the cart ships to customer "some-customer@something.com" address with postcode "4600"
    And the cart invoices to customer "some-customer@something.com" address with postcode "4600"
    And I create an order from my cart
    Then the order subtotal should be "2400" including tax
    And the order subtotal should be "2000" excluding tax
    And the notification rule for "order" should have been fired

  Scenario: Create a new order and add a product when I ship to a different country
    Given I add the product "T-Shirt" to my cart
    And the cart ships to customer "some-customer@something.com" address with postcode "04600"
    And the cart invoices to customer "some-customer@something.com" address with postcode "4600"
    And I create an order from my cart
    Then there should be one product in my order
    And the order total should be "2000" including tax
    And the order total should be "2000" excluding tax
    And the order subtotal should be "2000" including tax
    And the order subtotal should be "2000" excluding tax
    And the notification rule for "order" should have been fired
