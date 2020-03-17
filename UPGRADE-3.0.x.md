# Upgrade to 3.0

# CoreShop\Component\Index\Model\IndexableInterface Signature changed

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


