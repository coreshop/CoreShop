@domain @shipping
Feature: Adding a new Shipping Rule
  In order to calculate shipping
  I'll create a new shipping rule
  with a guest condition

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And the site has a tax rate "AT" with "20%" rate
    And the site has a tax rule group "AT"
    And the tax rule group has a tax rule for country "Austria" with tax rate "AT"
    And the site has a product "Shoe" priced at 10000
    And the product has the tax rule group "AT"
    And the site has a carrier "Post"
    And I am in country "Austria"
    And adding a shipping rule named "guest"
    And the shipping rule is active
    And the shipping rule has a condition guest
    And the site has a customer "some-customer@something.com"
    And the site has a guest "some-guest@something.com"

  Scenario: Add a new guest shipping rule for a guest customer which is valid
    Given I am guest "some-guest@something.com"
    And I add the product "Shoe" to my cart
    Then the shipping rule should be valid for my cart with carrier "Post"

  Scenario: Add a new guest shipping rule for a cart without a customer which is valid
    Given I add the product "Shoe" to my cart
    Then the shipping rule should be valid for my cart with carrier "Post"

  Scenario: Add a new guest shipping rule for a customer which is invalid
    Given I am customer "some-customer@something.com"
    And I add the product "Shoe" to my cart
    Then the shipping rule should be invalid for my cart with carrier "Post"