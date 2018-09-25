# CoreShop Cart Processor

The Cart Processor takes care about refreshing the state and prices of carts for you. Everytime the ```persistCart``` function of the [Cart-Manager](./02_Cart_Manager.md) is called
on a Cart, it gets triggered and re-calculates the cart.

Following Processors are implemented by default:

 - [Cart Adjustment Clearer](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Core/Order/Processor/CartAdjustmentClearer.php)
 - [Item Processor](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Core/Order/Processor/CartItemProcessor.php)
 - [Item Tax Processor](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Core/Order/Processor/CartItemTaxProcessor.php)
 - [Cart Price Rule Voucher Processor](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Core/Order/Processor/CartPriceRuleVoucherProcessor.php)
 - [Cart Rule Auto Processor](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Core/Order/Processor/CartRuleAutoProcessor.php)
 - [Cart Shipping Processor](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Core/Order/Processor/CartShippingProcessor.php)
 - [Cart Tax Processor](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Core/Order/Processor/CartTaxProcessor.php)

These Processors calculate all necessary prices of the cart. If you need to extend the Cart and change calculations, you need
to create a new Processor.

## Creating a Cart Processor

A Cart Processor needs to implement the Interface [```CoreShop\Component\Order\Processor\CartProcessorInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Order/Processor/CartProcessorInterface.php) and registered
into the container with the tag ```coreshop.cart_processor``` and a ```priority``` attribute.

### Example of a Cart Processor
For example, we create a Cart Processor, which calculates a custom field in our Cart.

```php
<?php

namespace AppBundle\CoreShop\Order\Cart\Processor;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;

final class CustomCartProcessor implements CartProcessorInterface
{
    public function process(CartInterface $cart)
    {
        $cart->setCustomField(uniqid());
    }
}
```

We now only need to register the class:

```yaml
app.coreshop.cart.processor.custom:
    class: AppBundle\CoreShop\Order\Cart\Processor\CustomCartProcessor
    tags:
      - { name: coreshop.cart_processor, priority: 200 }
```

On every Cart Update, our service now gets called a the custom field gets a new unique id.