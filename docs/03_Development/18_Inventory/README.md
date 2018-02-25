# CoreShop Inventory

## Twig Helper
There are some Helpers to check the product inventory.

### Inventory Available

```twig
{% if coreshop_inventory_is_available(product) %}
    {# show cart button since there is at least one item available #}
{% endif %}
```

### Inventory is Sufficient
```twig
{% if coreshop_inventory_is_sufficient(product, 10) %}
    {# do something here since there are at least 10 items available #}
{% endif %}
```
