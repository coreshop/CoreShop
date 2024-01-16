@ui @checkout
Feature: Use order tokens to capture payments

  # Checkout completed successfully
  Background:
    Given the site operates on a store in "Austria"
    And the store "Austria" is the default store
    And the site has a product "TShirt" priced at 10000
    And the product is active and published and available for store "Austria"
    And I am a logged in customer
    And the customer has an address with country "Austria", "4600", "Wels", "Freiung", "9-11/N3"
    And the site has a carrier "Post" and ships for 10 in currency "EUR"
    And the site has a payment provider "Bankwire" using factory "offline"
    And I add this product to the cart
    And I am at the address checkout step
    And I should be on the address checkout step
    And I use the last address as invoice address
    And I submit the address step
    And I should be on the shipping checkout step
    And I submit the shipping step
    And I should be on the payment checkout step
    And I select the payment provider "Bankwire"
    And I submit the payment step
    And I should be on the summary checkout step
    And I accept the checkout terms of service
    And I submit the order
    And I should be on the thank you page

  Scenario: Re-visiting pay URL should work with order token
    Given I should be on the thank you page
    Then I re-capture payment for same order
    And I should be on the thank you page
