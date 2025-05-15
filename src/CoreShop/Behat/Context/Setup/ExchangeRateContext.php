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

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Currency\Model\ExchangeRateInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Doctrine\Persistence\ObjectManager;

final class ExchangeRateContext implements Context
{
    public function __construct(
        private ObjectManager $objectManager,
        private FactoryInterface $exchangeRateFactory,
    ) {
    }

    /**
     * @Given /^the (currency "[^"]+") has a exchange-rate to (currency "[^"]+") of "([^"]+)"$/
     * @Given /^the (currency) has a exchange-rate to (currency "[^"]+") of "([^"]+)"$/
     */
    public function currencyHasExchangeRateTo(CurrencyInterface $fromCurrency, CurrencyInterface $toCurrency, float $rate): void
    {
        /**
         * @var ExchangeRateInterface $exchangeRate
         */
        $exchangeRate = $this->exchangeRateFactory->createNew();
        $exchangeRate->setFromCurrency($fromCurrency);
        $exchangeRate->setToCurrency($toCurrency);
        $exchangeRate->setExchangeRate($rate);

        $this->objectManager->persist($exchangeRate);
        $this->objectManager->flush();
    }
}
