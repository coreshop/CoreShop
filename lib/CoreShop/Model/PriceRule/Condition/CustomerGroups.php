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

namespace CoreShop\Model\PriceRule\Condition;

use CoreShop\Exception;
use CoreShop\Model\Cart\PriceRule;
use CoreShop\Model\Cart;
use CoreShop\Model\Product as ProductModel;
use CoreShop\Model\CustomerGroup as CustomerGroupModel;
use CoreShop\Model\User;
use CoreShop\Tool;

/**
 * Class CustomerGroups
 * @package CoreShop\Model\PriceRule\Condition
 */
class CustomerGroups extends AbstractCondition
{
    /**
     * @var int
     */
    public $customerGroups;

    /**
     * @var string
     */
    public $type = 'customerGroups';

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
        $customer = $cart->getUser() ? $cart->getUser() : Tool::getUser();

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
        return $this->check(Tool::getUser());
    }

    /**
     * @param User $customer
     * @param bool $throwException
     * @return bool
     * @throws Exception
     * @throws \Exception
     */
    private function check(User $customer, $throwException = false)
    {
        if (!$customer) {
            if ($throwException) {
                throw new Exception('Customer in cart is emtpy!');
            } else {
                return false;
            }
        }

        $validCustomerGroupFound = false;

        foreach($this->getCustomerGroups() as $group) {
            foreach ($customer->getGroups() as $customerGroup) {
                $customerGroup = CustomerGroupModel::getByField('name', $customerGroup);

                if ($customerGroup instanceof CustomerGroupModel) {
                    if ($group === $customerGroup->getId()) {
                        $validCustomerGroupFound = true;
                        break;
                    }
                }
            }

            if($validCustomerGroupFound) {
                break;
            }
        }

        if (!$validCustomerGroupFound) {
            if ($throwException) {
                throw new Exception('You cannot use this voucher.');
            }

            return false;
        }

        return true;
    }
}
