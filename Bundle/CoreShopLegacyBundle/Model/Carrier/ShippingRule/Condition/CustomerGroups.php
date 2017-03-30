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

namespace CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier\ShippingRule\Condition;

use CoreShop\Bundle\CoreShopLegacyBundle\Exception;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier\ShippingRule as CarrierShippingRule;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\User;

/**
 * Class CustomerGroups
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Model\PriceRule\Condition
 */
class CustomerGroups extends AbstractCondition
{
    /**
     * @var string
     */
    public static $type = 'customerGroups';

    /**
     * @var int[]
     */
    public $customerGroups;

    /**
     * @return int[]
     */
    public function getCustomerGroups()
    {
        return $this->customerGroups;
    }

    /**
     * @param int[] $customerGroups
     */
    public function setCustomerGroups($customerGroups)
    {
        $this->customerGroups = $customerGroups;
    }

    /**
     * Check if Cart is Valid for Condition.
     *
     * @param Carrier $carrier
     * @param Cart $cart
     * @param User\Address $address
     * @param CarrierShippingRule $shippingRule
     *
     * @return mixed
     */
    public function checkCondition(Carrier $carrier, Cart $cart, User\Address $address, CarrierShippingRule $shippingRule)
    {
        return $this->check(\CoreShop\Bundle\CoreShopLegacyBundle\CoreShop::getTools()->getUser());
    }

    /**
     * @param User $customer
     * @return bool
     * @throws Exception
     * @throws \Exception
     */
    private function check(User $customer)
    {
        if (!$customer) {
            return false;
        }

        foreach ($customer->getCustomerGroups() as $group) {
            if (in_array($group->getId(), $this->getCustomerGroups())) {
                return true;
            }
        }

        return false;
    }
}
