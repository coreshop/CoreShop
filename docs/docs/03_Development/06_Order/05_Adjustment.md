# Adjustments

Adjustments are a way to add additional costs or discounts to your Order. They are used for example for Taxes, Shipping or Discounts.

## Add a new Adjustment

It is best practice to add/remove adjustments within Cart Processors. You can find more information about Cart Processors [here](./07_Cart_Processor.md).

To add a new Adjustment, you need to create a new Adjustment Model, which implements the interface [```CoreShop\Component\Order\Model\AdjustmentInterface```]. You should use the Factory [```CoreShop\Component\Order\Factory\AdjustmentFactoryInterface```] to create a new Adjustment.

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