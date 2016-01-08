<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model;

use Pimcore\Cache;
use Pimcore\Model\Object;
use Pimcore\View\Helper\Url;
use Pimcore\Model\Object\Fieldcollection\Data\CoreShopProductSpecificPrice;
use Pimcore\Model\Asset\Image;

use CoreShop\Tool;
use CoreShop\Config;
use CoreShop\Exception\UnsupportedException;
use CoreShop\Tool\Service;

class Product extends Base {


    /**
     * @static
     * @param int $id
     * @return null|Product
     */
    public static function getById($id) {
        $object = Object\AbstractObject::getById($id);

        if ($object instanceof Product) {
            return $object;
        }

        return null;
    }

    /**
     * Get all Products
     *
     * @return array
     */
    public static function getAll()
    {
        $list = new Object\CoreShopProduct\Listing();
        $list->setCondition("enabled=1");

        return $list->getObjects();
    }

    /**
     * Get latest Products
     *
     * @param int $limit
     * @return array|mixed
     */
    public static function getLatest($limit = 8)
    {
        $cacheKey = "coreshop_latest";

        if(!$objects = \Pimcore\Cache::load($cacheKey)) {

            $list = new Object\CoreShopProduct\Listing();
            $list->setCondition("enabled=1");
            $list->setOrderKey("o_creationDate");
            $list->setOrder("DESC");

            if($limit) {
                $list->setLimit($limit);
            }

            $objects = $list->getObjects();
        }

        return $objects;
    }

    /**
     * Get Image for Product
     *
     * @return bool|\Pimcore\Model\Asset
     */
    public function getImage()
    {
        if(count($this->getImages()) > 0)
        {

            return $this->getImages()[0];
        }

        return $this->getDefaultImage();
    }

    /**
     * Get default Image for Product
     *
     * @return bool|\Pimcore\Model\Asset
     */
    public function getDefaultImage()
    {
        $config = Config::getConfig();
        $config = $config->toArray();
        $image = Image::getByPath($config['product']['default-image']);

        if($image instanceof Image)
            return $image;

        return false;
    }

    /**
     * Get Product is new
     *
     * @return bool
     */
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

    /**
     * Save Product
     *
     * @throws \Exception
     */
    public function save()
    {
        $currentGetInheritedValues = Object\AbstractObject::getGetInheritedValues();
        Object\AbstractObject::setGetInheritedValues(true);
        
        //Calculate Retail Price with Tax
        $retailPriceWithTax = $this->getRetailPrice() * (1 + $this->getTax());
        $this->setPrice($retailPriceWithTax);
        
        Object\AbstractObject::setGetInheritedValues($currentGetInheritedValues);
        
        parent::save();
    }

    /**
     * Check if Product is in Categry
     *
     * @param Category $category
     * @return bool
     */
    public function inCategory(Category $category) {
        foreach($this->getCategories() as $c) {
            if($c->getId() == $category->getId())
                return true;
        }

        return false;
    }

