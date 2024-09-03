# Cart Processor

The Cart Processor in CoreShop is responsible for refreshing the state and prices of carts. It is automatically
triggered every time the `persistCart` function of the [Cart-Manager](./08_Cart_Manager.md) is called on a cart,
ensuring that the cart is re-calculated.

The following processors are implemented by default:

- [Cart Adjustment Clearer](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Core/Order/Processor/CartAdjustmentClearer.php)
- [Item Processor](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Core/Order/Processor/CartItemProcessor.php)
- [Item Tax Processor](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Core/Order/Processor/CartItemTaxProcessor.php)
- [Cart Price Rule Voucher Processor](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Core/Order/Processor/CartPriceRuleVoucherProcessor.php)
- [Cart Rule Auto Processor](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Core/Order/Processor/CartRuleAutoProcessor.php)
- [Cart Shipping Processor](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Core/Order/Processor/CartShippingProcessor.php)
- [Cart Tax Processor](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Core/Order/Processor/CartTaxProcessor.php)

These processors handle all necessary price calculations for the cart. To extend cart calculations, a new processor
should be created.

## Creating a Cart Processor

To create a Cart Processor, implement the
interface [```CoreShop\Component\Order\Processor\CartProcessorInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Order/Processor/CartProcessorInterface.php).
Register this in the container with the tag ```coreshop.cart_processor``` and a ```priority``` attribute.

### Example of a Cart Processor

Here's an example of a custom Cart Processor that calculates a unique field for the cart:

```php
<?php

namespace App\CoreShop\Order\Cart\Processor;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;

final class CustomCartProcessor implements CartProcessorInterface
{
    public function process(OrderInterface $cart): void
    {
        $cart->setCustomField(uniqid());
    }
}
```

Registration of the processor:

```yaml
App\CoreShop\Order\Cart\Processor\CustomCartProcessor:
  tags:
    - { name: coreshop.cart_processor, priority: 200 }
```

This custom processor will now assign a new unique ID to the custom field on every cart update.