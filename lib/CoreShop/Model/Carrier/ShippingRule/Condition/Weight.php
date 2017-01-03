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
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Carrier\ShippingRule\Condition;

use CoreShop\Model;
use CoreShop\Model\Carrier\ShippingRule as CarrierShippingRule;

/**
 * Class Weight
 * @package CoreShop\Model\Carrier\ShippingRule\Condition
 */
class Weight extends AbstractCondition
{
    /**
     * @var string
     */
    public static $type = 'weight';

    /**
     * @var float
     */
    public $minWeight;

    /**
     * @var float
     */
    public $maxWeight;

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
        $totalWeight = $cart->getTotalWeight();

        if ($this->getMinWeight() > 0) {
            if ($totalWeight < $this->getMinWeight()) {
                return false;
            }
        }

        if ($this->getMaxWeight() > 0) {
            if ($totalWeight > $this->getMaxWeight()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return float
     */
    public function getMinWeight()
    {
        return $this->minWeight;
    }

    /**
     * @param float $minWeight
     */
    public function setMinWeight($minWeight)
    {
        $this->minWeight = $minWeight;
    }

    /**
     * @return float
     */
    public function getMaxWeight()
    {
        return $this->maxWeight;
    }

    /**
     * @param float $maxWeight
     */
    public function setMaxWeight($maxWeight)
    {
        $this->maxWeight = $maxWeight;
    }
}
