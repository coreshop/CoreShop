@shipping @shipping_default_carrier
Feature: In Order to make checkout easier
  I create one carrier which will be defaulted

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has a tax rate "AT" with "20%" rate
    And the site has a tax rule group "AT"
    And the tax rule group has a tax rule for country "Austria" with tax rate "AT"
    And the tax rule group is valid for store "Austria"
    And the site has a product "Shoe" priced at 10000
    And the product has the tax rule group "AT"
    And the site has a customer "some-customer@something.com"
    And the customer "some-customer@something.com" has an address with country "Austria", "4600", "Wels", "Freiung", "9-11/N3"
    And the cart belongs to customer "some-customer@something.com"
    And the cart ships to customer "some-customer@something.com" address with postcode "4600"
    And the cart invoices to customer "some-customer@something.com" address with postcode "4600"

  Scenario: The default resolved shipping for my cart should be 20
    Given the site has a carrier "Post"
    And adding a shipping rule named "fixed"
    And the shipping rule has a action price of 20 in currency "EUR"
    And the shipping rule belongs to carrier "Post"
    And I add the product "Shoe" to my cart
    Then the cart shipping should be "2000" excluding tax

  Scenario: The default resolved shipping for my cart should be 40
    Given the site has a carrier "Post"
    And adding a shipping rule named "Post"
    And the shipping rule has a action price of 20 in currency "EUR"
    And the shipping rule belongs to carrier "Post"
    And the shipping rule has a condition postcode with "4700"
    And the site has another carrier "DPD"
    And adding a shipping rule named "DPD"
    And the shipping rule has a action price of 40 in currency "EUR"
    And the shipping rule belongs to carrier "DPD"
    And I add the product "Shoe" to my cart
    Then the shipping rule "Post" should be invalid for my cart with carrier "Post"
    Then the cart shipping should be "4000" excluding tax
