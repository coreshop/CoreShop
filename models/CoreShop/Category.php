<?php
    
namespace CoreShop;

use CoreShop\Base;

use Pimcore\Model\Object;

class Category extends Base {
    
    public static function getAll()
    {
        $list = new Object\CoreShopCategory\Listing();
        
        return $list->getObjects();
    }
    
    public function getProducts()
    {
        $list = new Object\CoreShopProduct\Listing();
        $list->setCondition("categories LIKE '%,?,%'", array($this->getId()));
        
        return $list->getObjects();
    }
}