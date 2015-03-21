<?php

class CoreShop_ProductController extends CoreShop_Controller_Action {
    
    public function detailAction () {
        $id = $this->getParam("product");
        $product = CoreShop_Product::getById($id);
        
        if($product instanceof CoreShop_Product)
        {
            $this->view->product = $product;
        }
        else
        {
            throw new CoreShop_Exception(sprintf('Product with id "%s" not found', $id));
        }
    }
    
    public function indexAction()
    {
        
    }
    
    public function listAction() {
        
    }
}
