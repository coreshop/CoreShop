@domain @shipping
Feature: Adding a new Shipping Rule
  In order to reuse shipping rule prices
  I'll create a new shipping rule
  with a shipping-rule action

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has a tax rate "AT" with "20%" rate
    And the site has a tax rule group "AT"
    And the tax rule group has a tax rule for country "Austria" with tax rate "AT"
    And the site has a product "Shoe" priced at 10000
    And the product has the tax rule group "AT"
    And I add the product "Shoe" to my cart
    And the site has a carrier "Post"
    And adding a shipping rule named "base"
    And the shipping rule is active
    And the shipping rule has a action price of 20 in currency "EUR"
    And adding a shipping rule named "fixed"
    And the shipping rule is active
    And the shipping rule has a action price of 5 in currency "EUR"
    And the shipping rule belongs to carrier "Post"

  Scenario: Creating a Shipping Rule which reuses the price of another Shipping Rule
    Given the shipping rule "fixed" has a action shipping-rule with shipping rule "base"
    Then shipping for my cart with carrier "Post" should be priced at "2500"
    And the shipping rule "fixed" should be saveable without a circular reference

  Scenario: Creating a Shipping Rule which reuses its own price
    Given the shipping rule "fixed" has a action shipping-rule with shipping rule "fixed"
    Then shipping for my cart with carrier "Post" should be priced at "500"
    And the shipping rule "fixed" should not be saveable because of a circular reference

  Scenario: Creating two Shipping Rules which reuse each others price
    Given the shipping rule "fixed" has a action shipping-rule with shipping rule "base"
    And the shipping rule "base" has a action shipping-rule with shipping rule "fixed"
    Then shipping for my cart with carrier "Post" should be priced at "2500"
    And the shipping rule "fixed" should not be saveable because of a circular reference
    And the shipping rule "base" should not be saveable because of a circular reference
