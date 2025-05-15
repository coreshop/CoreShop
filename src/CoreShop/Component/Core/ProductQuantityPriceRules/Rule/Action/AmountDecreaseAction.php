<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Core\ProductQuantityPriceRules\Rule\Action;

use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangeInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangePriceAwareInterface;
use CoreShop\Component\ProductQuantityPriceRules\Rule\Action\ProductQuantityPriceRuleActionInterface;
use Webmozart\Assert\Assert;

class AmountDecreaseAction implements ProductQuantityPriceRuleActionInterface
{
    public function __construct(
        private CurrencyConverterInterface $currencyConverter,
    ) {
    }

    public function calculate(QuantityRangeInterface $range, QuantityRangePriceAwareInterface $subject, int $realItemPrice, array $context): int
    {
        /**
         * @var \CoreShop\Component\Core\Model\QuantityRangeInterface $range
         */
        Assert::isInstanceOf($range, \CoreShop\Component\Core\Model\QuantityRangeInterface::class);
        Assert::isInstanceOf($range->getCurrency(), CurrencyInterface::class);
        $currentContextCurrency = $context['base_currency'];
        $currencyAwareAmount = $this->currencyConverter->convert($range->getAmount(), $range->getCurrency()->getIsoCode(), $currentContextCurrency->getIsoCode());

        return max($realItemPrice - $currencyAwareAmount, 0);
    }
}
