<?php

namespace CoreShop\Bundle\OrderBundle\Cart\Rule\Condition;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use Webmozart\Assert\Assert;

class CategoriesConditionChecker implements ConditionCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isValid($subject, array $configuration)
    {
        Assert::isInstanceOf($subject, CartInterface::class);

        if (!$subject instanceof CartInterface) {
            return false;
        }

        foreach ($subject->getItems() as $item) {
            $product = $item->getProduct();

            if ($item instanceof ProductInterface) {
                foreach ($product->getCategories() as $category) {
                    if ($category instanceof ResourceInterface) {
                        if (in_array($category->getId(), $configuration['categories'])) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }
}
