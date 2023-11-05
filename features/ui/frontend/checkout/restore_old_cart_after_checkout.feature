@ui @checkout
Feature: Ability to restore an old after a successful checkout

  Background:
    Given the site operates on a store in "Austria"
    And the store "Austria" is the default store
    And the site has a product "TShirt" priced at 10000
    And the product is active and published and available for store "Austria"
    And the site has a product "Mug" priced at 1000
    And the product is active and published and available for store "Austria"
    And I am a logged in customer
    And the customer has an address with country "Austria", "4600", "Wels", "Freiung", "9-11/N3"
    And the site has a carrier "Post" and ships for 10 in currency "EUR"
    And the site has a payment provider "Bankwire" using factory "offline"
    Then I add product "TShirt" to the cart
    And I log out
    Then I add product "Mug" to the cart
    Given I want to log in
    And I specify the username as "coreshop@pimcore.org"
    And I specify the password as "coreshop"
    And I log in
    Given I see the summary of my cart
    Then I should see "Mug" with unit price "€10.00" in my cart

  Scenario: I proceed the checkout and create the Order
    Given I am at the address checkout step
    And I use the last address as invoice address
    And I submit the address step
    When I submit the shipping step
    And I select the payment provider "Bankwire"
    When I submit the payment step
    When I accept the checkout terms of service
    And I submit the order
    Given I see the summary of my cart
    Then I should see "TShirt" with unit price "€100.00" in my cart
