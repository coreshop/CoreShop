<?php
    
class CoreShop_Product extends CoreShop_Base {
    
    public static function getAll()
    {
        $list = new Object_CoreShopProduct_List();
        
        return $list->getObjects();
    }
    
    public function getImage()
    {
        if(count($this->getImages() > 0))
        {
            return $this->getImages()[0];
        }
        
        return false;
    }
    
    public function save()
    {
        //Calculate Retail Price with Tax
        $retailPriceWithTax = $this->getRetailPrice() * (1 + $this->getTax());
        $this->setPrice($retailPriceWithTax);
        
        parent::save();
    }
    
    public function toArray()
    {
        return array(
            "image" => $this->getImage(),
            "price" => $this->getPrice(),
            "priceFormatted" => CoreShop_Tool::formatPrice($this->getPrice()),
            "name" => $this->getName()
        );
    }
    
    public function getVariantDifferences()
    {
        $master = $this;
        $parent = Object_Service::hasInheritableParentObject($this);
        
        if($parent)
            $master = $parent;
        
        return CoreShop_Tool_Service::getDimensions($master);
    }
}