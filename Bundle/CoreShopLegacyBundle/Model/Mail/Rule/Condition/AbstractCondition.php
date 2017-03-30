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

namespace CoreShop\Bundle\CoreShopLegacyBundle\Model\Mail\Rule\Condition;

use CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier\ShippingRule as CarrierShippingRule;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Mail\Rule;
use Pimcore\Model\AbstractModel;

/**
 * Class AbstractCondition
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Model\Mail\Rule\Condition
 */
abstract class AbstractCondition extends \CoreShop\Bundle\CoreShopLegacyBundle\Model\Rules\Condition\AbstractCondition
{
    /**
     * Check condition
     *
     * @param AbstractModel $object
     * @param array $params
     * @param Rule $rule
     * @return mixed
     */
    abstract public function checkCondition(AbstractModel $object, $params = [], Rule $rule);

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
