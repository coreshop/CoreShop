# CoreShop Checkout

CoreShop uses a CheckoutManager to handle Checkout steps. The default installation comes with following Steps:

 - Cart
 - Customer
 - Address
 - Shipping
 - Payment
 - Summary

## Create a Custom CheckoutManager

If you want to have a total different checkout, you can create your own CheckoutManager and register your steps there:

```yaml
services:
  acme.coreshop.checkout_manager:
    class: CoreShop\Bundle\CoreBundle\Checkout\CheckoutManager

  acme.coreshop.checkout.step.cart:
    class: CoreShop\Bundle\CoreBundle\Checkout\Step\CartCheckoutStep
    tags:
      - {name: coreshop.registry.checkout.step, type: cart, priority: 10, manager: acme.coreshop.checkout_manager }

  acme.coreshop.checkout.step.summary:
    class: CoreShop\Bundle\CoreBundle\Checkout\Step\SummaryCheckoutStep
    tags:
      - {name: coreshop.registry.checkout.step, type: summary, priority: 20, manager: acme.coreshop.checkout_manager }

  coreshop.checkout_manager:
    alias: acme.coreshop.checkout_manager
```