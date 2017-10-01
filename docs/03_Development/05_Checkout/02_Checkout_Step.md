# CoreShop Checkout Step

If you want to implement a custom checkout step, you need to implement the interface ```CoreShop\Component\Order\Checkout\CheckoutStepInterface```
and register your step into your Cart Manager:

```yaml
# app/config/config.yml
core_shop_core:
    checkout:
      default:
        steps:
          custom:
            step: app.coreshop.checkout.custom
            priority: 50
```

The [Checkout Controller](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/FrontendBundle/Controller/CheckoutController.php#L44) takes care about handling
the Checkout for you then.