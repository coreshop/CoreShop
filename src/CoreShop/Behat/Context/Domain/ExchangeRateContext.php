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

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use Webmozart\Assert\Assert;

final class ExchangeRateContext implements Context
{
    public function __construct(
        private CurrencyConverterInterface $currencyConverter,
    ) {
    }

    /**
     * @Then /^price "([^"]+)" of (currency "[^"]+") should exchange to price "([^"]+)" in (currency "[^"]+")$/
     */
    public function priceOfCurrencyShouldExchangeToPriceInCurrency($fromPrice, CurrencyInterface $fromCurrency, $toPrice, CurrencyInterface $toCurrency): void
    {
        Assert::same(
            $this->currencyConverter->convert((int) $fromPrice, $fromCurrency->getIsoCode(), $toCurrency->getIsoCode()),
            (int) $toPrice,
            sprintf(
                'Given exchanged value (%s %s) is different from actual value (%s %s)',
                $fromPrice,
                $fromCurrency->getIsoCode(),
                $toPrice,
                $toCurrency->getIsoCode(),
            ),
        );
    }
}
