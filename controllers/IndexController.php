<?php

use CoreShop\Controller\Action;


class CoreShop_IndexController extends Action
{
    public function indexAction() {
        echo \CoreShop\Tool::getCountry();
    }
}