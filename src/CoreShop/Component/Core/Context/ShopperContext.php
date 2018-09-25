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

namespace CoreShop\Component\Core\Context;

use CoreShop\Component\Address\Context\CountryContextInterface;
use CoreShop\Component\Address\Context\CountryNotFoundException;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Currency\Context\CurrencyNotFoundException;
use CoreShop\Component\Customer\Context\CustomerContextInterface;
use CoreShop\Component\Customer\Context\CustomerNotFoundException;
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Locale\Context\LocaleNotFoundException;
use CoreShop\Component\Order\Context\CartContextInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Store\Context\StoreNotFoundException;

class ShopperContext implements ShopperContextInterface
{
    /**
     * @var StoreContextInterface
     */
    private $storeContext;

    /**
     * @var CurrencyContextInterface
     */
    private $currencyContext;

    /**
     * @var LocaleContextInterface
     */
    private $localeContext;

    /**
     * @var CountryContextInterface
     */
    private $countryContext;

    /**
     * @var CustomerContextInterface
     */
    private $customerContext;

    /**
     * @var CartContextInterface
     */
    private $cartContext;

    /**
     * @param StoreContextInterface    $storeContext
     * @param CurrencyContextInterface $currencyContext
     * @param LocaleContextInterface   $localeContext
     * @param CountryContextInterface  $countryContext
     * @param CustomerContextInterface $customerContext
     * @param CartContextInterface     $cartContext
     */
    public function __construct(
        StoreContextInterface $storeContext,
        CurrencyContextInterface $currencyContext,
        LocaleContextInterface $localeContext,
        CountryContextInterface $countryContext,
        CustomerContextInterface $customerContext,
        CartContextInterface $cartContext
    ) {
        $this->storeContext = $storeContext;
        $this->currencyContext = $currencyContext;
        $this->localeContext = $localeContext;
        $this->countryContext = $countryContext;
        $this->customerContext = $customerContext;
        $this->cartContext = $cartContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getStore()
    {
        return $this->storeContext->getStore();
    }

    /**
     * {@inheritdoc}
     */
    public function hasStore()
    {
        try {
            $this->storeContext->getStore();

            return true;
        } catch (StoreNotFoundException $ex) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        return $this->currencyContext->getCurrency();
    }

    /**
     * {@inheritdoc}
     */
    public function hasCurrency()
    {
        try {
            $this->currencyContext->getCurrency();

            return true;
        } catch (CurrencyNotFoundException $ex) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLocaleCode()
    {
        return $this->localeContext->getLocaleCode();
    }

    /**
     * {@inheritdoc}
     */
    public function hasLocaleCode()
    {
        try {
            $this->localeContext->getLocaleCode();

            return true;
        } catch (LocaleNotFoundException $ex) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCountry()
    {
        return $this->countryContext->getCountry();
    }

    /**
     * {@inheritdoc}
     */
    public function hasCountry()
    {
        try {
            $this->countryContext->getCountry();

            return true;
        } catch (CountryNotFoundException $ex) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomer()
    {
        return $this->customerContext->getCustomer();
    }

    /**
     * {@inheritdoc}
     */
    public function hasCustomer()
    {
        try {
            $this->customerContext->getCustomer();

            return true;
        } catch (CustomerNotFoundException $ex) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCart()
    {
        return $this->cartContext->getCart();
    }

    /**
     * {@inheritdoc}
     */
    public function getContext()
    {
        return [
            'store' => $this->getStore(),
            'customer' => $this->hasCustomer() ? $this->getCustomer() : null,
            'currency' => $this->getCurrency(),
            'country' => $this->getCountry(),
            'cart' => $this->getCart(),
        ];
    }
}
