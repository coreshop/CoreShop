<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Currency\Model\ExchangeRateInterface;
use CoreShop\Component\Currency\Repository\ExchangeRateRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Doctrine\Common\Persistence\ObjectManager;

final class ExchangeRateContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var FactoryInterface
     */
    private $exchangeRateFactory;

    /**
     * @var ExchangeRateRepositoryInterface
     */
    private $exchangeRateRepository;

    /**
     * @param SharedStorageInterface          $sharedStorage
     * @param ObjectManager                   $objectManager
     * @param FactoryInterface                $exchangeRateFactory
     * @param ExchangeRateRepositoryInterface $exchangeRateRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ObjectManager $objectManager,
        FactoryInterface $exchangeRateFactory,
        ExchangeRateRepositoryInterface $exchangeRateRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->objectManager = $objectManager;
        $this->exchangeRateFactory = $exchangeRateFactory;
        $this->exchangeRateRepository = $exchangeRateRepository;
    }

    /**
     * @Given /^the (currency "[^"]+") has a exchange-rate to (currency "[^"]+") of "([^"]+)"$/
     * @Given /^the (currency) has a exchange-rate to (currency "[^"]+") of "([^"]+)"$/
     */
    public function currencyHasExchangeRateTo(CurrencyInterface $fromCurrency, CurrencyInterface $toCurrency, float $rate)
    {
        /**
         * @var ExchangeRateInterface
         */
        $exchangeRate = $this->exchangeRateFactory->createNew();
        $exchangeRate->setFromCurrency($fromCurrency);
        $exchangeRate->setToCurrency($toCurrency);
        $exchangeRate->setExchangeRate(floatval($rate));

        $this->objectManager->persist($exchangeRate);
        $this->objectManager->flush();
    }
}
