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

namespace CoreShop\Bundle\CoreBundle\Shipping\Calculator;

use CoreShop\Component\Shipping\Calculator\CarrierPriceCalculatorInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;

class FreeShippingPriceRuleActionCalculator implements CarrierPriceCalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPrice(CarrierInterface $carrier, ShippableInterface $shippable, AddressInterface $address, $withTax = true)
    {
        if ($shippable instanceof CartInterface) {
            if ($shippable->hasPriceRules()) {
                foreach ($shippable->getPriceRules() as $priceRule) {
                    if ($priceRule instanceof CartPriceRuleInterface) {
                        foreach ($priceRule->getActions() as $action) {
                            if ($action->getType() === 'freeShipping') {
                                return 0;
                            }
                        }
                    }
                }
            }
        }

        return false;
    }
}
