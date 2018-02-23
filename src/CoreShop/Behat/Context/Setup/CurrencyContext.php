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
use CoreShop\Component\Address\Context\FixedCountryContext;
use CoreShop\Component\Core\Model\CountryInterface;
use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Core\Repository\CountryRepositoryInterface;
use CoreShop\Component\Core\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Currency\Context\FixedCurrencyContext;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Doctrine\Common\Persistence\ObjectManager;

final class CurrencyContext implements Context
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
    private $currencyFactory;

    /**
     * @var CurrencyRepositoryInterface
     */
    private $currencyRepository;

    /**
     * @var FixedCurrencyContext
     */
    private $fixedCurrencyContext;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ObjectManager $objectManager
     * @param FactoryInterface $currencyFactory
     * @param CurrencyRepositoryInterface $currencyRepository
     * @param FixedCurrencyContext $fixedCurrencyContext
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ObjectManager $objectManager,
        FactoryInterface $currencyFactory,
        CurrencyRepositoryInterface $currencyRepository,
        FixedCurrencyContext $fixedCurrencyContext
    )
    {
        $this->sharedStorage = $sharedStorage;
        $this->objectManager = $objectManager;
        $this->currencyFactory = $currencyFactory;
        $this->currencyRepository = $currencyRepository;
        $this->fixedCurrencyContext = $fixedCurrencyContext;
    }

    /**
     * @Given /^the site has a currency "([^"]+)" with iso "([^"]+)"$/
     */
    public function theSiteHasACurrency($name, $iso)
    {
        $this->createCurrency($name, $iso);
    }

    /**
     * @param $name
     * @param $iso
     */
    private function createCurrency($name, $iso)
    {
        $currency = $this->currencyRepository->findOneBy(['isoCode' => $iso]);

        if (!$currency) {
            /**
             * @var $currency CurrencyInterface
             */
            $currency = $this->currencyFactory->createNew();
            $currency->setName($name);
            $currency->setIsoCode($iso);

            $this->saveCurrency($currency);
        }
    }

    /**
     * @param CurrencyInterface $currency
     */
    private function saveCurrency(CurrencyInterface $currency)
    {
        $this->objectManager->persist($currency);
        $this->objectManager->flush();
    }
}
