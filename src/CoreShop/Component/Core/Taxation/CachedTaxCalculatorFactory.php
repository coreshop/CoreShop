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

namespace CoreShop\Component\Core\Taxation;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Address\Model\CountryInterface;
use CoreShop\Component\Address\Model\StateInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;

class CachedTaxCalculatorFactory implements TaxCalculatorFactoryInterface
{
    private array $cache = [];

    public function __construct(
        private TaxCalculatorFactoryInterface $taxCalculatorFactory,
    ) {
    }

    public function getTaxCalculatorForAddress(
        TaxRuleGroupInterface $taxRuleGroup,
        AddressInterface $address,
        array $context = [],
    ): TaxCalculatorInterface {
        $cacheIdentifier = sprintf(
            '%s.%s.%s',
            $taxRuleGroup->getId(),
            ($address->getCountry() instanceof CountryInterface ? $address->getCountry()->getId() : 0),
            ($address->getState() instanceof StateInterface ? $address->getState()->getId() : 0),
        );

        foreach ($context as $key => $value) {
            if ($value instanceof ResourceInterface) {
                $cacheIdentifier .= '-' . $key . '-' . $value->getId();
            }
        }

        if (!array_key_exists($cacheIdentifier, $this->cache)) {
            $this->cache[$cacheIdentifier] = $this->taxCalculatorFactory->getTaxCalculatorForAddress(
                $taxRuleGroup,
                $address,
                $context,
            );
        }

        return $this->cache[$cacheIdentifier];
    }
}
