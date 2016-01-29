<?php
/**
 * CoreShop
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

namespace CoreShop\Model\Product\SpecificPrice\Condition;

use CoreShop\Model;
use CoreShop\Tool;

use Pimcore\Model\Object\CoreShopUser;

class Customer extends AbstractCondition {

    /**
     * @var int
     */
    public $customer;

    /**
     * @var string
     */
    public $type = "customer";

    /**
     * @return int
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param int $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    /**
     * Check if Product is Valid for Condition
     *
     * @param Model\Product $product
     * @param Model\Product\SpecificPrice $specificPrice
     * @return boolean
     */
    public function checkCondition(Model\Product $product, Model\Product\SpecificPrice $specificPrice)
    {
        $session = Tool::getSession();
        $cart = Tool::prepareCart();

        if($cart instanceof Model\Cart) {
            if ($cart->getUser() instanceof CoreShopUser && $session->user instanceof CoreShopUser) {
                if ($cart->getUser()->getId() === $session->user->getId()) {
                    return true;
                }
            }
        }

        return false;
    }
}
