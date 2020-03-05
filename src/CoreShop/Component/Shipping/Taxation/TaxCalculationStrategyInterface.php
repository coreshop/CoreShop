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

namespace CoreShop\Component\Shipping\Taxation;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;
use CoreShop\Component\Taxation\Model\TaxItemInterface;

interface TaxCalculationStrategyInterface
{
    /**
     * @param ShippableInterface $shippable
     * @param CarrierInterface   $carrier
     * @param AddressInterface   $address
     * @param int                $shippingAmount
     * @return TaxItemInterface[]
     */
    public function calculateShippingTax(
        ShippableInterface $shippable,
        CarrierInterface $carrier,
        AddressInterface $address,
        int $shippingAmount
    );
}
