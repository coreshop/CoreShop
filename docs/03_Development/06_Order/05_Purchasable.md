# CoreShop Order Purchasable

Items, you want to add to your Cart/Order/Quote, need to implement [```CoreShop\Component\Order\Model\PurchasableInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Order/Model/PurchasableInterface.php).

The concept of Purchasable allows us to decouple CoreShops Order Component from the Product Component and makes the Cart/Quote/Order more flexible
in ways of which object types can be used.

> A Purchasable does not a have Price directly
> You need create a class that implements [```CoreShop\Component\Order\Calculator\PurchasablePriceCalculatorInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Order/Calculator/PurchasablePriceCalculatorInterface.php).
> in order to calculate price

# Implementation of a new Purchasable Price Calculator
To implement a new custom Purchasable Price Calculator, you need to implement the interface [```CoreShop\Component\Order\Calculator\PurchasablePriceCalculatorInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Order/Calculator/PurchasablePriceCalculatorInterface.php).

As an example, we create a ProductSetCalculator, which takes prices of each consisting Product:

```php
<?php

namespace AppBundle\CoreShop\Order\Calculator;

use CoreShop\Component\Order\Calculator\PurchasablePriceCalculatorInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use Pimcore\Model\Product\ProductSet;

final class ProductSetCalculator implements PurchasablePriceCalculatorInterface
{
    /**
     * @var PurchasablePriceCalculatorInterface
     */
    private $purchasablePriceCalculator;

    /**
     * @param PurchasablePriceCalculatorInterface $purchasablePriceCalculator
     */
    public function __construct(PurchasablePriceCalculatorInterface $purchasablePriceCalculator)
    {
        $this->purchasablePriceCalculator = $purchasablePriceCalculator;
    }

    public function getPrice(PurchasableInterface $purchasable)
    {
        if ($purchasable instanceof ProductSet) {
            $price = 0;

            foreach ($purchasable->getProducts() as $product) {
                $price .= $this->purchasablePriceCalculator->getPrice($product);
            }

            return $price;
        }

        return null;
    }

}
```

Now we need to register our Service to the Container:

```yml
app.coreshop.order.purchasable.price_calculator.product_set:
    class: AppBundle\CoreShop\Order\Calculator\ProductSetCalculator
    arguments:
        - '@coreshop.order.purchasable.price_calculator'
    tags:
     - { name: coreshop.order.purchasable.price_calculator, type: product_set, priority: 20 }
```