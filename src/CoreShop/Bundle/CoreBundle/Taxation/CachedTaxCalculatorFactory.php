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

namespace CoreShop\Bundle\CoreBundle\Taxation;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Address\Model\CountryInterface;
use CoreShop\Component\Address\Model\StateInterface;
use CoreShop\Component\Core\Model\TaxRuleGroupInterface;

use CoreShop\Component\Core\Taxation\TaxCalculatorFactoryInterface;


class CachedTaxCalculatorFactory implements TaxCalculatorFactoryInterface
{
    /**
     * @var TaxCalculatorFactoryInterface
     */
    private $taxCalculatorFactory;

    /**
     * @var array
     */
    private $cache = [];

    /**
     * @param TaxCalculatorFactoryInterface $taxCalculatorFactory
     */
    public function __construct(TaxCalculatorFactoryInterface $taxCalculatorFactory)
    {
        $this->taxCalculatorFactory = $taxCalculatorFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxCalculatorForAddress(TaxRuleGroupInterface $taxRuleGroup, AddressInterface $address)
    {
        $cacheIdentifier = sprintf('%s.%s.%s',
            $taxRuleGroup->getId(),
            ($address->getCountry() instanceof CountryInterface ? $address->getCountry()->getId() : 0),
            ($address->getState() instanceof StateInterface ? $address->getState()->getId() : 0)
        );

        if (!array_key_exists($cacheIdentifier, $this->cache)) {
            $this->cache[$cacheIdentifier] = $this->taxCalculatorFactory->getTaxCalculatorForAddress($taxRuleGroup, $address);
        }

        return $this->cache[$cacheIdentifier];
    }
}
