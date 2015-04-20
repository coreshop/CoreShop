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

use CoreShop\Plugin;
use CoreShop\Cart;
use CoreShop\CartItem;
use CoreShop\Product;
use CoreShop\Controller\Action;

class CoreShop_EmailController extends Action
{
    public function init()
    {
        parent::init();

        $this->view->layout()->setLayout('coreshop_email');
    }

    public function emailAction()
    {

    }
}