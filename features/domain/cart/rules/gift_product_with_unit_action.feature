@domain @cart
Feature: Adding a new cart rule
  In order to give the customer a gift product with a unit
  based on the cart, we add a new rule

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has two categories "Shoes" and "Coats"
    And the site has a product-unit "Pieces"
    And the site has a product-unit "Carton"
    And the site has a product "Shoe" priced at 10000
    And it is in category "Shoes"
    And the site has a product "Jacket" priced at 10000
    And it is in category "Coats"
    And the product has the default unit "Pieces"

  Scenario: Add a new gift rule with a free product with a unit
    Given adding a cart price rule named "gift"
    And the cart rule is active
    And the cart rule is a voucher rule with code "FreeProduct123"
    And the cart rule has a action gift-product with product "Jacket"
    And I add the product "Shoe" to my cart
    And I apply the voucher code "FreeProduct123" to my cart
    Then the cart discount should be "0" including tax
    And the cart total should be "10000" including tax
    And the product "Jacket" should be in my cart as gift
    And the product "Jacket" in my cart should have unit "Pieces"

  Scenario: Add a new gift rule with a free product with no unit
    Given adding a cart price rule named "gift"
    And the cart rule is active
    And the cart rule is a voucher rule with code "FreeProduct123"
    And the cart rule has a action gift-product with product "Shoe"
    And I add the product "Jacket" to my cart
    And I apply the voucher code "FreeProduct123" to my cart
    Then the cart discount should be "0" including tax
    And the cart total should be "10000" including tax
    And the product "Shoe" should be in my cart as gift
    And the product "Shoe" in my cart should have no unit
