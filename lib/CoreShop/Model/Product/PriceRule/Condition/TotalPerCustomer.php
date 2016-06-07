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
use CoreShop\Model\User;
use CoreShop\Tool;

class TotalPerCustomer extends AbstractCondition
{
    /**
     * @var int
     */
    public $total;

    /**
     * @var string
     */
    public $type = 'totalPerCustomer';

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param int $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
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
        $user = Tool::getUser();

        //Check Total For Customer
        if ($user instanceof User) {
            $orders = $user->getOrders();
            $priceRulesUsed = 0;

            foreach ($orders as $order) {
                if ($order->getPriceRule() instanceof PriceRule && $order->getPriceRule()->getId() == $priceRule->getId()) {
                    ++$priceRulesUsed;
                }
            }

            if ($priceRulesUsed >= $this->getTotal()) {
                return false;
            }
        }

        return true;
    }
}
