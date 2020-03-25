@cart @domain
Feature: Adding a new cart rule
  In order to give the customer a gift product
  based on the cart, we add a new rule

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has two categories "Shoes" and "Coats"
    And the site has a product "Shoe" priced at 10000
    And it is in category "Shoes"
    And the site has a product "Shoe 2" priced at 15000
    And it is in category "Shoes"
    And the site has a product "Jacket" priced at 40000
    And it is in category "Coats"

  Scenario: Add a new gift rule with a free product
    Given adding a cart price rule named "gift"
    And the cart rule is active
    And the cart rule is a voucher rule with code "FreeProduct123"
    And the cart rule has a action gift-product with product "Jacket"
    And I add the product "Shoe" to my cart
    And I apply the voucher code "FreeProduct123" to my cart
    Then the cart discount should be "0" including tax
    And the cart total should be "10000" including tax
    And the product "Jacket" should be in my cart as gift

  Scenario: Add a new gift rule with a free product and I remove the product
    Given adding a cart price rule named "gift"
    And the cart rule is active
    And the cart rule is a voucher rule with code "FreeProduct123"
    And the cart rule has a action gift-product with product "Jacket"
    And the cart rule has a condition products with product "Shoe"
    And I add the product "Shoe" to my cart
    And I apply the voucher code "FreeProduct123" to my cart
    And I remove the product "Shoe" from my cart
    Then the product "Jacket" should not be in my cart

  Scenario: Add a new gift rule that has the condition for the product that is a gift (1+1)
    Given adding a cart price rule named "gift"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has a action gift-product with product "Shoe"
    And the cart rule has a condition products with product "Shoe"
    And I add the product "Shoe" to my cart
    Then the product "Shoe" should be in my cart
    Then the product "Shoe" should be in my cart as gift
    Given I remove the product "Shoe" from my cart
    Then there should be no product in my cart
