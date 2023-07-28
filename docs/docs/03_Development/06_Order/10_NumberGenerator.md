# CoreShop Order Number Generator

When an order is completed, a number is generated for that order. This happens at the `CoreShop\Component\Core\Order\Committer\OrderCommitter`, which uses a `NumberGeneratorInterface` service.

If the CoreBundle is installed, the `coreshop.order.number_generator.prefix_suffix` service is injected. It decorates the `coreshop.order.number_generator.default` and extends it with the feature to manage a prefix and suffix via the CoreShop settings.

To create a custom number generator, a service must be created which also decorates the `coreshop.order.number_generator.default`. To set the order of the decorations, use `decoration_priority`.

Choosing `decoration_priority` higher or equal zero, the order of called decoraters would be  `PrefixSuffix Generator > CustomNumberGenerator > DefaultSequenceGenerator`.
If you choose a `decoration_priority` below zero, the order of called decoraters would change to `CustomNumberGenerator > PrefixSuffix Generator > DefaultSequenceGenerator`.

Example for custom implementation of NumberGenerator, that adds padding to the number

```php
final class CustomNumberGenerator implements NumberGeneratorInterface
{
    public function __construct(
        private NumberGeneratorInterface $numberGenerator, # decorated service
    ) {
    }

    public function generate(ResourceInterface $model): string
    {
        $number = $this->numberGenerator->generate($model);

        return str_pad($number, 10, '0', STR_PAD_LEFT);
    }
}
```

Symfony service definition

```yaml
  App\Order\CustomNumberGenerator:
    decorates: coreshop.order.number_generator.default
    decoration_priority: 1
    arguments:
      - '@.inner'
```
