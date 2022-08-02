@ui @checkout
Feature: Ability to complete the checkout

  Background:
    Given the site operates on a store in "Austria"
    And the store "Austria" is the default store
    And the site has a product "TShirt" priced at 10000
    And the product is active and published and available for store "Austria"
    And I am a logged in customer
    And the customer has an address with country "Austria", "4600", "Wels", "Freiung", "9-11/N3"
    And the site has a carrier "Post" and ships for 10 in currency "EUR"
    And the site has a carrier "DHL" and ships for 15 in currency "EUR"
    And the carrier "DHL" is disabled for store "Austria"
    Then I add this product to the cart

  Scenario: I proceed to the checkout
    Given I am at the address checkout step
    Then I should be on the address checkout step
    And I use the last address as invoice address
    And I submit the address step
    Then I should be on the shipping checkout step
    And I should not see carrier "DHL"
