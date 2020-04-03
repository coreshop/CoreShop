@domain @shipping
Feature: Adding a new Shipping Rule
  In order to calculate shipping
  I'll create a new shipping rule
  with an product condition

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has a tax rate "AT" with "20%" rate
    And the site has a tax rule group "AT"
    And the tax rule group has a tax rule for country "Austria" with tax rate "AT"
    And the site has a product "Shoe" priced at 10000
    And the product "Shoe" has a variant "Shoe Variant" priced at 100
    And the product has the tax rule group "AT"
    And the site has a product "Jacket" priced at 400
    And the product "Jacket" has a variant "Jacket Variant" priced at 400
    And the product has the tax rule group "AT"
    And the site has a carrier "Post"

  Scenario: Add a new product shipping rule which is valid
    Given adding a shipping rule named "product"
    And the shipping rule is active
    And the shipping rule has a condition products with product "Shoe" which includes variants
    And I add the product "Shoe Variant" to my cart
    Then the shipping rule should be valid for my cart with carrier "Post"

  Scenario: Add a new product shipping rule which is inactive
    Given adding a shipping rule named "product"
    And the shipping rule is inactive
    And the shipping rule has a condition products with product "Shoe" which includes variants
    And I add the product "Shoe Variant" to my cart
    Then the shipping rule should be invalid for my cart with carrier "Post"

  Scenario: Add a new product shipping rule which is invalid
    Given adding a shipping rule named "product"
    And the shipping rule is active
    And the shipping rule has a condition products with product "Shoe" which includes variants
    And I add the product "Jacket Variant" to my cart
    Then the shipping rule should be invalid for my cart with carrier "Post"

  Scenario: Add a new product shipping rule with two products which is valid
    Given adding a shipping rule named "product"
    And the shipping rule is active
    And the shipping rule has a condition products with products "Shoe", "Jacket" which includes variants
    And I add the product "Jacket Variant" to my cart
    And I add the product "Shoe Variant" to my cart
    Then the shipping rule should be valid for my cart with carrier "Post"

  Scenario: Add a new product shipping rule with two products which is valid
    Given adding a shipping rule named "product"
    And the shipping rule is active
    And the shipping rule has a condition products with product "Jacket" which includes variants
    And I add the product "Jacket Variant" to my cart
    And I add the product "Shoe Variant" to my cart
    Then the shipping rule should be valid for my cart with carrier "Post"
