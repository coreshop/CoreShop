<?php

use CoreShop\Controller\Action;

use Object\CoreShopProduct as Product;

class CoreShop_ProductController extends Action {
    
    public function detailAction () {
        $id = $this->getParam("product");
        $product = CoreShop_Product::getById($id);
        
        if($product instanceof Product)
        {
            $this->view->product = $product;
            
            $this->view->seo = array(
                "image" => $product->getImage(),
                "description" => $product->getMetaDescription() ? $product->getMetaDescription() : $product->getShortDescription()
            );
            
            $this->view->headTitle($product->getMetaTitle() ? $product->getMetaTitle() : $product->getName());
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
