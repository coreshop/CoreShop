<?php

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