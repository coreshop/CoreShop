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

## PHP Template Engine Helpers removed
The PHP Engine has been deprecated by Symfony. Since that, we don't have plans to further support it as well.

## CoreShop\Component\Index\Model\IndexableInterface Signature changed

From

```php
    public function getIndexable();
    public function getIndexableEnabled();
    public function getIndexableName($language);
```

To

```php
    public function getIndexable(IndexInterface $index): bool
    public function getIndexableEnabled(IndexInterface $index): bool
    public function getIndexableName(IndexInterface $index, string $language): string
```


