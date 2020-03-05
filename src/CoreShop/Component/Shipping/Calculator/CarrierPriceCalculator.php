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

namespace CoreShop\Component\Shipping\Calculator;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;

final class CarrierPriceCalculator implements CarrierPriceCalculatorInterface
{
    private $shippingCalculatorRegistry;

    public function __construct(PrioritizedServiceRegistryInterface $shippingCalculatorRegistry)
    {
        $this->shippingCalculatorRegistry = $shippingCalculatorRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(CarrierInterface $carrier, ShippableInterface $shippable, AddressInterface $address): int
    {
        $netPrice = 0;

        /**
         * @var CarrierPriceCalculatorInterface $calculator
         */
        foreach ($this->shippingCalculatorRegistry->all() as $calculator) {
            $price = $calculator->getPrice($carrier, $shippable, $address);

            if (false !== $price && null !== $price) {
                $netPrice = $price;

                break;
            }
        }

        return $netPrice;
    }
}
