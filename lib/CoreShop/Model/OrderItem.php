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

use \Pimcore\Model\Object\CoreShopOrder;;

class OrderItem extends Base {
    
    public function getTotal()
    {
        return $this->getAmount() * $this->getPrice();
    }


    public function getOrder()
    {
        $parent = $this->getParent();

        do {
            if ($parent instanceof CoreShopOrder) {
                return $parent;
            }

            $parent = $parent->getParent();
        } while ($parent != null);

        return;
    }
}