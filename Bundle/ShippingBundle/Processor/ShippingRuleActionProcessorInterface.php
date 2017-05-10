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

namespace CoreShop\Bundle\ShippingBundle\Processor;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleInterface;

interface ShippingRuleActionProcessorInterface
{
    /**
     * @param ShippingRuleInterface $shippingRule
     * @param CarrierInterface      $carrier
     * @param AddressInterface      $address
     * @param bool                  $withTax
     *
     * @return mixed
     */
    public function getPrice(ShippingRuleInterface $shippingRule, CarrierInterface $carrier, AddressInterface $address, $withTax = true);

    /**
     * @param ShippingRuleInterface $shippingRule
     * @param CarrierInterface      $carrier
     * @param AddressInterface      $address
     * @param $price
     *
     * @return mixed
     */
    public function getModification(ShippingRuleInterface $shippingRule, CarrierInterface $carrier, AddressInterface $address, $price);
}
