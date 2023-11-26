# Order Creation

Orders are usually getting created through the Checkout Step. If you ever need to create an Order manually, there are
multiple ways.

## Order CRUD

You can always use the Pimcore API to create Orders, in CoreShop you would do it through the Factory:

```php
$factory = $container->get('coreshop.factory.order')->createNew();
```

You can now fill the Order with all necessary information.