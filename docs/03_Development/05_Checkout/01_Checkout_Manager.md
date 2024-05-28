# Checkout Manager

# Checkout

CoreShop uses a CheckoutManager to handle Checkout steps. The default installation comes with following Steps:

- Cart
- Customer
- Address
- Shipping
- Payment
- Summary

## Create a Custom CheckoutManager

If you want to modify the Checkout Manager, you have two options:

- Create a total different Checkout Manager configuration
- Modify the default configuration

### Create a total different Checkout Manager

```yaml
# app/config/config.yml
core_shop_core:
    checkout_manager: my_custom_manager
    checkout:
      my_custom_manager:
        steps:
          customer:
            step: coreshop.checkout.step.customer
            priority: 10
          address:
            step: coreshop.checkout.step.address
            priority: 20
          shipping:
            step: coreshop.checkout.step.shipping
            priority: 30
          payment:
            step: coreshop.checkout.step.payment
            priority: 40
          summary:
            step: coreshop.checkout.step.summary
            priority: 50
```

### Modify the default configuration

```yaml
# app/config/config.yml
core_shop_core:
    checkout:
        default:
            steps:
              payment: false                                              # disables the payment step
              shipping: false                                             # disables the shipping step
              payment_shipping:                                           # adds a new PaymentShiping Step
                  step: app_bundle.coreshop.checkout.payment_shipping     # This is your service-id, the service needs to implement CoreShop\Component\Order\Checkout\CheckoutStepInterface
                  priority: 40                                            # Priority of this step
```