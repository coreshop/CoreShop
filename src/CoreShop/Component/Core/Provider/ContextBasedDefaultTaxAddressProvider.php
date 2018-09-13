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

namespace CoreShop\Component\Core\Provider;

use CoreShop\Component\Address\Context\CountryContextInterface;
use CoreShop\Component\Resource\Factory\PimcoreFactoryInterface;

class ContextBasedDefaultTaxAddressProvider implements DefaultTaxAddressProviderInterface
{
     /**
     * @var PimcoreFactoryInterface
     */
    private $addressFactory;

    /**
     * @var CountryContextInterface
     */
    private $countryContext;

    /**
     * @param PimcoreFactoryInterface $addressFactory
     * @param CountryContextInterface $countryContext
     */
    public function __construct(PimcoreFactoryInterface $addressFactory, CountryContextInterface $countryContext)
    {
        $this->addressFactory = $addressFactory;
        $this->countryContext = $countryContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getAddress()
    {
        $address = $this->addressFactory->createNew();
        $country = $this->countryContext->getCountry();

        $address->setCountry($country);

        return $address;
    }
}
