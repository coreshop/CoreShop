@ui @checkout
Feature: Ability to complete the checkout

  Background:
    Given the site operates on a store in "Austria"
    And the store "Austria" is the default store
    And configuration guest checkout is enabled
    And the site has a product "TShirt" priced at 10000
    And the product is active and published and available for store "Austria"
    And the site has a carrier "Post" and ships for 10 in currency "EUR"
    And the site has a payment provider "Bankwire" using factory "offline"
    Then I add this product to the cart

  Scenario: I proceed to the checkout
    Given I am at the customer checkout step
    Then I should be on the customer checkout step
    And I specify the guest checkout firstname "Max"
    And I specify the guest checkout lastname "Mustermann"
    And I specify the guest checkout email address "max@mustermann.at"
    And I specify the guest checkout address firstname "Mustermann"
    And I specify the guest checkout address lastname "Mustermann"
    And I specify the guest checkout address street "Musterstraße"
    And I specify the guest checkout address number "1"
    And I specify the guest checkout address postcode "1234"
    And I specify the guest checkout address city "Musterstadt"
    And I specify the guest checkout address country "Austria"
    And I specify the guest checkout address phone number "+00 000000"
    Then I submit the guest checkout
    Then I should be on the address checkout step
    And I submit the address step
    Then I should be on the shipping checkout step
    When I submit the shipping step
    Then I should be on the payment checkout step
    And I select the payment provider "Bankwire"
    When I submit the payment step
    Then I should be on the summary checkout step
    When I accept the checkout terms of service
    And I submit the order
    Then I should be on the thank you page
