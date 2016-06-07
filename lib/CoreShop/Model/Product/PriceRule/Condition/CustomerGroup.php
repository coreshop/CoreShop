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
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Product\PriceRule\Condition;

use CoreShop\Model\Product\PriceRule;
use CoreShop\Model\Product;
use CoreShop\Model\CustomerGroup as CustomerGroupModel;
use CoreShop\Tool;

class CustomerGroup extends AbstractCondition
{
    /**
     * @var int
     */
    public $customerGroup;

    /**
     * @var string
     */
    public $type = 'customerGroup';

    /**
     * @return int
     */
    public function getCustomerGroup()
    {
        if (!$this->customerGroup instanceof CustomerGroupModel) {
            $this->customerGroup = CustomerGroupModel::getById($this->customerGroup);
        }

        return $this->customerGroup;
    }

    /**
     * @param int $customerGroup
     */
    public function setCustomerGroup($customerGroup)
    {
        $this->customerGroup = $customerGroup;
    }

    /**
     * Check if Product is Valid for Condition.
     *
     * @param Product $product
     * @param Product\AbstractProductPriceRule $priceRule
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function checkCondition(Product $product, Product\AbstractProductPriceRule $priceRule)
    {
        $customer = Tool::getUser();

        if (!$customer) {
            return false;
        }

        $validCustomerGroupFound = false;

        if ($this->getCustomerGroup() instanceof CustomerGroupModel) {
            foreach ($customer->getGroups() as $customerGroup) {
                $customerGroup = CustomerGroupModel::getByField('name', $customerGroup);

                if ($customerGroup instanceof CustomerGroupModel) {
                    if ($this->getCustomerGroup()->getId() === $customerGroup->getId()) {
                        $validCustomerGroupFound = true;
                        break;
                    }
                }
            }
        }

        if (!$validCustomerGroupFound) {
            return false;
        }

        return true;
    }
}
