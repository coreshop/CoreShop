# CoreShop Fraction Digits
CoreShop allows you working with more than 2 digits for Currency Values. The default configuration looks like this:

```yml
core_shop_currency:
    money_decimal_precision: 2
    money_decimal_factor: 100
```

> `money_decimal_precision: 2`

Having a global precision of 2

> `money_decimal_factor: 100`

Displaying Currency with a factor of 100 (in Pimcore Backend).

> *WARNING* If you change these settings in an existing Installation with Orders/Products etc. you also have to create 
> a migration to change all the values from a precision of 2 to your new setting.

In order to handle payment values with a precision of for example 4, we introduced a new Order Property called `paymentTotal`.
Within a Payment, you cannot deal with a precision > 2, since a currency only has eg. 100 Cents. Therefore, we round the `total`
Cart/Order value to a precision of 2 into the `paymentTotal` Value.

For example:

Your Cart/Order is "€ 1.000,5498", your payment total then is: "€ 1.000,55".
