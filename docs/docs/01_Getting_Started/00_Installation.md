# CoreShop Installation

You need a running instance of Pimcore on your system before you can install CoreShop. 

You can setup your own example:
 - Install with composer ```composer require coreshop/core-shop ^3.0```
 - Run enable Bundle command
    ```php bin/console pimcore:bundle:enable CoreShopCoreBundle```
 - Run Install Command
    `php bin/console coreshop:install`
 - Optional: Install Demo Data `php bin/console coreshop:install:demo`

## Messenger
CoreShop also uses Symfony Messenger for async tasks like sending E-Mails or Processing DataObjects for the Index. Please run these 2 transports to process the data
```
bin/console messenger:consume coreshop_notification coreshop_index --time-limit=300
```

## Payment
CoreShop uses Payum for Payment. Checkout Payums Documentation on how to add payment providers.

Payment providers are implemented as Pimcore Plugin. They can be installed using composer. Here you can find all available payment modules via composer

[Payum Documentation](https://github.com/Payum/Payum/blob/master/docs/index.md#symfony-payum-bundle)
