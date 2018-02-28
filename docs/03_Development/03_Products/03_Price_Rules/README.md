# CoreShop Product Price Rules

CoreShop gives you the ability of very complex price calculation methods.
Price Rules always consist of Conditions and Actions.

## Price Rule Types

### Catalog Price Rules

> Checkout all available catalog price rules [here](../../../02_User_Documentation/03_Price_Rules/02_Product_Price_Rules.md)

Catalog Price Rules are applied to multiple Products based on Conditions like Category or Manufacturer.

### Specific Price Rules

> Checkout all available specific price rules [here](../../../02_User_Documentation/03_Price_Rules/03_Specific_Product_Prices.md)

Specific Price Rules are applied to a single Product based on Conditions like Customer or Customer Group.

## Extending Conditions and Actions

 - [Click here to see how you can add custom Actions](../../01_Extending_Guide/04_Extending_Rule_Actions.md)
 - [Click here to see how you can add custom Conditions](../../01_Extending_Guide/05_Extending_Rule_Conditions.md)

## Template Helper

#### Get Formatted Price with all applied Rules

```twig
{% import '@CoreShopFrontend/Common/Macro/currency.html.twig' as currency %}
{% import '@CoreShopFrontend/Common/Macro/product_price.html.twig' as product_price %}

<div class="price">
    <span class="price-head">{{ 'coreshop.ui.price'|trans }}:</span>
    {{ product_price.display_product_price(product) }}
</div>
<div class="tax">
    {{ 'coreshop_product_tax_inc'|trans|format(product|coreshop_product_tax_rate) }} ({{ currency.convertAndFormat(product|coreshop_product_tax_amount) }})
</div>
```

#### Get Active Price Rules

```twig
{{ dump(product|coreshop_product_price_rules) }}
```