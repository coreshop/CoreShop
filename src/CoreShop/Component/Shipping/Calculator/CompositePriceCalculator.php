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

namespace CoreShop\Component\Shipping\Calculator;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;

class CompositePriceCalculator implements CarrierPriceCalculatorInterface
{
    /**
     * @var CarrierPriceCalculatorInterface[]
     */
    protected $calculators;

    /**
     * @param CarrierPriceCalculatorInterface[] $calculators
     */
    public function __construct(array $calculators)
    {
        $this->calculators = $calculators;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(CarrierInterface $carrier, ShippableInterface $shippable, AddressInterface $address)
    {
        $price = false;

        /*
         * First Price wins
         */
        foreach ($this->calculators as $calculator) {
            $actionPrice = $calculator->getPrice($carrier, $shippable, $address);

            if (false !== $actionPrice && null !== $actionPrice) {
                $price = $actionPrice;

                break;
            }
        }

        return $price;
    }
}
