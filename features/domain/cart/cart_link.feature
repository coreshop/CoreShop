@domain @cart
Feature: In order that a customer can visit the cart-summary page
  The website needs to create a URL

  Scenario: Create URL for cart-summary
    Then the generated url for route "coreshop_cart_summary" should be "/en/shop/cart"
