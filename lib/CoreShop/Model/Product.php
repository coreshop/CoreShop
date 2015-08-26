<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.coreshop.org/license
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     New BSD License
 */

namespace CoreShop\Model;

use Pimcore\Model\Object;
use Pimcore\Model\Object\CoreShopUser;
use Pimcore\Model\Object\CoreShopCurrency;
use Pimcore\Model\Object\Fieldcollection\Data\CoreShopProductSpecificPrice;
use Pimcore\Model\Asset\Image;
use CoreShop\Tool;
use CoreShop\Config;

class Product extends Base {
    
    public static function getAll()
    {
        $list = new Object\CoreShopProduct\Listing();
        $list->setCondition("enabled=1");

        return $list->getObjects();
    }
    
    public static function getLatest($limit = 8)
    {
        $cacheKey = "coreshop_latest";

        if(!$objects = \Pimcore\Model\Cache::load($cacheKey)) {

            $list = new Object\CoreShopProduct\Listing();
            $list->setCondition("enabled=1");
            $list->setOrderKey("o_creationDate");
            $list->setOrder("DESC");

            $objects = $list->getObjects();
        }

        return $objects;
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

    public function getIsNew()
    {
        $config = Config::getConfig();
        $configArray = $config->toArray();

        if($configArray['product']['days-as-new'] > 0)
        {
            $creationDate = new \Zend_Date($this->getCreationDate());
            $nowDate = new \Zend_Date();

            $diff = $nowDate->sub($creationDate)->toValue();
            $days = ceil($diff/60/60/24) +1;

            if($days <= $configArray['product']['days-as-new'])
                return true;
        }

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

    public function inCategory(Object\CoreShopCategory $category) {
        foreach($this->getCategories() as $c) {
            if($c->getId() == $category->getId())
                return true;
        }

        return false;
    }
    
    public function toArray()
    {
        $urlHelper = new \Pimcore\View\Helper\Url();

        return array(
            "image" => $this->getImage()->getFullPath(),
            "price" => $this->getProductPrice(),
            "priceFormatted" => Tool::formatPrice($this->getProductPrice()),
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

    public function getProductPrice()
    {
        $cacheKey = "coreshop_product_price_" . $this->getId();

        if($price = \Pimcore\Model\Cache::load($cacheKey)) {
            return $price;
        }

        $price = $this->getPrice();

        if(count($this->getSpecificPrice()) > 0)
        {
            $session = Tool::getSession();
            //Process Specific Prices
            foreach($this->getSpecificPrice() as $sPrice)
            {
                $date = \Zend_Date::now();

                $hasCustomer = false;
                $hasCountry = false;
                $hasCurrency = false;

                if($sPrice->getFrom() instanceof \Zend_Date) {
                    if ($date->get(\Zend_Date::TIMESTAMP) < $sPrice->getFrom()->get(\Zend_Date::TIMESTAMP)) {
                        continue;
                    }
                }

                if($sPrice->getTo() instanceof \Zend_Date) {
                    if ($date->get(\Zend_Date::TIMESTAMP) > $sPrice->getTo()->get(\Zend_Date::TIMESTAMP)) {
                        continue;
                    }
                }


                if(count($sPrice->getCustomers()) > 0 && $session->user instanceof CoreShopUser)
                {
                    foreach($sPrice->getCustomers() as $cust)
                    {
                        if($cust->getId() == $session->user->getId())
                            $hasCustomer = true;
                    }
                }
                else if (count($sPrice->getCustomers()) == 0) { //Non is selected means all Users
                    $hasCustomer = true;
                }

                if(count($sPrice->getCountries()) > 0 && Tool::objectInList(Tool::getCountry(), $sPrice->getCountries())) {
                    $hasCountry = true;
                }
                else if(count($sPrice->getCountries()) == 0) { //Non selected means all
                    $hasCountry = true;
                }

                if(count($sPrice->getCurrencies()) > 0 && Tool::objectInList(Tool::getCurrency(), $sPrice->getCurrencies())) {
                    $hasCurrency = true;
                }
                else if(count($sPrice->getCountries()) == 0) { //Non selected means all
                    $hasCurrency = true;
                }

                if($hasCountry && $hasCustomer && $hasCurrency) {
                    $price = $this->applySpecificPrice($sPrice);
                    break;
                }
            }
        }

        return Tool::convertToCurrency($price);
    }

    protected function applySpecificPrice(CoreShopProductSpecificPrice $sPrice)
    {
        $basePrice = $sPrice->getPrice() > 0 ? $sPrice->getPrice() : $this->getPrice();

        if($sPrice->getReductionType() == "percentage") {
            return $basePrice * (100 - $sPrice->getReduction()) / 100;
        }

        if($sPrice->getReductionType() == "amount") {
            return $basePrice - $sPrice->getReduction();
        }

        return $basePrice;
    }
}