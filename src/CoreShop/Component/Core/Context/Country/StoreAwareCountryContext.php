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

namespace CoreShop\Component\Core\Context\Country;

use CoreShop\Component\Address\Context\CountryContextInterface;
use CoreShop\Component\Address\Context\CountryNotFoundException;
use CoreShop\Component\Core\Model\CountryInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;

final class StoreAwareCountryContext implements CountryContextInterface
{
    /**
     * @var CountryContextInterface
     */
    private $countryContext;

    /**
     * @var StoreContextInterface
     */
    private $storeContext;

    /**
     * @param CountryContextInterface $countryContext
     * @param StoreContextInterface $storeContext
     */
    public function __construct(CountryContextInterface $countryContext, StoreContextInterface $storeContext)
    {
        $this->countryContext = $countryContext;
        $this->storeContext = $storeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountry()
    {
        /** @var StoreInterface $store */
        $store = $this->storeContext->getStore();

        try {
            $country = $this->countryContext->getCountry();

            if (!$country instanceof CountryInterface || !$this->isCountryAvailable($country, $store)) {
                return $store->getBaseCountry();
            }

            return $country;
        } catch (CountryNotFoundException $exception) {
            return $store->getBaseCountry();
        }
    }

    /**
     * @param CountryInterface $country
     * @param StoreInterface $store
     * @return bool
     */
    private function isCountryAvailable(CountryInterface $country, StoreInterface $store)
    {
        return in_array($country->getIsoCode(), array_map(function(CountryInterface $country) {
            return $country->getIsoCode();
        }, $store->getCountries()->toArray()));
    }
}
