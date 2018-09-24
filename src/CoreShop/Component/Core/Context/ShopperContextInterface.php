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
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Customer\Context\CustomerContextInterface;
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Order\Context\CartContextInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;

interface ShopperContextInterface extends
    StoreContextInterface,
    CurrencyContextInterface,
    LocaleContextInterface,
    CountryContextInterface,
    CustomerContextInterface,
    CartContextInterface
{
    /**
     * @return array
     */
    public function getContext();

    /**
     * @return bool
     */
    public function hasStore();

    /**
     * @return bool
     */
    public function hasCurrency();

    /**
     * @return bool
     */
    public function hasLocaleCode();

    /**
     * @return bool
     */
    public function hasCountry();

    /**
     * @return bool
     */
    public function hasCustomer();
}
