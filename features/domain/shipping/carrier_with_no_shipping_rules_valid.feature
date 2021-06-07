@domain @shipping
Feature: Add a new carrier without rules should be valid

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

  Scenario: Carrier with not shipping rule should be valid
    Given I add the product "Shoe" to my cart
    And the carrier "Post" should be valid for my cart

  Scenario: Carrier with one shipping rule should be valid
    Given adding a shipping rule named "product"
    And the shipping rule is active
    And the shipping rule has a condition products with product "Shoe"
    And the shipping rule belongs to carrier "Post"
    And I add the product "Shoe" to my cart
    And the carrier "Post" should be valid for my cart

