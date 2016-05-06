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

use CoreShop\Model\Cart\PriceRule;
use CoreShop\Model\Product\SpecificPrice;
use CoreShop\Model\User\Address;
use Pimcore\Cache;
use Pimcore\Model\Object;
use Pimcore\View\Helper\Url;
use Pimcore\Model\Object\Fieldcollection\Data\CoreShopProductSpecificPrice;
use Pimcore\Model\Asset\Image;
use CoreShop\Tool;
use CoreShop\Exception\UnsupportedException;
use CoreShop\Tool\Service;

class Product extends Base
{

    /**
     * OUT_OF_STOCK_DENY denies order of product if out-of-stock
     */
    const OUT_OF_STOCK_DENY = 0;

    /**
     * OUT_OF_STOCK_ALLOW allows order of product if out-of-stock
     */
    const OUT_OF_STOCK_ALLOW = 1;

    /**
     * OUT_OF_STOCK_DEFAULT Default behaviour for out of stock.
     */
    const OUT_OF_STOCK_DEFAULT = 2;

    /**
     * @var float
     */
    protected $cheapestDeliveryPrice = null;

    /**
     * @static
     * @param int $id
     * @return null|Product
     */
    public static function getById($id)
    {
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

        if (!$objects = \Pimcore\Cache::load($cacheKey)) {
            $list = new Object\CoreShopProduct\Listing();
            $list->setCondition("enabled=1");
            $list->setOrderKey("o_creationDate");
            $list->setOrder("DESC");

            if ($limit) {
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
        if (count($this->getImages()) > 0) {
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
        $defaultImage = Configuration::get("SYSTEM.PRODUCT.DEFAULTIMAGE");

        if ($defaultImage) {
            $image = Image::getByPath($defaultImage);

            if ($image instanceof Image) {
                return $image;
            }
        }

        return false;
    }

    /**
     * Get Product is new
     *
     * @return bool
     */
    public function getIsNew()
    {
        $markAsNew = Configuration::get("SYSTEM.PRODUCT.DAYSASNEW");

        if (is_int($markAsNew) && $markAsNew > 0) {
            $creationDate = new \Zend_Date($this->getCreationDate());
            $nowDate = new \Zend_Date();

            $diff = $nowDate->sub($creationDate)->toValue();
            $days = ceil($diff/60/60/24) +1;

            if ($days <= $markAsNew) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if Product is in Categry
     *
     * @param Category $category
     * @return bool
     */
    public function inCategory(Category $category)
    {
        foreach ($this->getCategories() as $c) {
            if ($c->getId() == $category->getId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all Variants Differences
     *
     * @param $language
     * @return array
     */
    public function getVariantDifferences($language = null)
    {
        $cacheKey = "coreshop_variant_differences" . $this->getId();

        if($differences = Cache::load($cacheKey)) {
            return $differences;
        }

        if ($language) {
            $language = \Zend_Registry::get("Zend_Locale")->getLanguage();
        }

        $master = $this;
        //Find master object
        while ($master->getType() === "variant") {
            $master = $master->getParent();
        }
        if ($master instanceof Product) {
            $differences = Service::getProductVariations($master, $this, $language);

            Cache::save($differences, $cacheKey);

            return $differences;
        }
        return false;
    }

    /**
     * get price without tax
     *
     * @return float|mixed
     * @throws UnsupportedException
     */
    public function getPriceWithoutTax()
    {
        $cacheKey = "coreshop_product_price_" . $this->getId();

        if ($price = Cache::load($cacheKey)) {
            return $price;
        }

        $price = $this->getSpecificPrice();

        Cache::save($price, $cacheKey, array("coreshop_product_price", "coreshop_product_" . $this->getId() . "_price"));

        return $price;
    }

    /**
     * Get Specific Price
     *
     * @return float
     */
    public function getSpecificPrice()
    {
        $specificPrices = $this->getSpecificPrices();
        $price = $this->getRetailPrice();

        foreach ($specificPrices as $specificPrice) {
            $actions = $specificPrice->getActions();
            $conditions = $specificPrice->getConditions();

            $isValid = true;

            foreach ($conditions as $condition) {
                if (!$condition->checkCondition($this, $specificPrice)) {
                    $isValid = false;
                    break;
                }
            }

            //Conditions are not valid, so continue with next rule
            if (!$isValid) {
                continue;
            }

            foreach ($actions as $action) {
                $actionsPrice = $action->getPrice($this);

                if ($actionsPrice !== false) {
                    $price = $actionsPrice;
                }
            }
        }

        return $price - $this->getSpecificPriceDiscount($price);
    }

    /**
     * Get Discount from Specific Prices
     *
     * @param $price float Base Price for Discounts
     *
     * @return float
     */
    public function getSpecificPriceDiscount($price)
    {
        $specificPrices = $this->getSpecificPrices();
        $discount = 0;

        foreach ($specificPrices as $specificPrice) {
            $conditions = $specificPrice->getConditions();
            $actions = $specificPrice->getActions();

            $isValid = true;

            foreach ($conditions as $condition) {
                if (!$condition->checkCondition($this, $specificPrice)) {
                    $isValid = false;
                    break;
                }
            }

            //Conditions are not valid, so continue with next rule
            if (!$isValid) {
                continue;
            }

            foreach ($actions as $action) {
                $discount += $action->getDiscount($price, $this);
            }
        }

        return $discount;
    }

    /**
     * Get Product Price with Tax
     *
     * @return float|mixed
     * @throws \Exception
     */
    public function getPrice()
    {
        $price = $this->getPriceWithoutTax();
        $calculator = $this->getTaxCalculator();

        if ($calculator) {
            $price = $calculator->addTaxes($price);
        }

        return Tool::convertToCurrency($price);
    }

    /**
     * returns variant with cheapest price
     *
     * @return float|mixed
     */
    public function getCheapestVariantPrice()
    {
        $cacheKey = "coreshop_product_cheapest_variant_price_" . $this->getId();

        if ($price = Cache::load($cacheKey)) {
            return $price;
        }

        if ($this->getType() == 'object') {
            $childs = $this->getChilds(array(self::OBJECT_TYPE_VARIANT));

            $prices = array($this->getPrice());

            if (empty($childs)) {
                return $this->getPrice();
            } else {
                foreach ($childs as $child) {
                    $prices[] = $child->getPrice();
                }

                $price = min($prices);

                Cache::save($price, $cacheKey);

                return $price;
            }
        }

        return $this->getPrice();
    }

    /**
     * Get Tax Rate
     *
     * @return float
     */
    public function getTaxRate()
    {
        $calculator = $this->getTaxCalculator();

        if ($calculator) {
            return $calculator->getTotalRate();
        }

        return 0;
    }

    /**
     * Get Product Tax Amount
     *
     * @param boolean $asArray
     * @return float
     */
    public function getTaxAmount($asArray = false)
    {
        $calculator = $this->getTaxCalculator();

        if ($calculator) {
            return $calculator->getTaxesAmount($this->getPriceWithoutTax(), $asArray);
        }

        return 0;
    }

    /**
     * get TaxCalculator
     *
     * @param Address $address
     * @return bool|TaxCalculator
     */
    public function getTaxCalculator(Address $address = null)
    {
        if (is_null($address)) {
            $cart = Tool::prepareCart();

            $address = $cart->getCustomerAddressForTaxation();

            if (!$address instanceof Address) {
                $address = Address::create();
                $address->setCountry(Tool::getCountry());
            }
        }

        $taxRule = $this->getTaxRule();

        if ($taxRule instanceof TaxRuleGroup) {
            $taxManager = TaxManagerFactory::getTaxManager($address, $taxRule->getId());
            $taxCalculator = $taxManager->getTaxCalculator();

            return $taxCalculator;
        }

        return false;
    }

    /**
     * Adds $delta to current Quantity
     *
     * @param $delta
     */
    public function updateQuantity($delta)
    {
        $this->setQuantity($this->getQuantity() + $delta);
        $this->save();
    }

    /**
     * Is Available when out-of-stock
     *
     * @return bool
     * @throws UnsupportedException
     */
    public function isAvailableWhenOutOfStock()
    {
        $outOfStockBehaviour = $this->getOutOfStockBehaviour();

        if (is_null($outOfStockBehaviour)) {
            $outOfStockBehaviour = self::OUT_OF_STOCK_DEFAULT;
        }

        if (intval($outOfStockBehaviour) === self::OUT_OF_STOCK_DEFAULT) {
            return intval(Configuration::get("SYSTEM.STOCK.DEFAULTOUTOFSTOCKBEHAVIOUR")) === self::OUT_OF_STOCK_ALLOW;
        }

        return intval($outOfStockBehaviour) === self::OUT_OF_STOCK_ALLOW;
    }

    /**
     * get all specific prices
     *
     * @return SpecificPrice[]|null
     */
    public function getSpecificPrices()
    {
        return SpecificPrice::getSpecificPrices($this);
    }

    /**
     * get cheapest delivery price for product
     *
     * @return float
     */
    public function getCheapestDeliveryPrice()
    {
        if (is_null($this->cheapestDeliveryPrice)) {
            $cart = new Object\CoreShopCart();
            $cartItem = new Object\CoreShopCartItem();
            $cartItem->setPublished(true);
            $cartItem->setAmount(1);
            $cartItem->setProduct($this);
            $cart->setItems(array($cartItem));
            $cart->getItems();

            PriceRule::autoAddToCart($cart);
            $this->cheapestDeliveryPrice = $cart->getShipping();
        }

        return $this->cheapestDeliveryPrice;
    }

    /**
     * Determines if product should be indexed
     *
     * @return bool
     */
    public function getDoIndex()
    {
        return true;
    }

    /**
     * returns array of images.
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return Image[]
     */
    public function getImages()
    {
        throw new UnsupportedException("getImages is not supported for " . get_class($this));
    }

    /**
     * returns array of categories.
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return Category[]
     */
    public function getCategories()
    {
        throw new UnsupportedException("getCategories is not supported for " . get_class($this));
    }

    /**
     * returns retail price
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return float
     */
    public function getRetailPrice()
    {
        throw new UnsupportedException("getRetailPrice is not supported for " . get_class($this));
    }

    /**
     * returns name
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return string
     */
    public function getName()
    {
        throw new UnsupportedException("getName is not supported for " . get_class($this));
    }

    /**
     * returns TaxRuleGroup
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return TaxRuleGroup
     */
    public function getTaxRule()
    {
        throw new UnsupportedException("getTaxRule is not supported for " . get_class($this));
    }

    /**
     * returns wholesale price
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return float
     */
    public function getWholesalePrice()
    {
        throw new UnsupportedException("getWholesalePrice is not supported for " . get_class($this));
    }

    /**
     * returns is download product
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return string
     */
    public function getIsDownloadProduct()
    {
        throw new UnsupportedException("getIsDownloadProduct is not supported for " . get_class($this));
    }

    /**
     * returns is weight of the product
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return float
     */
    public function getWeight()
    {
        throw new UnsupportedException("getWeight is not supported for " . get_class($this));
    }

    /**
     * returns is width of the product
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return float
     */
    public function getWidth()
    {
        throw new UnsupportedException("getWidth is not supported for " . get_class($this));
    }

    /**
     * returns is height of the product
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return float
     */
    public function getHeight()
    {
        throw new UnsupportedException("getHeight is not supported for " . get_class($this));
    }

    /**
     * returns is depth of the product
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return float
     */
    public function getDepth()
    {
        throw new UnsupportedException("getDepth is not supported for " . get_class($this));
    }

    /**
     * returns current Quantity
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return int
     */
    public function getQuantity()
    {
        throw new UnsupportedException("getQuantity is not supported for " . get_class($this));
    }

    /**
     * set Quantity
     * this method has to be overwritten in Pimcore Object
     *
     * @param $quantity
     *
     * @throws UnsupportedException
     * @return int
     */
    public function setQuantity($quantity)
    {
        throw new UnsupportedException("setQuantity is not supported for " . get_class($this));
    }

    /**
     * returns out of stock Behaviour
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return int
     */
    public function getOutOfStockBehaviour()
    {
        throw new UnsupportedException("getOutOfStockBehaviour is not supported for " . get_class($this));
    }
}
