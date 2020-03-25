@cart @domain
Feature: Adding a new cart rule
  In order to give the customer discounts
  based on the cart, we add a new rule
  with a category condition

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has two categories "Shoes" and "Coats"
    And the site has a category "Sneakers"
    And the category "Sneakers" is child of category "Shoes"
    And the site has a product "Shoe" priced at 10000
    And it is in category "Shoes"
    And the site has a product "Shoe 2" priced at 15000
    And it is in category "Shoes"
    And the site has a product "Jacket" priced at 40000
    And it is in category "Coats"
    And the site has a product "Sneaker" priced at 350000
    And it is in category "Sneakers"

  Scenario: Add a new category cart price rule which is valid
    Given I add the product "Shoe" to my cart
    And adding a cart price rule named "category"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has a condition categories with category "Shoes"
    Then the cart rule should be valid for my cart

  Scenario: Add a new category cart price rule which is invalid
    Given I add the product "Shoe" to my cart
    And adding a cart price rule named "category"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has a condition categories with category "Coats"
    And the cart rule should be invalid for my cart

  Scenario: Add a new category cart price rule for a child category which is invalid
    Given I add the product "Sneaker" to my cart
    And adding a cart price rule named "category"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has a condition categories with category "Shoes"
    Then the cart rule should be invalid for my cart

  Scenario: Add a new category cart price rule for a child category which is valid
    Given I add the product "Sneaker" to my cart
    And adding a cart price rule named "category"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has a condition categories with category "Shoes" and it is recursive
    Then the cart rule should be valid for my cart
