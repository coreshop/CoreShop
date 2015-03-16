<?php
    
class CoreShop_Category extends CoreShop_Base {
    
    public static function getAll()
    {
        $list = new Object_CoreShopCategory_List();
        
        return $list->getObjects();
    }
    
    public function getProducts()
    {
        $list = new Object_CoreShopProduct_List();
        $list->setCondition("categories LIKE '%,?,%'", array($this->getId()));
        
        return $list->getObjects();
    }
}