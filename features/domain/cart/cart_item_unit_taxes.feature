@domain @cart
Feature: Create a new cart item units where store uses net values
  In Order to calculate taxes
  we create a cart and add items to it

  Background:
    Given the site operates on a store in "Austria"
    And the site has a tax rate "AT" with "20%" rate
    And the site has a tax rule group "AT"
    And the tax rule group has a tax rule for country "Austria" with tax rate "AT"
    And the site has a product "Shoes" priced at 27422
    And the product has the tax rule group "AT"

  Scenario: Create a new cart and add a product and calculate item unit taxes
    Given I add the product "Shoes" x 15 to my cart
    Then the cart item unit at position 1 for product "Shoes" should have a total of 27422 excluding tax
    Then the cart item unit at position 1 for product "Shoes" should have a total of 32907 including tax
    Then the cart item unit at position 7 for product "Shoes" should have a total of 27422 excluding tax
    Then the cart item unit at position 7 for product "Shoes" should have a total of 32906 including tax

