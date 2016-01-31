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

namespace CoreShop\Model\PriceRule\Condition;

use CoreShop\Model\PriceRule;
use CoreShop\Model\Cart;
use Pimcore\Model\Object\CoreShopProduct;

class Product extends AbstractCondition
{

    /**
     * @var int
     */
    public $product;

    /**
     * @var string
     */
    public $type = "product";

    /**
     * @return int
     */
    public function getProduct()
    {
        if (!$this->product instanceof CoreShopProduct) {
            $this->product = CoreShopProduct::getByPath($this->product);
        }

        return $this->product;
    }

    /**
     * @param int $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * Check if Cart is Valid for Condition
     *
     * @param Cart $cart
     * @param PriceRule $priceRule
     * @param bool|false $throwException
     * @return bool
     * @throws \Exception
     */
    public function checkCondition(Cart $cart, PriceRule $priceRule, $throwException = false)
    {
        $found = false;

        if ($this->getProduct() instanceof CoreShopProduct) {
            foreach ($cart->getItems() as $i) {
                if ($i->getProduct()->getId() == $this->getProduct()->getId()) {
                    $found = true;
                }
            }
        }

        if (!$found) {
            if ($throwException) {
                throw new \Exception("You cannot use this voucher with these products");
            } else {
                return false;
            }
        }

        return true;
    }
}
