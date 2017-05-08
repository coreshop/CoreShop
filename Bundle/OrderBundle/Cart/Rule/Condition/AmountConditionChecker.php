<?php

namespace CoreShop\Bundle\OrderBundle\Cart\Rule\Condition;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use Webmozart\Assert\Assert;

class AmountConditionChecker implements ConditionCheckerInterface
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

        if ($configuration['minAmount'] > 0) {
            $minAmount = $configuration['minAmount'];

            $cartTotal = $subject->getSubtotal();

            if ($minAmount > $cartTotal) {
                return false;
            }
        }

        if ($configuration['maxAmount'] > 0) {
            $maxAmount = $configuration['maxAmount'];

            $cartTotal = $subject->getSubtotal();

            if ($maxAmount < $cartTotal) {
                return false;
            }
        }

        return true;
    }
}
