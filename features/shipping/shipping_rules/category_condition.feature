@shipping @shipping_rules @shipping_rule_condition_category
Feature: Adding a new Shipping Rule
  In order to calculate shipping
  I'll create a new shipping rule
  with an category condition

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has a tax rate "AT" with "20%" rate
    And the site has a tax rule group "AT"
    And the tax rule group has a tax rule for country "Austria" with tax rate "AT"
    And the tax rule group is valid for store "Austria"
    And the site has two categories "Shoes" and "Coats"
    And the site has a category "Sneakers"
    And the category "Sneakers" is child of category "Shoes"
    And the site has a product "Shoe" priced at 10000
    And the product has the tax rule group "AT"
    And it is in category "Shoes"
    And the site has a product "Jacket" priced at 400
    And the product has the tax rule group "AT"
    And it is in category "Coats"
    And the site has a product "Sneaker" priced at 3500
    And the product has the tax rule group "AT"
    And it is in category "Sneakers"
    And the site has a carrier "Post"

  Scenario: Add a new category shipping rule which is valid
    Given adding a shipping rule named "category"
    And the shipping rule is active
    And the shipping rule has a condition categories with category "Shoes"
    And I add the product "Shoe" to my cart
    Then the shipping rule should be valid for my cart with carrier "Post"

  Scenario: Add a new category shipping rule which is inactive
    Given adding a shipping rule named "category"
    And the shipping rule is inactive
    And the shipping rule has a condition categories with category "Shoes"
    And I add the product "Shoe" to my cart
    Then the shipping rule should be invalid for my cart with carrier "Post"

  Scenario: Add a new category shipping rule which is invalid
    Given adding a shipping rule named "category"
    And the shipping rule is active
    And the shipping rule has a condition categories with category "Shoes"
    And I add the product "Jacket" to my cart
    Then the shipping rule should be invalid for my cart with carrier "Post"

  Scenario: Add a new category shipping rule with two products which is valid
    Given adding a shipping rule named "category"
    And the shipping rule is active
    And the shipping rule has a condition categories with categories "Coats", "Shoes"
    And I add the product "Jacket" to my cart
    And I add the product "Shoe" to my cart
    Then the shipping rule should be valid for my cart with carrier "Post"

  Scenario: Add a new category shipping rule with two products which is valid
    Given adding a shipping rule named "category"
    And the shipping rule is active
    And the shipping rule has a condition categories with category "Coats"
    And I add the product "Jacket" to my cart
    And I add the product "Shoe" to my cart
    Then the shipping rule should be valid for my cart with carrier "Post"

  Scenario: Add a new category shipping rule which includes all subcategory and is invalid
    Given adding a shipping rule named "category"
    And the shipping rule is active
    And the shipping rule has a condition categories with category "Shoes"
    And I add the product "Sneaker" to my cart
    Then the shipping rule should be invalid for my cart with carrier "Post"

  Scenario: Add a new category shipping rule which includes all subcategory and is valid
    Given adding a shipping rule named "category"
    And the shipping rule is active
    And the shipping rule has a condition categories with category "Shoes" and it is recursive
    And I add the product "Sneaker" to my cart
    Then the shipping rule should be valid for my cart with carrier "Post"
