<?php
    
namespace CoreShop;

use CoreShop\Base;
use Pimcore\Model\Object;
use Pimcore\Model\Asset\Image;
use CoreShop\Tool;
use CoreShop\Config;

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
        if(count($this->getImages()) > 0)
        {

            return $this->getImages()[0];
        }

        return $this->getDefaultImage();
    }

    public function getDefaultImage()
    {
        $config = Config::getConfig();
        $config = $config->toArray();
        $image = Image::getByPath($config['product']['default-image']);

        if($image instanceof Image)
            return $image;

        return false;
    }

    public function save()
    {
        $currentGetInheritedValues = \Pimcore\Model\Object\AbstractObject::getGetInheritedValues();
        \Pimcore\Model\Object\AbstractObject::setGetInheritedValues(true);
        
        //Calculate Retail Price with Tax
        $retailPriceWithTax = $this->getRetailPrice() * (1 + $this->getTax());
        $this->setPrice($retailPriceWithTax);
        
        \Pimcore\Model\Object\AbstractObject::setGetInheritedValues($currentGetInheritedValues);
        
        parent::save();
    }
    
    public function toArray()
    {
        $urlHelper = new \Pimcore\View\Helper\Url();

        return array(
            "image" => $this->getImage()->getFullPath(),
            "price" => $this->getPrice(),
            "priceFormatted" => Tool::formatPrice($this->getPrice()),
            "name" => $this->getName(),
            "thumbnail" => array(
                "cart" => $this->getImage() instanceof Image ? $this->getImage()->getThumbnail("coreshop_productCartPreview")->getPath(true) : ""
            ),
            "href" => $urlHelper->url(array("lang" => \Zend_Registry::get("Zend_Locale"), "name" => $this->getName(), "product" => $this->getId()), 'coreshop_detail')
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