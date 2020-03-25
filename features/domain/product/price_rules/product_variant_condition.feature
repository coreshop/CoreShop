@product @domain
Feature: Adding a new Product with Variants
  In order to extend my catalog
  The catalog has a price-rule for a product and allow variants
  I want to create a new product

  Background:
    Given the site operates on a store in "Austria"
    Given the site has a currency "Euro" with iso "EUR"
    Given I am in country "Austria"
    Given the site has two categories "Shoes" and "Coats"
    Given the site has a product "Shoe" priced at 100
    Given the product "Shoe" has a variant "Shoe Variant" priced at 100
    Given it is in category "Shoes"
    Given the site has a product "Shoe 2" priced at 150
    Given the product "Shoe 2" has a variant "Shoe 2 Variant" priced at 150
    Given it is in category "Shoes"
    Given the site has a product "Jacket" priced at 400
    Given the product "Jacket" has a variant "Jacket Variant" priced at 400
    Given it is in category "Coats"
    Then the product "Shoe" should be priced at "100"
    Then the product "Shoe 2" should be priced at "150"
    Then the product "Jacket" should be priced at "400"

  Scenario: Add a new product catalog price rule which is valid
    Given adding a product price rule named "product-discount"
    And the price rule is active
    And the price rule has a condition products with product "Shoe" which includes variants
    Then the price rule should be valid for product "Shoe Variant"
    And the price rule should be invalid for product "Shoe 2 Variant"
    And the price rule should be invalid for product "Jacket Variant"

  Scenario: Add a new product catalog price rule which is valid for two products
    Given adding a product price rule named "product-discount"
    And the price rule is active
    And the price rule has a condition products with product "Shoe" and product "Jacket" which includes variants
    Then the price rule should be valid for product "Shoe Variant"
    And the price rule should be valid for product "Jacket Variant"
    And the price rule should be invalid for product "Shoe 2 Variant"
