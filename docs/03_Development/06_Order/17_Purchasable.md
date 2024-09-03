# Order Purchasable

For items to be added to a Cart, Order, or Quote in CoreShop, they must implement the
interface [```CoreShop\Component\Order\Model\PurchasableInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Order/Model/PurchasableInterface.php).
This concept of Purchasable decouples CoreShop's Order Component from the Product Component, offering flexibility in the
types of objects that can be used in the Cart, Quote, or Order.

> **Note**: A Purchasable item does not have a direct price. Instead, create a class that
> implements [```CoreShop\Component\Order\Calculator\PurchasablePriceCalculatorInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Order/Calculator/PurchasablePriceCalculatorInterface.php)
> to calculate its price.

## Implementation of a New Purchasable Price Calculator

To implement a new custom Purchasable Price Calculator, adhere to the
interface [```CoreShop\Component\Order\Calculator\PurchasablePriceCalculatorInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Order/Calculator/PurchasablePriceCalculatorInterface.php).

### Example: ProductSetCalculator

This example creates a `ProductSetCalculator`, which calculates prices for each product in a set:

```php
<?php

namespace App\CoreShop\Order\Calculator;

use CoreShop\Component\Order\Calculator\PurchasablePriceCalculatorInterface;use Pimcore\Model\Product\ProductSet;

final class ProductSetCalculator implements PurchasablePriceCalculatorInterface
{
    // Implementation of methods
}
```

Service registration in the container:

```yml
App\CoreShop\Order\Calculator\ProductSetCalculator:
    arguments:
        - '@coreshop.order.purchasable.price_calculator'
    tags:
     - { name: coreshop.order.purchasable.price_calculator, type: product_set, priority: 20 }
````

This format provides detailed guidance on implementing and integrating a custom Purchasable Price Calculator in
CoreShop. If there are more sections to work on or specific adjustments needed, please let me know!