    /**
     * Return Product as Array
     *
     * @return array
     * @throws \Exception
     * @throws \Zend_Exception
     */
    public function toArray()
    {
        $urlHelper = new Url();

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

    /**
     * Get all Variants Differences
     *
     * @return array
     */
    public function getVariantDifferences()
    {
        $master = $this;
        $parent = Object\Service::hasInheritableParentObject($this);
        
        if($parent)
            $master = $parent;

        if($master instanceof Product)
            return Service::getDimensions($master);

        return false;
    }

    /**
     * Apply Specific Price to Product
     *
     * @param CoreShopProductSpecificPrice $sPrice
     * @return float
     * @throws UnsupportedException
     */
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

    /**
     * Calculate Product Price
     *
     * @depcreated: Should be replaced with PriceRules
     *
     * @return float|mixed
     * @throws \Exception
     */
    public function getProductPrice()
    {
        $cacheKey = "coreshop_product_price_" . $this->getId();

        if($price = Cache::load($cacheKey)) {
            return $price;
        }

        $price = $this->getPrice();

        try {
            if (count($this->getSpecificPrice()) > 0) {
                $session = Tool::getSession();
                //Process Specific Prices
                foreach ($this->getSpecificPrice() as $sPrice) {
                    $date = \Zend_Date::now();

                    $hasCustomer = false;
                    $hasCountry = false;
                    $hasCurrency = false;

                    if ($sPrice->getFrom() instanceof \Zend_Date) {
                        if ($date->get(\Zend_Date::TIMESTAMP) < $sPrice->getFrom()->get(\Zend_Date::TIMESTAMP)) {
                            continue;
                        }
                    }

                    if ($sPrice->getTo() instanceof \Zend_Date) {
                        if ($date->get(\Zend_Date::TIMESTAMP) > $sPrice->getTo()->get(\Zend_Date::TIMESTAMP)) {
                            continue;
                        }
                    }


                    if (count($sPrice->getCustomers()) > 0 && $session->user instanceof User) {
                        foreach ($sPrice->getCustomers() as $cust) {
                            if($cust instanceof User) {
                                if ($cust->getId() == $session->user->getId())
                                    $hasCustomer = true;
                            }
                        }
                    } else if (count($sPrice->getCustomers()) == 0) { //Non is selected means all Users
                        $hasCustomer = true;
                    }

                    if (count($sPrice->getCountries()) > 0 && Tool::objectInList(Tool::getCountry(), $sPrice->getCountries())) {
                        $hasCountry = true;
                    } else if (count($sPrice->getCountries()) == 0) { //Non selected means all
                        $hasCountry = true;
                    }

                    if (count($sPrice->getCurrencies()) > 0 && Tool::objectInList(Tool::getCurrency(), $sPrice->getCurrencies())) {
                        $hasCurrency = true;
                    } else if (count($sPrice->getCountries()) == 0) { //Non selected means all
                        $hasCurrency = true;
                    }

                    if ($hasCountry && $hasCustomer && $hasCurrency) {
                        $price = $this->applySpecificPrice($sPrice);
                        break;
                    }
                }
            }
        }
        catch(\Exception $ex) {
            \Logger::err(sprintf("No SpecificPrice inside Product Model (%s)", $ex->getMessage()));
        }

        return Tool::convertToCurrency($price);
    }

    /**
     * returns array of images.
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return Image[]
     */
    public function getImages() {
        throw new UnsupportedException("getImages is not supported for " . get_class($this));
    }

    /**
     * returns array of categories.
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return Category[]
     */
    public function getCategories() {
        throw new UnsupportedException("getCategories is not supported for " . get_class($this));
    }

    /**
     * returns retail price
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return float
     */
    public function getRetailPrice() {
        throw new UnsupportedException("getRetailPrice is not supported for " . get_class($this));
    }

    /**
     * returns tax
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return float
     */
    public function getTax() {
        throw new UnsupportedException("getTax is not supported for " . get_class($this));
    }

    /**
     * returns name
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return string
     */
    public function getName() {
        throw new UnsupportedException("getName is not supported for " . get_class($this));
    }


    /**
     * returns price
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return CoreShopProductSpecificPrice[]
     */
    public function getSpecificPrice() {
        throw new UnsupportedException("getPrice is not supported for " . get_class($this));
    }

    /**
     * returns wholesale price
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return float
     */
    public function getWholesalePrice() {
        throw new UnsupportedException("getWholesalePrice is not supported for " . get_class($this));
    }

    /**
     * returns price
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return float
     */
    public function getPrice() {
        throw new UnsupportedException("getPrice is not supported for " . get_class($this));
    }

    /**
     * sets price
     * this method has to be overwritten in Pimcore Object
     *
     * @param $price
     * @throws UnsupportedException
     */
    public function setPrice($price) {
        throw new UnsupportedException("setPrice is not supported for " . get_class($this));
    }

    /**
     * returns is download product
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return string
     */
    public function getIsDownloadProduct() {
        throw new UnsupportedException("getIsDownloadProduct is not supported for " . get_class($this));
    }

    /**
     * returns is weight of the product
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return float
     */
    public function getWeight() {
        throw new UnsupportedException("getWeight is not supported for " . get_class($this));
    }

    /**
     * returns is width of the product
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return float
     */
    public function getWidth() {
        throw new UnsupportedException("getWidth is not supported for " . get_class($this));
    }

    /**
     * returns is height of the product
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return float
     */
    public function getHeight() {
        throw new UnsupportedException("getHeight is not supported for " . get_class($this));
    }

    /**
     * returns is depth of the product
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return float
     */
    public function getDepth() {
        throw new UnsupportedException("getDepth is not supported for " . get_class($this));
    }
}