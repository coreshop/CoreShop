<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Carrier\ShippingRule\Condition;

use CoreShop\Model;
use CoreShop\Model\Carrier\ShippingRule as CarrierShippingRule;

/**
 * Class Amount
 * @package CoreShop\Model\Carrier\ShippingRule\Condition
 */
class Amount extends AbstractCondition
{
    /**
     * @var string
     */
    public static $type = 'amount';

    /**
     * @var float
     */
    public $minAmount;

    /**
     * @var float
     */
    public $maxAmount;

    /**
     * Check if Cart is Valid for Condition.
     *
     * @param Model\Carrier $carrier
     * @param Model\Cart $cart
     * @param Model\User\Address $address;
     * @param CarrierShippingRule $shippingRule
     *
     * @return mixed
     */
    public function checkCondition(Model\Carrier $carrier, Model\Cart $cart, Model\User\Address $address, CarrierShippingRule $shippingRule)
    {
        $totalAmount = $cart->getSubtotal(true);

        if ($this->getMinAmount() > 0) {
            if ($totalAmount <= $this->getMinAmount()) {
                return false;
            }
        }

        if ($this->getMaxAmount() > 0) {
            if ($totalAmount >= $this->getMaxAmount()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return float
     */
    public function getMinAmount()
    {
        return $this->minAmount;
    }

    /**
     * @param float $minAmount
     */
    public function setMinAmount($minAmount)
    {
        $this->minAmount = $minAmount;
    }

    /**
     * @return float
     */
    public function getMaxAmount()
    {
        return $this->maxAmount;
    }

    /**
     * @param float $maxAmount
     */
    public function setMaxAmount($maxAmount)
    {
        $this->maxAmount = $maxAmount;
    }
}
