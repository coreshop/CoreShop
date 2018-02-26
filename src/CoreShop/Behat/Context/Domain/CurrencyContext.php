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

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Core\Repository\ProductRepositoryInterface;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Customer\Context\CustomerContextInterface;
use CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Pimcore\Model\DataObject\Folder;
use Webmozart\Assert\Assert;

final class CurrencyContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var CurrencyRepositoryInterface
     */
    private $currencyRepository;

    /**
     * @var CurrencyContextInterface
     */
    private $currencyContext;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param CurrencyRepositoryInterface $currencyRepository
     * @param CurrencyContextInterface $currencyContext
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        CurrencyRepositoryInterface $currencyRepository,
        CurrencyContextInterface $currencyContext
    )
    {
        $this->sharedStorage = $sharedStorage;
        $this->currencyRepository = $currencyRepository;
        $this->currencyContext = $currencyContext;
    }

    /**
     * @Then /^the site should be using (currency "[^"]+")$/
     */
    public function theSiteShouldBeUsingCurrency(CurrencyInterface $currency)
    {
        Assert::same(
            $currency->getId(),
            $this->currencyContext->getCurrency()->getId(),
            sprintf(
                'Given currency (%s) is different from actual currency(%s)',
                $currency->getIsoCode(),
                $this->currencyContext->getCurrency()->getIsoCode()
            )
        );
    }

        /**
     * @Then /^the (store "[^"]+") should have "([^"]+)" currencies$/
     */
    public function theStoreShouldHaveXCurrencies(StoreInterface $store, $countOfCurrencies)
    {
        $validCurrencies = $this->currencyRepository->findActiveForStore($store);

        Assert::same(
            count($validCurrencies),
            intval($countOfCurrencies),
            sprintf(
                'Found %s valid currencies insteaof of %s',
                count($validCurrencies),
                intval($countOfCurrencies)
            )
        );
    }
}
