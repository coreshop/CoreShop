# Order Template/Twig Helper

CoreShop provides a Twig filter for obtaining the current state of an order.

## Order State

To get the current state of an order, use the following Twig filter:

```twig
{{ dump(order|coreshop_order_state) }}
```