<?php

namespace CoreShop\Bundle\OrderBundle\Cart\Rule\Condition;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use Webmozart\Assert\Assert;

class ProductsConditionChecker implements ConditionCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isValid($subject, array $configuration)
    {
        Assert::isInstanceOf($subject, CartInterface::class);

        foreach ($subject->getItems() as $item) {
            $product = $item->getProduct();

            if ($product instanceof ProductInterface) {
                if (in_array($product->getId(), $configuration['products'])) {
                    return true;
                }
            }
        }

        return false;
    }
}
