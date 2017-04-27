<?php

namespace CoreShop\Bundle\CoreBundle\Cart\Rule\Condition;

use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use Webmozart\Assert\Assert;

class CurrenciesConditionChecker implements ConditionCheckerInterface
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

        if (!$subject->getCurrency() instanceof CurrencyInterface) {
            return false;
        }

        return in_array($subject->getCurrency()->getId(), $configuration['currencies']);
    }
}
