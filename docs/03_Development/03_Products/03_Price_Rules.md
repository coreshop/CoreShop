# CoreShop Product Price Rules

### Templating

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