<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.coreshop.org/license
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     New BSD License
 */

namespace CoreShop\Model;

use Pimcore\Model\Object\CoreShopCart;
use CoreShop\Tool;

class CartItem extends Base {

    /**
     * Calculates the total for the CartItem
     *
     * @return mixed
     */
    public function getTotal()
    {
        return $this->getAmount() * $this->product->getProductPrice();
    }

    /**
     * Get the Cart for this CartItem
     *
     * @return \Pimcore\Model\Object\AbstractObject|void|null
     */
    public function getCart()
    {
        $parent = $this->getParent();

        do {
            if ($parent instanceof CoreShopCart) {
                return $parent;
            }

            $parent = $parent->getParent();
        } while ($parent != null);

        return null;
    }

    /**
     * Returns the CartItem as array
     *
     * @return array+
     */
    public function toArray()
    {
        return array(
            "id" => $this->getId(),
            "product" => $this->getProduct()->toArray(),
            "amount" => $this->getAmount(),
            "price" => Tool::formatPrice($this->product->getProductPrice()),
            "total" => Tool::formatPrice($this->getTotal()),
        );
    }
}