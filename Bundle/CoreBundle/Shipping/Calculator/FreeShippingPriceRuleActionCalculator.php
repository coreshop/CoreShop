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

use CoreShop\Bundle\ShippingBundle\Calculator\CarrierPriceCalculatorInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;

class FreeShippingPriceRuleActionCalculator implements CarrierPriceCalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPrice(CarrierInterface $carrier, CartInterface $cart, AddressInterface $address, $withTax = true)
    {
        if ($cart->hasPriceRules()) {
            foreach ($cart->getPriceRules() as $priceRule) {
                if ($priceRule instanceof CartPriceRuleInterface) {
                    foreach ($priceRule->getActions() as $action) {
                        if ($action->getType() === 'freeShipping') {
                            return 0;
                        }
                    }
                }
            }
        }

        return false;
    }
}
