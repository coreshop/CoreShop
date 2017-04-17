<?php

namespace CoreShop\Bundle\CoreBundle\Product\ProductPriceRule\Condition;

use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use Webmozart\Assert\Assert;

class CurrenciesConditionChecker implements ConditionCheckerInterface
{
    /**
     * @var CurrencyContextInterface
     */
    private $currencyContext;

    /**
     * @param CurrencyContextInterface $currencyContext
     */
    public function __construct(CurrencyContextInterface $currencyContext)
    {
        $this->currencyContext = $currencyContext;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($subject, array $configuration)
    {
        Assert::isInstanceOf($subject, ProductInterface::class);

        $currency = $this->currencyContext->getCurrency();

        if (!$currency instanceof CurrencyInterface) {
            return false;
        }

        return in_array($currency->getId(), $configuration['currencies']);
    }
}
