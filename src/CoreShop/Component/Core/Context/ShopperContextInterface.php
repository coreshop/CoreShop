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

namespace CoreShop\Component\Core\Context;

use CoreShop\Component\Address\Context\CountryContextInterface;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Customer\Context\CustomerContextInterface;
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Order\Context\CartContextInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;

interface ShopperContextInterface extends StoreContextInterface, CurrencyContextInterface, LocaleContextInterface, CountryContextInterface, CustomerContextInterface, CartContextInterface
{
    public function getContext(): array;

    public function hasStore(): bool;

    public function hasCurrency(): bool;

    public function hasLocaleCode(): bool;

    public function hasCountry(): bool;

    public function hasCustomer(): bool;
}
