# CoreShop Installation

You can setup your own example:

* Download Plugin and place it in your plugins directory
* Download Example Template and place it in your website folder (https://github.com/coreshop/website-example)
* Open Extension Manager in Pimcore and enable/install Plugin
* After Installation within Pimcore Extension Manager, you have to reload Pimcore
* Now the CoreShop Icon will appear in the Menu
* You now have to let CoreShop install itself
* finished
* Go To http://yourdomain/en/shop

or install it via composer on an existing pimcore installation

```
composer require coreshop/core-shop dev-master
```

## Payment
Payment providers are implemented as Pimcore Plugin. They can be installed using composer. Here you can find all available payment modules via composer

- [Payunity](https://github.com/coreshop/payunity)
- [Bankwire](https://github.com/coreshop/bankwire)
- [Cash on Delivery](https://github.com/coreshop/cashondelivery)
- [Paypal](https://github.com/coreshop/paypal)
- [Sofortueberweisung](https://github.com/coreshop/sofortueberweisung)


[All available via Composer](https://packagist.org/search/?tags=coreshop-payment)