@domain @cart
Feature: Adding a new cart rule
  In order to give the customer credit
  based on the cart, we add a new credit voucher rule

  Background:
    Given the site operates on a store in "Austria"
    And the site has a currency "Euro" with iso "EUR"
    And I am in country "Austria"
    And the site has a tax rate "AT" with "20%" rate
    And the site has a tax rule group "AT"
    And the tax rule group has a tax rule for country "Austria" with tax rate "AT"
    And the site has a product "Shoe" priced at 10000
    And the product has the tax rule group "AT"
    And I add the product "Shoe" to my cart

  Scenario: Add a new voucher credit rule with 20 euro
    Given adding a cart price rule named "credit"
    And the cart rule is active
    And the cart rule is a voucher rule with code "asdf"
    And the voucher code "asdf" is a credit voucher with credit "2000" in currency "EUR"
    And the cart rule has a action voucher credit
    And I apply the voucher code "asdf" to my cart
    Then the cart discount should be "-2000" including tax
    Then the cart discount should be "-1667" excluding tax
    Then the cart total should be "8333" excluding tax
    Then the cart total should be "10000" including tax

  Scenario: Add a new voucher credit rule with 200 euro
    Given adding a cart price rule named "credit"
    And the cart rule is active
    And the cart rule is a voucher rule with code "asdf"
    And the voucher code "asdf" is a credit voucher with credit "20000" in currency "EUR"
    And the cart rule has a action voucher credit
    And I apply the voucher code "asdf" to my cart
    Then the cart discount should be "-12000" including tax
    Then the cart discount should be "-10000" excluding tax
    Then the cart total should be "0" excluding tax
    Then the cart total should be "0" including tax

  Scenario: Add a new voucher credit rule with 200 euro and an already used credit
    Given adding a cart price rule named "credit"
    And the cart rule is active
    And the cart rule is a voucher rule with code "asdf"
    And the voucher code "asdf" is a credit voucher with credit "20000" in currency "EUR" and credit used "10000"
    And the cart rule has a action voucher credit
    And I apply the voucher code "asdf" to my cart
    Then the cart discount should be "-10000" including tax
    Then the cart discount should be "-8333" excluding tax
    Then the cart total should be "1667" excluding tax
    Then the cart total should be "2000" including tax

