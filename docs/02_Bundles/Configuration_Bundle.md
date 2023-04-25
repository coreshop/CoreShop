# CoreShop Configuration Bundle

## Installation
```bash
$ composer require coreshop/configuration-bundle:^3.0
```

## Usage

Configuration Component helps you store your configurations in database.

```php
    $service = new CoreShop\Component\Configuration\Service\ConfigurationService($doctrineEntityManager, $configRepo, $configFactory);
    $service->set('key', 'value');

    $service->get('key');
```