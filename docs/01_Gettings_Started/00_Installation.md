# CoreShop Installation

You can setup your own example:

 - Install with composer ```composer require coreshop/core-shop dev-master```
 - Add Following Call to AppKernel's registerBundlesToCollection function
    ```php
        \CoreShop\Bundle\CoreBundle\Application\RegisterBundleHelper::registerBundles($collection);
    ```
 - Add Following to your app/config/config.yml
    ```yaml
        imports:
            - { resource: "@CoreShopCoreBundle/Resources/config/app/config.yml" }
    ```
 - Run Install Command
    ```php bin/console coreshop:install```
 - Optional: Install Demo Data ```php bin/console coreshop:install:demo```

## Payment
CoreShop uses Payum for Payment. Checkout Payums Documentation on how to add payment providers.

Payment providers are implemented as Pimcore Plugin. They can be installed using composer. Here you can find all available payment modules via composer

[Payum Documentation](https://github.com/Payum/Payum/blob/master/docs/index.md#symfony-payum-bundle)