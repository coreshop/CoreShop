@cart @cart_shipping_tax_rate
Feature: Use a different calculation strategy for shipping tax calculation
  The tax calculation for the shipping can be based on the cart items
  We create and store a cart with different products to
  check the shipping taxes.

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has a tax rate "AT20" with "20%" rate
    And the site has a tax rule group "AT20"
    And the tax rule group has a tax rule for country "Austria" with tax rate "AT20"
    And the site has a tax rate "AT10" with "10%" rate
    And the site has a tax rule group "AT10"
    And the tax rule group has a tax rule for country "Austria" with tax rate "AT10"
    And the site has a carrier "Post"
    And the carrier uses the tax calculation strategy "cartItems"
    And adding a shipping rule named "post"
    And the shipping rule is active
    And the shipping rule has a action price of 10 in currency "EUR"
    And the shipping rule belongs to carrier "Post"
    And the site has a product "Book" priced at 1000
    And the product has the tax rule group "AT10"
    And the site has a product "Another Book" priced at 1000
    And the product has the tax rule group "AT10"
    And the site has a product "DVD" priced at 3000
    And the product has the tax rule group "AT20"

  Scenario: Create a new cart, add two products and the correct shipping should be applied
    And I add the product "Book" to my cart
    And I add the product "DVD" to my cart
    Then the cart shipping should be "1176" including tax
    And the cart shipping should be "1000" excluding tax
    And the loaded carts shipping tax rate should be "17.6"

  Scenario: Create a new cart, add one product and the correct shipping should be applied
    And I add the product "Book" to my cart
    Then the cart shipping should be "1100" including tax
    And the cart shipping should be "1000" excluding tax
    And the loaded carts shipping tax rate should be "10"

  Scenario: Create a new cart, add two of the same product and the correct shipping should be applied
    And I add the product "Book" to my cart
    And I add the product "Book" to my cart
    Then the cart shipping should be "1100" including tax
    And the cart shipping should be "1000" excluding tax
    And the loaded carts shipping tax rate should be "10"

  Scenario: Create a new cart, add two products with the same tax-rate and the correct shipping should be applied
    And I add the product "Book" to my cart
    And I add the product "Another Book" to my cart
    Then the cart shipping should be "1100" including tax
    And the cart shipping should be "1000" excluding tax
    And the loaded carts shipping tax rate should be "10"
