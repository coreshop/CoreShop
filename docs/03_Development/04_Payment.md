# CoreShop Payment

# TODO

CoreShop uses Pimcore Plugins for Payment.

You can create your own Payment Plugin by implementing following the class CoreShop\Bundle\LegacyBundle\Model\Plugin\Payment and the Controller CoreShop\Bundle\LegacyBundle\Controller\Action\Payment.

To notify CoreShop that a Payment Plugin is available you need to hook into "payment.getProvider" by:

```php
Pimcore::getEventManager()->attach("payment.getProvider", function($e) {
   return CoreShop\Bundle\LegacyBundle\Model\Plugin\Payment; //Your Payment Class
});
```

You can take a look at the example payment Providers:

- [Payunity](https://github.com/coreshop/payunity)
- [Bankwire](https://github.com/coreshop/bankwire)
- [Cash on Delivery](https://github.com/coreshop/cashondelivery)
- [Paypal](https://github.com/coreshop/paypal)
- [Sofortueberweisung](https://github.com/coreshop/sofortueberweisung)