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

namespace CoreShop\Component\Shipping\Checker;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;

interface CarrierShippingRuleCheckerInterface
{
    /**
     * @param CarrierInterface   $carrier
     * @param ShippableInterface $shippable
     * @param AddressInterface   $address
     *
     * @return mixed
     */
    public function isShippingRuleValid(CarrierInterface $carrier, ShippableInterface $shippable, AddressInterface $address);
}
