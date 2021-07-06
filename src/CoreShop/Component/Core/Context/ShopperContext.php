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

namespace CoreShop\Component\Core\Context;

use CoreShop\Component\Address\Context\CountryContextInterface;
use CoreShop\Component\Address\Context\CountryNotFoundException;
use CoreShop\Component\Address\Model\CountryInterface;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Currency\Context\CurrencyNotFoundException;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Customer\Context\CustomerContextInterface;
use CoreShop\Component\Customer\Context\CustomerNotFoundException;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Locale\Context\LocaleNotFoundException;
use CoreShop\Component\Order\Context\CartContextInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Store\Context\StoreNotFoundException;
use CoreShop\Component\Store\Model\StoreInterface;

class ShopperContext implements ShopperContextInterface
{
    private $storeContext;
    private $currencyContext;
    private $localeContext;
    private $countryContext;
    private $customerContext;
    private $cartContext;

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

    public function getStore(): StoreInterface
    {
        return $this->storeContext->getStore();
    }

    public function hasStore(): bool
    {
        try {
            $this->storeContext->getStore();

            return true;
        } catch (StoreNotFoundException $ex) {
            return false;
        }
    }

    public function getCurrency(): CurrencyInterface
    {
        return $this->currencyContext->getCurrency();
    }

    public function hasCurrency(): bool
    {
        try {
            $this->currencyContext->getCurrency();

            return true;
        } catch (CurrencyNotFoundException $ex) {
            return false;
        }
    }

    public function getLocaleCode(): string
    {
        return $this->localeContext->getLocaleCode();
    }

    public function hasLocaleCode(): bool
    {
        try {
            $this->localeContext->getLocaleCode();

            return true;
        } catch (LocaleNotFoundException $ex) {
            return false;
        }
    }

    public function getCountry(): CountryInterface
    {
        return $this->countryContext->getCountry();
    }

    public function hasCountry(): bool
    {
        try {
            $this->countryContext->getCountry();

            return true;
        } catch (CountryNotFoundException $ex) {
            return false;
        }
    }

    public function getCustomer(): CustomerInterface
    {
        return $this->customerContext->getCustomer();
    }

    public function hasCustomer(): bool
    {
        try {
            $this->customerContext->getCustomer();

            return true;
        } catch (CustomerNotFoundException $ex) {
            return false;
        }
    }

    public function getCart(): OrderInterface
    {
        return $this->cartContext->getCart();
    }

    public function getContext(): array
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
