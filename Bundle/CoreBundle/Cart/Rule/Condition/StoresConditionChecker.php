<?php

namespace CoreShop\Bundle\CoreBundle\Cart\Rule\Condition;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use Webmozart\Assert\Assert;

class StoresConditionChecker implements ConditionCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isValid($subject, array $configuration)
    {
        /**
         * @var $subject CartInterface
         */
        Assert::isInstanceOf($subject, CartInterface::class);

        //TODO: Cart needs to be StoreAware

        return false;
    }
}
