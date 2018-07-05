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

namespace CoreShop\Component\Shipping\Validator;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;

interface ShippableCarrierValidatorInterface
{
    /**
     * Check if Carrier is valid for Shippable.
     *
     * @param CarrierInterface   $carrier
     * @param ShippableInterface $shippable
     * @param AddressInterface   $address
     *
     * @return bool
     */
    public function isCarrierValid(CarrierInterface $carrier, ShippableInterface $shippable, AddressInterface $address);
}
