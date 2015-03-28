<?php

use CoreShop\Controller\Action;

use Pimcore\Model\Object\CoreShopProduct;

class CoreShop_ProductController extends Action {
    
    public function detailAction () {
        $id = $this->getParam("product");
        $product = CoreShopProduct::getById($id);
        
        if($product instanceof CoreShopProduct)
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
            throw new CoreShop\Exception(sprintf('Product with id "%s" not found', $id));
        }
    }
    
    public function indexAction()
    {

    }
    
    public function listAction() {
        
    }   
}
