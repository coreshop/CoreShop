@domain @cart
Feature: Create a new cart where store uses gross values
  In Order to calculate taxes
  we create a cart and add items to it

  Background:
    Given the site operates on a store in "Austria" with gross values
    And the site has a tax rate "AT" with "20%" rate
    And the site has a tax rule group "AT"
    And the tax rule group has a tax rule for country "Austria" with tax rate "AT"
    And the site has a tax rate "CH" with "7.7%" rate
    And the site has a tax rule group "CH"
    And the tax rule group has a tax rule for country "Austria" with tax rate "CH"
    And the site has a product-unit "Pieces"
    And the site has a product-unit "Carton"
    And the site has a product-unit "Palette"
    And the site has a product "Shoes" priced at 1000
    And the product has the tax rule group "AT"
    And the product has the default unit "Pieces"
    And the product has an additional unit "Carton" with conversion rate "24" and price 2000
    And the product has an additional unit "Palette" with conversion rate "200" and price 150000
    And the site has a product "T-Shirt" priced at 2000
    And the product has the tax rule group "AT"
    And the product has the default unit "Pieces"
    And the product has an additional unit "Carton" with conversion rate "24" and price 4000
    And the product has an additional unit "Palette" with conversion rate "200" and price 300000
    And the site has a product "Shorts" priced at 3000
    And the product has the tax rule group "AT"
    And the product has the default unit "Pieces"
    And the product has an additional unit "Carton" with conversion rate "24" and price 3000
    And the product has an additional unit "Palette" with conversion rate "200" and price 450000
    And the site has a product "CH T-Shirt" priced at 1590
    And the product has the tax rule group "CH"
    And the product has the default unit "Pieces"
    And the product has an additional unit "Carton" with conversion rate "24" and price 35000
    And the product has an additional unit "Palette" with conversion rate "200" and price 250000

  Scenario: Create a new cart and add a product and calculate totals
    Given I add the product "T-Shirt" with unit "Carton" to my cart
    Then the cart total tax should be "667"

  Scenario: Create a new cart, add a product and calculate subtotals
    Given I add the product "T-Shirt" with unit "Carton" to my cart
    Given I add the product "Shorts" with unit "Palette" to my cart
    Then the cart total tax should be "75667"

  Scenario: Create a new cart, add a product and calculate subtotals
    Given I add the product "Shoes" with unit "Pieces" to my cart
    Given I add the product "Shoes" with unit "Carton" to my cart
    Given I add the product "Shoes" with unit "Palette" to my cart
    Given I add the product "Shoes" to my cart
    Given I add the product "Shoes" with unit "Pieces" to my cart
    Then the cart total tax should be "25833"

  Scenario: Create a new cart for a different Tax Rate, add a product and calculate subtotals
    Given I add the product "CH T-Shirt" with unit "Carton" to my cart
    Given I add the product "CH T-Shirt" with unit "Pieces" to my cart
    Then the cart total should be "36590" including tax
    Then the cart total tax should be "2616"
