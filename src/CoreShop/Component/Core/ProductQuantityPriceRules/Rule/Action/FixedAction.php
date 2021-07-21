<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Core\ProductQuantityPriceRules\Rule\Action;

use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangeInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangePriceAwareInterface;
use CoreShop\Component\ProductQuantityPriceRules\Rule\Action\ProductQuantityPriceRuleActionInterface;
use Webmozart\Assert\Assert;

class FixedAction implements ProductQuantityPriceRuleActionInterface
{
    private CurrencyConverterInterface $currencyConverter;

    public function __construct(CurrencyConverterInterface $currencyConverter)
    {
        $this->currencyConverter = $currencyConverter;
    }

    public function calculate(QuantityRangeInterface $range, QuantityRangePriceAwareInterface $subject, int $realItemPrice, array $context): int
    {
        /**
         * @var \CoreShop\Component\Core\Model\QuantityRangeInterface $range
         */
        Assert::isInstanceOf($range, \CoreShop\Component\Core\Model\QuantityRangeInterface::class);
        Assert::isInstanceOf($range->getCurrency(), CurrencyInterface::class);
        $currentContextCurrency = $context['currency'];

        return $this->currencyConverter->convert($range->getAmount(), $range->getCurrency()->getIsoCode(), $currentContextCurrency->getIsoCode());
    }
}
