<?php

namespace CoreShop\Bundle\CoreBundle\Cart\Rule\Condition;

use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use Webmozart\Assert\Assert;

class CarriersConditionChecker implements ConditionCheckerInterface
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

        if (!$subject->getCarrier() instanceof CarrierInterface) {
            return false;
        }

        return in_array($subject->getCarrier()->getId(), $configuration['carriers']);
    }
}
