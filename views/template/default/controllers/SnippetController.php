<?php
    
use Website\Controller\Action;

class CoreShopTemplate_SnippetController extends Action
{
    public function init()
    {
        parent::init();
        
        if ($this->view->editmode) {
            $this->enableLayout();
            $this->setLayout("snippet");
        }
    }

    public function footerAction()
    {
    }

    public function textAction()
    {
    }
}
