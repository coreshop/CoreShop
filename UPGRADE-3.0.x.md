# Upgrade to 3.0

## Price Twig Helper changed signature 

from

```
product|coreshop_product_price(with_tax, coreshop.context)
product|coreshop_product_retail_price(with_tax, coreshop.context)
product|coreshop_product_discount(with_tax, coreshop.context)
product|coreshop_product_discount_price(with_tax, coreshop.context)
```

to

```
product|coreshop_product_price(coreshop.context, with_tax)
product|coreshop_product_retail_price(coreshop.context, with_tax)
product|coreshop_product_discount(coreshop.context, with_tax)
product|coreshop_product_discount_price(coreshop.context, with_tax)
```
