<?php
    
namespace CoreShop;

use CoreShop\Base;
use Pimcore\Model\Object;
use CoreShop\Tool;

class Product extends Base {
    
    public static function getAll()
    {
        $list = new Object\CoreShopProduct\Listing();
        
        return $list->getObjects();
    }
    
    public static function getLatest($limit = 8)
    {
        $list = new Object\CoreShopProduct\Listing();

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
            "priceFormatted" => Tool::formatPrice($this->getPrice()),
            "name" => $this->getName()
        );
    }
    
    public function getVariantDifferences()
    {
        $master = $this;
        $parent = Object\Service::hasInheritableParentObject($this);
        
        if($parent)
            $master = $parent;
        
        return \CoreShop\Tool\Service::getDimensions($master);
    }
}