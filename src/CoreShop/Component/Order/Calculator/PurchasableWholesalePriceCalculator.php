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

namespace CoreShop\Component\Order\Calculator;

use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Order\Exception\NoPurchasableWholesalePriceFoundException;
use CoreShop\Component\Order\Model\PurchasableInterface;
use Webmozart\Assert\Assert;

class PurchasableWholesalePriceCalculator implements PurchasableWholesalePriceCalculatorInterface
{
    public function __construct(
        private CurrencyConverterInterface $currencyConverter,
    ) {
    }

    public function getPurchasableWholesalePrice(PurchasableInterface $purchasable, array $context): int
    {
        Assert::keyExists($context, 'base_currency');
        Assert::isInstanceOf($context['base_currency'], CurrencyInterface::class);

        /**
         * @var CurrencyInterface $contextCurrency
         */
        $contextCurrency = $context['base_currency'];

        $wholesalePrice = $purchasable->getWholesaleBuyingPrice();

        if (!$wholesalePrice) {
            throw new NoPurchasableWholesalePriceFoundException(__CLASS__);
        }

        return $this->currencyConverter->convert($wholesalePrice->getValue(), $wholesalePrice->getCurrency()->getIsoCode(), $contextCurrency->getIsoCode());
    }
}
