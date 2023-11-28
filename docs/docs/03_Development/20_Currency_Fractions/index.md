# Fraction Digits

CoreShop allows you to work with more than two digits for currency values. The default configuration looks like this:

```yml
core_shop_currency:
money_decimal_precision: 2
money_decimal_factor: 100
```

- `money_decimal_precision: 2`: This setting specifies a global precision of 2.
- `money_decimal_factor: 100`: This setting is for displaying currency with a factor of 100 in the Pimcore Backend.

> *WARNING*: If you change these settings in an existing installation with orders, products, etc., you also need to
> create a migration to change all the values from a precision of 2 to your new setting.

For handling payment values with a precision of, for example, 4, a new order property called `paymentTotal` is
introduced. In payments, dealing with a precision greater than 2 isn't feasible since a currency only has, for example,
100 cents. Therefore, the `total` Cart/Order value is rounded to a precision of 2 in the `paymentTotal` value.

For example, if your Cart/Order total is "€ 1,000.5498", the payment total would be "€ 1,000.55".

To display the payment total in your cart, you can use this template:

```twig
{% if currency.convertAndFormat(cart.total) != currency.convertAndFormat(cart.paymentTotal, 2, 100) %}
<tr>
    <td class="text-right" colspan="3">	
        <strong>{{ 'coreshop.ui.payment_total'|trans }}:</strong>
    </td>
    <td colspan="2" class="text-right cart-total-payment-price">
        {{ currency.convertAndFormat(cart.paymentTotal, 2, 100) }}
    </td>
</tr>
{% endif %}
```
