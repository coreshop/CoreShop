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

namespace CoreShop\Controller\Action;

use CoreShop\Config;
use CoreShop\Controller\Action;

use CoreShop\Model\OrderState;
use CoreShop\Plugin;

use Pimcore\Model\Object\CoreShopPayment;
use Pimcore\Model\Object\CoreShopOrder;
use Pimcore\Model\Object\CoreShopUser;

class Payment extends Action {
    
    protected function paymentReturnAction () {
        $this->prepareCart();
        $this->cart->delete();
        
        unset($this->session->order);
        unset($this->session->cart);
        unset($this->session->cartId);
    }
}
