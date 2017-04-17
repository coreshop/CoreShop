<?php

namespace CoreShop\Bundle\ProductBundle\Rule\Condition;

use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Webmozart\Assert\Assert;

class CategoriesConditionChecker implements ConditionCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isValid($subject, array $configuration)
    {
        Assert::isInstanceOf($subject, ProductInterface::class);

        if (!$subject instanceof ProductInterface) {
            return false;
        }

        foreach ($subject->getCategories() as $category) {
            if ($category instanceof ResourceInterface) {
                if (in_array($category->getId(), $configuration['categories'])) {
                    return true;
                }
            }
        }

        return false;
    }
}
