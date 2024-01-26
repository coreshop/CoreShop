# Product Price Rules

CoreShop provides advanced capabilities for complex price calculation methods. Price Rules are structured around
Conditions and Actions.

## Price Rule Types

### Product Price Rules

> Find detailed information on product price
> rules [here](../../../02_User_Documentation/03_Price_Rules/02_Product_Price_Rules.md).  
> Product price rules are applicable to multiple products and are based on conditions like category or manufacturer.

### Specific Price Rules

> Explore specific price rules [here](../../../02_User_Documentation/03_Price_Rules/03_Specific_Price_Rules.md).  
> Specific price rules target a single product, with conditions based on factors like customer or customer group.

### Quantity Price Rules

> For quantity price rules, click [here](../../../02_User_Documentation/03_Price_Rules/04_Quantity_Price_Rules.md).  
> These rules apply to a single product and are based on the quantity of a cart item. They only affect cart item prices,
> and the default price calculation in CoreShop will return zero outside the cart context if only quantity rules are
> configured.

## Extending Conditions and Actions

- To add custom Actions, [click here](../../01_Extending_Guide/04_Extending_Rule_Actions.md).
- To add custom Conditions, [click here](../../01_Extending_Guide/05_Extending_Rule_Conditions.md).

## Template Helper

### Get Formatted Price with all applied Rules

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

### Get Active Price Rules

```twig
{{ dump(product|coreshop_product_price_rules) }}
```
