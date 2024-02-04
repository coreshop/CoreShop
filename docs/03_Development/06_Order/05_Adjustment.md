# Adjustments

Adjustments in CoreShop provide a mechanism to add extra costs or discounts to your Order, such as Taxes, Shipping, or
Discounts.

## Add a New Adjustment

The best practice for adding or removing adjustments is within Cart Processors. More information about Cart Processors
can be found [here](./04_Cart_Processor.md).

To add a new Adjustment, you should create a new Adjustment Model that implements the
interface [`CoreShop\Component\Order\Model\AdjustmentInterface`]. Use the
Factory [`CoreShop\Component\Order\Factory\AdjustmentFactoryInterface`] to create a new Adjustment.

```php
/**
 * @var AdjustmentFactoryInterface $adjustmentFactory
 */
$adjustmentFactory = $this->container->get('coreshop.factory.adjustment');

$type = "custom-adjustment";
$label = "Label for Frontend";
$amountGross = 10000; //100.00
$amountNet = 8000; //80.00
$neutral = false; //If neutral, it doesn't affect the total amount

$adjustment = $this->adjustmentFactory->createWithData(
    $type,
    $label,
    $amountGross,
    $amountNet,
    $neutral,
);

//Adjustments can be added to the Cart or to a Cart Item
$cart->addAdjustment($adjustment);
$cartItem->addAdjustment($adjustment);
```

Adjustments can be applied either to the Cart as a whole or to individual Cart Items.
