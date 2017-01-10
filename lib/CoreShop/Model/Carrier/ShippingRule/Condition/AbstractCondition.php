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
 * Class AbstractCondition
 * @package CoreShop\Model\Carrier\ShippingRule\Condition
 */
abstract class AbstractCondition extends Model\Rules\Condition\AbstractCondition
{
    /**
     * Check if Cart is Valid for Condition.
     *
     * @param Model\Carrier $carrier
     * @param Model\Cart $cart
     * @param Model\User\Address $address
     * @param CarrierShippingRule $shippingRule
     *
     * @return boolean
     */
    abstract public function checkCondition(Model\Carrier $carrier, Model\Cart $cart, Model\User\Address $address, CarrierShippingRule $shippingRule);

    /**
     * get cache key for Condition. Use this method to invalidate a condition
     *
     * @return string
     */
    public function getCacheKey()
    {
        return md5(\Zend_Json::encode(get_object_vars($this)));
    }
}
