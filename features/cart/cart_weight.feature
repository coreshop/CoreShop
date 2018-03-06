@cart
Feature: Create a new cart
  In Order for a customer to purchase something
  he needs to create a cart first
  and put items into it
  the cart then has a weight

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has a tax rate "AT" with "20%" rate
    And the site has a tax rule group "AT"
    And the tax rule group has a tax rule for country "Austria" with tax rate "AT"
    And the tax rule group is valid for store "Austria"

  Scenario: Create a new cart and add a product, cart should weigh 10kg
    And the site has a product "Shoe" priced at 10000
    And the product weighs 10kg
    And the product has the tax rule group "AT"
    And I add the product "Shoe" to my cart
    Then the cart should weigh 10kg

  Scenario: Create a new cart and one product twice, cart should weigh 20kg
    And the site has a product "Shoe" priced at 10000
    And the product weighs 10kg
    And the product has the tax rule group "AT"
    And I add the product "Shoe" to my cart
    And I add the product "Shoe" to my cart
    Then the cart should weigh 20kg

  Scenario: Create a new cart and add two products, cart should weigh 12kg
    And the site has a product "Shoe" priced at 10000
    And the product weighs 7kg
    And the product has the tax rule group "AT"
    And the site has a product "Dress" priced at 20000
    And the product weighs 5kg
    And the product has the tax rule group "AT"
    And I add the product "Shoe" to my cart
    And I add the product "Dress" to my cart
    Then the cart should weigh 12kg
