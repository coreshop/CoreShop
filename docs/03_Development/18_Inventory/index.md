# Inventory

## Twig Helper

CoreShop provides Twig helpers to assist in checking the inventory of products.

### Inventory Available

This helper checks if any inventory is available for a product:

```twig
{% if coreshop_inventory_is_available(product) %}
{# show cart button since there is at least one item available #}
{% endif %}
```

### Inventory is Sufficient

This helper checks if the inventory level meets or exceeds a specified amount:

```twig
{% if coreshop_inventory_is_sufficient(product, 10) %}
{# do something here since there are at least 10 items available #}
{% endif %}
```
