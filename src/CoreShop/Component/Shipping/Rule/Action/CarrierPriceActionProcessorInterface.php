<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Shipping\Rule\Action;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;

interface CarrierPriceActionProcessorInterface
{
    /**
     * @param CarrierInterface   $carrier
     * @param ShippableInterface $shippable
     * @param AddressInterface   $address
     * @param array              $configuration
     *
     * @return mixed
     */
    public function getPrice(CarrierInterface $carrier, ShippableInterface $shippable, AddressInterface $address, array $configuration);

    /**
     * @param CarrierInterface   $carrier
     * @param ShippableInterface $shippable
     * @param AddressInterface   $address
     * @param int                $price
     * @param array              $configuration
     *
     * @return mixed
     */
    public function getModification(CarrierInterface $carrier, ShippableInterface $shippable, AddressInterface $address, $price, array $configuration);
}
