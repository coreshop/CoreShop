# Order Number Generator

CoreShop generates a unique number for each completed order. This generation is handled by
the `CoreShop\Component\Core\Order\Committer\OrderCommitter`, utilizing a `NumberGeneratorInterface` service.

If the CoreBundle is installed, the `coreshop.order.number_generator.prefix_suffix` service is used. This service
decorates the `coreshop.order.number_generator.default` and adds the capability to manage a prefix and suffix through
CoreShop settings.

To implement a custom number generator, create a service that decorates the `coreshop.order.number_generator.default`.
The `decoration_priority` setting determines the order in which decorators are called.

- A `decoration_priority` higher or equal to zero results in this
  order: `PrefixSuffix Generator > CustomNumberGenerator > DefaultSequenceGenerator`.
- A `decoration_priority` below zero changes the order
  to: `CustomNumberGenerator > PrefixSuffix Generator > DefaultSequenceGenerator`.

### Example: Custom Number Generator with Padding

This example shows a custom implementation that adds padding to the number:

```php
final class CustomNumberGenerator implements NumberGeneratorInterface
{
    public function __construct(
        private NumberGeneratorInterface $numberGenerator, // decorated service
    ) {
    
    }

    public function generate(ResourceInterface $model): string
    {
        $number = $this->numberGenerator->generate($model);

        return str_pad($number, 10, '0', STR_PAD_LEFT);
    }
}
```

Symfony service definition:

```yaml
  App\CoreShop\Order\CustomNumberGenerator:
    decorates: coreshop.order.number_generator.default
    decoration_priority: 1
    arguments:
      - '@.inner'
```

This format should provide clarity on the customization of the Order Number Generator in CoreShop. If you have more
sections to work on or need further adjustments, please let me know!
