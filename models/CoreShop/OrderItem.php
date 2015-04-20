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

namespace CoreShop;

use CoreShop\Base;

class OrderItem extends Base {
    
    public function getTotal()
    {
        return $this->getAmount() * $this->getPrice();
    }


    public function getOrder()
    {
        $parent = $this->getParent();

        do {
            if ($parent instanceof \Pimcore\Model\Object\CoreShopOrder) {
                return $parent;
            }

            $parent = $parent->getParent();
        } while ($parent != null);

        return;
    }
}