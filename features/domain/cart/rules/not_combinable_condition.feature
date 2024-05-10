@domain @cart
Feature: Adding a new cart rule
  In order to give the customer discounts
  based on the cart, we add a new rule
  with a not combinable condition

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has a tax rate "AT" with "20%" rate
    And the site has a tax rule group "AT"
    And the tax rule group has a tax rule for country "Austria" with tax rate "AT"
    And the site has a product "Shoe" priced at 10000
    And adding a cart price rule named "price_rule_100"
    And the cart rule is active
    And the cart rule has priority "100"
    And the cart rule is not a voucher rule
    And the cart rule has a action discount-percent with 30% discount
    And adding a cart price rule named "price_rule_200"
    And the cart rule is active
    And the cart rule has priority "200"
    And the cart rule is not a voucher rule
    And the cart rule has a action discount-percent with 20% discount

  Scenario: Add a new not combinable price rule, which is not combinable with the other 2 rules
    Given adding a cart price rule named "not_combinable"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has priority "300"
    And the cart rule has a condition not combinable with cart rule "price_rule_100" and cart rule "price_rule_200"
    And the cart rule has a action discount-percent with 30% discount
    Given the cart rule "price_rule_100" has a condition not combinable with cart rule "not_combinable" and cart rule "price_rule_200"
    Given the cart rule "price_rule_200" has a condition not combinable with cart rule "not_combinable" and cart rule "price_rule_100"
    And I add the product "Shoe" to my cart
    Then the cart discount should be "-3000" including tax

  Scenario: Add a new not combinable price rule, which is not combinable with the other 1 other rule
    Given adding a cart price rule named "not_combinable"
    And the cart rule is active
    And the cart rule is not a voucher rule
    And the cart rule has priority "300"
    And the cart rule has a condition not combinable with cart rule "price_rule_100"
    And the cart rule has a action discount-percent with 30% discount
    Given the cart rule "price_rule_100" has a condition not combinable with cart rule "price_rule_200"
    Given the cart rule "price_rule_200" has a condition not combinable with cart rule "price_rule_100"
    Given I add the product "Shoe" to my cart
    Then the cart discount should be "-5000" including tax
