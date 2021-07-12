@domain @tracking
Feature: In order to track ecommerce sales
  we track them using Matomo

  Background:
    Given the site operates on a store in "Austria"
    And the site operates on locale "en"
    And the site has a tax rate "AT" with "20%" rate
    And the site has a tax rule group "AT"
    And the tax rule group has a tax rule for country "Austria" with tax rate "AT"
    And the site has a product "T-Shirt" priced at 2000
    And the product has the tax rule group "AT"

  Scenario: Track Product
    Then tracking product with tracker "matomo" should generate:
      """
      _paq.push(["setEcommerceView",##id##,"T-Shirt","",[],24]);
      """

  Scenario: Track Cart Add
    Given I add the product "T-Shirt" to my cart
    Then tracking cart-add for my cart with product with tracker "matomo" should generate:
      """
      _paq.push(["addEcommerceItem",##item_id##,"T-Shirt","",24,1]); _paq.push(["trackEcommerceCartUpdate",24]);
      """

  Scenario: Track Cart Add
    Then tracking cart-remove for my cart with product with tracker "matomo" should generate:
      """
      _paq.push(["trackEcommerceCartUpdate",0]);
      """

  Scenario: Track Checkout Complete
    Given the site has a customer "some-customer@something.com"
    And the customer "some-customer@something.com" has an address with country "Austria", "4600", "Wels", "Freiung", "9-11/N3"
    And the cart belongs to customer "some-customer@something.com"
    And I add the product "T-Shirt" to my cart
    And the cart ships to customer "some-customer@something.com" address with postcode "4600"
    And the cart invoices to customer "some-customer@something.com" address with postcode "4600"
    And I create an order from my cart
    Then tracking my order checkout complete with tracker "matomo" should generate:
      """
      _paq.push(["addEcommerceItem",##item_id##,"T-Shirt","",24,1]); _paq.push(["trackEcommerceOrder",##id##,24,24,4,0,0]);
      """
