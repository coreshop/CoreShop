# CoreShop Configuration Component

Configuration Component helps you store your configurations in database.

```php
    $service = new CoreShop\Component\Configuration\Service\ConfigurationService($doctrineEntityManager, $configRepo, $configFactory);
    $service->set('key', 'value');

    $service->get('key');
```