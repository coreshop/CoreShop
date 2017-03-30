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

namespace CoreShop\Bundle\CoreShopLegacyBundle\Model\PriceRule\Condition;

use CoreShop\Bundle\CoreShopLegacyBundle\Exception;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart\PriceRule;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Product as ProductModel;
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
     * @param Cart       $cart
     * @param PriceRule  $priceRule
     * @param bool|false $throwException
     *
     * @return bool
     *
     * @throws Exception
     */
    public function checkConditionCart(Cart $cart, PriceRule $priceRule, $throwException = false)
    {
        $customer = $cart->getUser() ? $cart->getUser() : \CoreShop\Bundle\CoreShopLegacyBundle\CoreShop::getTools()->getUser();

        return $this->check($customer);
    }

    /**
     * Check if Product is Valid for Condition.
     *
     * @param ProductModel $product
     * @param ProductModel\AbstractProductPriceRule $priceRule
     *
     * @return bool
     */
    public function checkConditionProduct(ProductModel $product, ProductModel\AbstractProductPriceRule $priceRule)
    {
        return $this->check(\CoreShop\Bundle\CoreShopLegacyBundle\CoreShop::getTools()->getUser());
    }

    /**
     * @param User $customer
     * @param bool $throwException
     * @return bool
     * @throws Exception
     * @throws \Exception
     */
    private function check($customer, $throwException = false)
    {
        if (!$customer instanceof User) {
            if ($throwException) {
                throw new Exception('Customer in cart is emtpy!');
            } else {
                return false;
            }
        }

        foreach ($customer->getCustomerGroups() as $group) {
            if (in_array($group->getId(), $this->getCustomerGroups())) {
                return true;
            }
        }

        return false;
    }
}
