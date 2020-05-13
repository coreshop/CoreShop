@domain @cart
Feature: Adding a new cart rule
  In order to give the customer discounts
  based on the cart, we add a new rule
  with a customer condition

  Background:
    Given the site operates on a store in "Austria"
    Given the site has a currency "Euro" with iso "EUR"
    Given I am in country "Austria"
    Given the site has two categories "Shoes" and "Coats"
    Given the site has a product "Shoe" priced at 100
    Given the product "Shoe" has a variant "Shoe Variant" priced at 100
    Given the site has a product "Shoe 2" priced at 150
    Given the product "Shoe 2" has a variant "Shoe 2 Variant" priced at 100
    Given the site has a product "Jacket" priced at 400
    Given the product "Jacket" has a variant "Jacket Variant" priced at 400

  Scenario: Add a new cart price rule which is valid
    Given I add the product "Shoe Variant" to my cart
    And adding a cart price rule named "product"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has a condition products with product "Shoe" which includes variants
    Then the cart rule should be valid for my cart

  Scenario: Add a new cart price rule which is invalid
    Given I add the product "Shoe 2 Variant" to my cart
    And adding a cart price rule named "product"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has a condition products with product "Shoe" and product "Jacket" which includes variants
    Then the cart rule should be invalid for my cart

  Scenario: Add a new cart price rule which is valid for two products
    Given I add the product "Jacket Variant" to my cart
    And adding a cart price rule named "product"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has a condition products with product "Shoe" and product "Jacket" which includes variants
    Then the cart rule should be valid for my cart
