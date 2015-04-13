<?php

use CoreShop\Controller\Action;


class CoreShop_ContentController extends Action
{
    public function indexAction() {

    }

    public function contactAction()
    {
        if($this->getRequest()->isPost()) {
            $name = "";
            $mail = "";
            $subject = "";
            $message = "";
        }
    }
}