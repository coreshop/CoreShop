@domain @order
Feature: Create a new order
  And test if the shipping is calculated right

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Swiss Franc" with iso "CHF"
    And the site operates on locale "en"
    And I am in country "Austria"
    And the site has a tax rate "CH" with "7.7%" rate
    And the site has a tax rule group "CH"
    And the tax rule group has a tax rule for country "Austria" with tax rate "CH"
    And the site has a carrier "Post"
    And the carrier has the tax rule group "CH"
    And adding a shipping rule named "post"
    And the shipping rule is active
    And the shipping rule has a action price of 10 in currency "CHF"
    And the shipping rule belongs to carrier "Post"
    And the site has a product "Shoe" priced at 10000
    And the product has the tax rule group "CH"
    And the site has a customer "some-customer@something.com"
    And the customer "some-customer@something.com" has an address with country "Austria", "4600", "Wels", "Freiung", "9-11/N3"
    And the cart belongs to customer "some-customer@something.com"

  Scenario: Create a new order and add a product
    Given I add the product "Shoe" to my cart
    And the cart ships to customer "some-customer@something.com" address with postcode "4600"
    And the cart invoices to customer "some-customer@something.com" address with postcode "4600"
    And I create an order from my cart
    Then the order shipping should be "1000" excluding tax
    And the order shipping should be "1077" including tax
    And the order shipping tax rate should be "7.7"
