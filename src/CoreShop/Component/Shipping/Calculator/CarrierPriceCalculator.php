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

declare(strict_types=1);

namespace CoreShop\Component\Shipping\Calculator;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;
use CoreShop\Component\Shipping\Exception\NoShippingPriceFoundException;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;

final class CarrierPriceCalculator implements CarrierPriceCalculatorInterface
{
    public function __construct(private PrioritizedServiceRegistryInterface $shippingCalculatorRegistry)
    {
    }

    public function getPrice(CarrierInterface $carrier, ShippableInterface $shippable, AddressInterface $address, array $context): int
    {
        $price = 0;

        /**
         * @var CarrierPriceCalculatorInterface $calculator
         */
        foreach ($this->shippingCalculatorRegistry->all() as $calculator) {
            try {
                $price = $calculator->getPrice($carrier, $shippable, $address, $context);
            }
            catch (NoShippingPriceFoundException) {
                continue;
            }
        }

        return $price;
    }
}
