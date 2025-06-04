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

namespace CoreShop\Component\Core\Context\Country;

use CoreShop\Component\Address\Context\CountryContextInterface;
use CoreShop\Component\Address\Context\CountryNotFoundException;
use CoreShop\Component\Core\Model\CountryInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;

final class StoreAwareCountryContext implements CountryContextInterface
{
    public function __construct(
        private CountryContextInterface $countryContext,
        private StoreContextInterface $storeContext,
    ) {
    }

    public function getCountry(): \CoreShop\Component\Address\Model\CountryInterface
    {
        /** @var StoreInterface $store */
        $store = $this->storeContext->getStore();

        try {
            $country = $this->countryContext->getCountry();

            if (!$country instanceof CountryInterface || !$this->isCountryAvailable($country, $store)) {
                return $store->getBaseCountry();
            }

            return $country;
        } catch (CountryNotFoundException) {
            return $store->getBaseCountry();
        }
    }

    private function isCountryAvailable(CountryInterface $country, StoreInterface $store): bool
    {
        return in_array($country->getIsoCode(), array_map(static function (CountryInterface $country) {
            return $country->getIsoCode();
        }, $store->getCountries()->toArray()), true);
    }
}
