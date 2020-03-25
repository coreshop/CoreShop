@cart @domain
Feature: Create a new cart
  In Order for a customer to purchase something
  he needs to create a cart first
  and put items into it

  Background:
    Given the site operates on a store in "Austria"
    And the site has a tax rate "AT" with "20%" rate
    And the site has a tax rule group "AT"
    And the tax rule group has a tax rule for country "Austria" with tax rate "AT"
    And the site has a product-unit "Pieces"
    And the site has a product-unit "Carton"
    And the site has a product-unit "Palette"
    And the site has a product "Shoes" priced at 1000
    And the product has the tax rule group "AT"
    And the product has the default unit "Pieces"
    And the product has and additional unit "Carton" with conversion rate "24" and price 2000 and precision 2
    And the product has and additional unit "Palette" with conversion rate "200" and price 150000 and precision 4
    And adding a cart price rule named "discount"
    And the cart rule is active
    And the cart rule is a voucher rule with code "asdf"
    And the cart rule has a action discount-percent with 10% discount

  Scenario: Create a new cart and add 1.5 units to the cart and then apply a voucher
    Given I add the product "Shoes" with unit "Carton" in quantity 1.5 to my cart
    And I apply the voucher code "asdf" to my cart
    Then the cart total should be "2700" excluding tax

  Scenario: Create a new cart and add 2.5014 units to the cart and then apply a voucher
    Given I add the product "Shoes" with unit "Palette" in quantity 2.5014 to my cart
    And I apply the voucher code "asdf" to my cart
    Then the cart total should be "337689" excluding tax
