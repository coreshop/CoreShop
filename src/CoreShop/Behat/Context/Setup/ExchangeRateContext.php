<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Currency\Model\ExchangeRateInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Doctrine\Persistence\ObjectManager;

final class ExchangeRateContext implements Context
{
    public function __construct(private ObjectManager $objectManager, private FactoryInterface $exchangeRateFactory)
    {
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
