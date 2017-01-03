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

use CoreShop\Model\Carrier;
use CoreShop\Model\Carrier\ShippingRule as CarrierShippingRule;
use CoreShop\Model\Cart;
use CoreShop\Model\Product as ProductModel;
use CoreShop\Model\User\Address;

/**
 * Class ShippingRule
 * @package CoreShop\Model\PriceRule\Condition
 */
class ShippingRule extends AbstractCondition
{
    /**
     * @var string
     */
    public static $type = 'shippingRule';

    /**
     * @var int
     */
    public $shippingRule;

    /**
     * Check if Cart is Valid for Condition.
     *
     * @param Carrier $carrier
     * @param Cart $cart
     * @param Address $address;
     * @param CarrierShippingRule $shippingRule
     *
     * @return mixed
     */
    public function checkCondition(Carrier $carrier, Cart $cart, Address $address, CarrierShippingRule $shippingRule)
    {
        $carrierShippingRule = CarrierShippingRule::getById($this->getShippingRule());

        if ($carrierShippingRule instanceof CarrierShippingRule) {
            return $carrierShippingRule->checkValidity($carrier, $cart, $address);
        }

        return false;
    }

    /**
     * @return int
     */
    public function getShippingRule()
    {
        return $this->shippingRule;
    }

    /**
     * @param int $shippingRule
     */
    public function setShippingRule($shippingRule)
    {
        $this->shippingRule = $shippingRule;
    }
}
