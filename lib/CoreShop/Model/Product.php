<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model;

use CoreShop\Model\Cart\Item;
use CoreShop\Model\Cart\PriceRule;
use CoreShop\Model\PriceRule\Action\AbstractAction;
use CoreShop\Model\PriceRule\Condition\AbstractCondition;
use CoreShop\Model\Product\SpecificPrice;
use CoreShop\Model\User\Address;
use Pimcore\Cache;
use Pimcore\Model\Asset;
use Pimcore\Model\Object;
use Pimcore\Model\Asset\Image;
use CoreShop\Tool;
use CoreShop\Exception\ObjectUnsupportedException;
use CoreShop\Tool\Service;

/**
 * Class Product
 * @package CoreShop\Model
 *
 * @method static Object\Listing\Concrete getByLocalizedfields ($field, $value, $locale = null, $limit = 0)
 * @method static Object\Listing\Concrete getByEan ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByArticleNumber ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByEnabled ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByAvailableForOrder ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByIsDownloadProduct ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByManufacturer ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByShops ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByCategories ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByWholesalePrice ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByRetailPrice ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByTaxRule ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByPriceWithTax ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByQuantity ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByOutOfStockBehaviour ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByDepth ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByWidth ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByHeight ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByWeight ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByImages ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByCustomProperties ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByVariants ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByClassificationStore ($value, $limit = 0)
 */
class Product extends Base
{
    /**
     * Pimcore Object Class.
     *
     * @var string
     */
    public static $pimcoreClass = 'Pimcore\\Model\\Object\\CoreShopProduct';

    /**
     * OUT_OF_STOCK_DENY denies order of product if out-of-stock.
     */
    const OUT_OF_STOCK_DENY = 0;

    /**
     * OUT_OF_STOCK_ALLOW allows order of product if out-of-stock.
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
     * @var []
     */
    protected $validPriceRules = null;

    /**
     * @static
     *
     * @param int $id
     *
     * @return null|Product
     */
    public static function getById($id)
    {
        $object = Object\AbstractObject::getById($id);

        if ($object instanceof self) {
            return $object;
        }

        return null;
    }

    /**
     * Get all Products.
     *
     * @return array
     */
    public static function getAll()
    {
        $list = self::getList();
        $list->setCondition('enabled=1');

        return $list->load();
    }

    /**
     * Get latest Products.
     *
     * @param int $limit
     *
     * @return array|mixed
     */
    public static function getLatest($limit = 8)
    {
        $cacheKey = 'coreshop_latest';

        if (!$objects = \Pimcore\Cache::load($cacheKey)) {
            $list = self::getList();
            $list->setCondition("enabled = 1 AND shops LIKE '%,".Shop::getShop()->getId().",%'");
            $list->setOrderKey('o_creationDate');
            $list->setOrder('DESC');

            if ($limit) {
                $list->setLimit($limit);
            }

            $objects = $list->load();
        }

        return $objects;
    }

    /**
     * get price cache tag for product-id
     *
     * @param Product $product
     * @return string
     */
    public static function getPriceCacheTag($product)
    {
        return 'coreshop_product_'.$product->getId().'_price_' . $product->getTaxRate();
    }

    /**
     * Get Image for Product.
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
     * Get default Image for Product.
     *
     * @return bool|\Pimcore\Model\Asset
     */
    public function getDefaultImage()
    {
        $defaultImage = Configuration::get('SYSTEM.PRODUCT.DEFAULTIMAGE');

        if ($defaultImage) {
            $image = Image::getByPath($defaultImage);

            if ($image instanceof Image) {
                return $image;
            }
        }

        return false;
    }

    /**
     * Get Product is new.
     *
     * @return bool
     */
    public function getIsNew()
    {
        $markAsNew = Configuration::get('SYSTEM.PRODUCT.DAYSASNEW');

        if (is_int($markAsNew) && $markAsNew > 0) {
            $creationDate = new \Zend_Date($this->getCreationDate());
            $nowDate = new \Zend_Date();

            $diff = $nowDate->sub($creationDate)->toValue();
            $days = ceil($diff / 60 / 60 / 24) + 1;

            if ($days <= $markAsNew) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if Product is in Categry.
     *
     * @param Category $category
     *
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
     * Get all Variants Differences.
     *
     * @param $language
     * @param $type
     * @param $field
     *
     * @return array
     */
    public function getVariantDifferences($language = null, $type = 'objectbricks', $field = 'variants')
    {
        $cacheKey = 'coreshop_variant_differences'.$this->getId();

        if ($differences = Cache::load($cacheKey)) {
            return $differences;
        }

        if ($language) {
            $language = \Zend_Registry::get('Zend_Locale')->getLanguage();
        }

        $master = $this->getVariantMaster();

        if ($master instanceof self) {
            $differences = Service::getProductVariations($master, $this, $type, $field, $language);

            Cache::save($differences, $cacheKey);

            return $differences;
        }

        return false;
    }

    /**
     * Clear Cache for this Product Price
     */
    public function clearPriceCache()
    {
        Cache::clearTag(self::getPriceCacheTag($this));
    }

    /**
     * get all valid specific price riles
     *
     * @return SpecificPrice[]
     */
    public function getValidSpecificPriceRules()
    {
        if(is_null($this->validPriceRules)) {
            $specificPrices = $this->getSpecificPrices();
            $rules = [];

            foreach ($specificPrices as $specificPrice) {
                $conditions = $specificPrice->getConditions();

                $isValid = true;

                foreach ($conditions as $condition) {
                    if ($condition instanceof AbstractCondition) {
                        if (!$condition->checkConditionProduct($this, $specificPrice)) {
                            $isValid = false;
                            break;
                        }
                    }
                }

                //Conditions are not valid, so continue with next rule
                if (!$isValid) {
                    continue;
                }

                $rules[] = $specificPrice;
            }

            $this->validPriceRules = $rules;
        }

        return $this->validPriceRules;
    }

    /**
     * Get Specific Price.
     *
     * @return float|boolean
     */
    public function getSpecificPrice()
    {
        $specificPrices = $this->getValidSpecificPriceRules();
        $price = false;

        foreach ($specificPrices as $specificPrice) {
            $actions = $specificPrice->getActions();

            foreach ($actions as $action) {
                if ($action instanceof AbstractAction) {
                    $actionsPrice = $action->getPrice($this);

                    if ($actionsPrice !== false) {
                        $price = $actionsPrice;
                    }
                }
            }
        }

        return $price;
    }

    /**
     * Get Discount from Specific Prices.
     *
     * @todo: add some caching?!
     *
     * @return float
     */
    public function getDiscount()
    {
        $price = $this->getSalesPrice(false);

        $specificPrices = $this->getValidSpecificPriceRules();
        $discount = 0;

        foreach ($specificPrices as $specificPrice) {
            $actions = $specificPrice->getActions();

            foreach ($actions as $action) {
                $discount += $action->getDiscountProduct($price, $this);
            }
        }

        //TODO: With this, we can apply post-tax discounts, but this needs to be more tested
        /*if(Tool::getPricesAreGross()) {
            $taxCalculator = $this->getTaxCalculator();

            if($taxCalculator) {
                $discount = $taxCalculator->removeTaxes($discount);
            }
        }*/

        return $discount;
    }

    /**
     * Get Sales Price (without discounts), with or without taxes
     *
     * @param bool $withTax
     * @return float
     */
    public function getSalesPrice($withTax = true)
    {
        $cacheKey = self::getPriceCacheTag($this);

        if ((!$price = Cache::load($cacheKey)) || true) {
            $price = $this->getRetailPrice();
            $specificPrice = $this->getSpecificPrice();

            if ($specificPrice) {
                $price = $specificPrice;
            }

            Cache::save($price, $cacheKey, array('coreshop_product_price', $cacheKey));
        }

        $calculator = $this->getTaxCalculator();

        if ($withTax) {
            if (!Tool::getPricesAreGross()) {
                if ($calculator) {
                    $price = $calculator->addTaxes($price);
                }
            }
        } else {
            if (Tool::getPricesAreGross()) {
                if ($calculator) {
                    $price = $calculator->removeTaxes($price);
                }
            }
        }

        return $price;
    }

    /**
     * Get Product Price with Tax.
     *
     * @param boolean $withTax
     *
     * @return float|mixed
     *
     * @throws Exception
     */
    public function getPrice($withTax = true)
    {
        $netPrice = $this->getSalesPrice(false);

        //Apply Discounts on Price, currently, only net-discounts are supported
        $netPrice = $netPrice - $this->getDiscount();

        if ($withTax) {
            $calculator = $this->getTaxCalculator();

            if ($calculator) {
                $netPrice = $calculator->addTaxes($netPrice);
            }
        }

        return Tool::convertToCurrency($netPrice);
    }

    /**
     * returns variant with cheapest price.
     *
     * @return float|mixed
     */
    public function getCheapestVariantPrice()
    {
        $cacheKey = 'coreshop_product_cheapest_variant_price_'.$this->getId();

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
     * Get Tax Rate.
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
     * Get Product Tax Amount.
     *
     * @param bool $asArray
     *
     * @return float
     */
    public function getTaxAmount($asArray = false)
    {
        $calculator = $this->getTaxCalculator();

        if ($calculator) {
            return $calculator->getTaxesAmount($this->getPrice(false), $asArray);
        }

        return 0;
    }

    /**
     * get TaxCalculator.
     *
     * @param Address $address
     *
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
     * Adds $delta to current Quantity.
     *
     * @param $delta
     */
    public function updateQuantity($delta)
    {
        $this->setQuantity($this->getQuantity() + $delta);
        $this->save();
    }

    /**
     * Is Available when out-of-stock.
     *
     * @return bool
     *
     * @throws ObjectUnsupportedException
     */
    public function isAvailableWhenOutOfStock()
    {
        $outOfStockBehaviour = $this->getOutOfStockBehaviour();

        if (is_null($outOfStockBehaviour)) {
            $outOfStockBehaviour = self::OUT_OF_STOCK_DEFAULT;
        }

        if (intval($outOfStockBehaviour) === self::OUT_OF_STOCK_DEFAULT) {
            return intval(Configuration::get('SYSTEM.STOCK.DEFAULTOUTOFSTOCKBEHAVIOUR')) === self::OUT_OF_STOCK_ALLOW;
        }

        return intval($outOfStockBehaviour) === self::OUT_OF_STOCK_ALLOW;
    }

    /**
     * get all specific prices.
     *
     * @return SpecificPrice[]|null
     */
    public function getSpecificPrices()
    {
        return array_merge(SpecificPrice::getSpecificPrices($this), Product\PriceRule::getPriceRules());
    }

    /**
     * get cheapest delivery price for product.
     *
     * @return float
     */
    public function getCheapestDeliveryPrice()
    {
        if (is_null($this->cheapestDeliveryPrice)) {
            $cart = Cart::create();
            $cartItem = Item::create();
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
     * Determines if product should be indexed.
     *
     * @return bool
     */
    public function getDoIndex()
    {
        return true;
    }

    /**
     * @return string
     *
     * @throws ObjectUnsupportedException
     */
    public function getEan()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param string $ean
     *
     * @throws ObjectUnsupportedException
     */
    public function setEan($ean)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return string
     *
     * @throws ObjectUnsupportedException
     */
    public function getArticleNumber()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param string $articleNumber
     *
     * @throws ObjectUnsupportedException
     */
    public function setArticleNumber($articleNumber)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return boolean
     *
     * @throws ObjectUnsupportedException
     */
    public function getEnabled()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param boolean $enabled
     *
     * @throws ObjectUnsupportedException
     */
    public function setEnabled($enabled)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return boolean
     *
     * @throws ObjectUnsupportedException
     */
    public function getAvailableForOrder()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param boolean $availableForOrder
     *
     * @throws ObjectUnsupportedException
     */
    public function setAvailableForOrder($availableForOrder)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return boolean
     *
     * @throws ObjectUnsupportedException
     */
    public function getIsDownloadProduct()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param boolean $isDownloadProduct
     *
     * @throws ObjectUnsupportedException
     */
    public function setIsDownloadProduct($isDownloadProduct)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return Manufacturer|null
     *
     * @throws ObjectUnsupportedException
     */
    public function getManufacturer()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param Manufacturer|int $manufacturer
     *
     * @throws ObjectUnsupportedException
     */
    public function setManufacturer($manufacturer)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return int[]
     *
     * @throws ObjectUnsupportedException
     */
    public function getShops()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param int[] $shops
     *
     * @throws ObjectUnsupportedException
     */
    public function setShops($shops)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return Category[]
     *
     * @throws ObjectUnsupportedException
     */
    public function getCategories()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param Category[] $categories
     *
     * @throws ObjectUnsupportedException
     */
    public function setCategories($categories)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return double
     *
     * @throws ObjectUnsupportedException
     */
    public function getWholesalePrice()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param double $wholesalePrice
     *
     * @throws ObjectUnsupportedException
     */
    public function setWholesalePrice($wholesalePrice)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return double
     *
     * @throws ObjectUnsupportedException
     */
    public function getRetailPrice()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param double $retailPrice
     *
     * @throws ObjectUnsupportedException
     */
    public function setRetailPrice($retailPrice)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return TaxRule|null mixed
     *
     * @throws ObjectUnsupportedException
     */
    public function getTaxRule()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param TaxRuleGroup $taxRule
     *
     * @throws ObjectUnsupportedException
     */
    public function setTaxRule($taxRule)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return int
     *
     * @throws ObjectUnsupportedException
     */
    public function getQuantity()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param int $quantity
     *
     * @throws ObjectUnsupportedException
     */
    public function setQuantity($quantity)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return int
     *
     * @throws ObjectUnsupportedException
     */
    public function getOutOfStockBehaviour()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param int $outOfStockBehaviour
     *
     * @throws ObjectUnsupportedException
     */
    public function setOutOfStockBehaviour($outOfStockBehaviour)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return float
     *
     * @throws ObjectUnsupportedException
     */
    public function getDepth()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param float $depth
     *
     * @throws ObjectUnsupportedException
     */
    public function setDepth($depth)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param float $width
     *
     * @throws ObjectUnsupportedException
     */
    public function setWidth($width)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return float
     *
     * @throws ObjectUnsupportedException
     */
    public function getHeight()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param float $height
     *
     * @throws ObjectUnsupportedException
     */
    public function setHeight($height)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return float
     *
     * @throws ObjectUnsupportedException
     */
    public function getWeight()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param float $weight
     *
     * @throws ObjectUnsupportedException
     */
    public function setWeight($weight)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return Asset[]
     *
     * @throws ObjectUnsupportedException
     */
    public function getImages()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param Asset[] $images
     *
     * @throws ObjectUnsupportedException
     */
    public function setImages($images)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return mixed
     *
     * @throws ObjectUnsupportedException
     */
    public function getCustomProperties()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param mixed $customProperties
     *
     * @throws ObjectUnsupportedException
     */
    public function setCustomProperties($customProperties)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return mixed
     *
     * @throws ObjectUnsupportedException
     */
    public function getVariants()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param mixed $variants
     *
     * @throws ObjectUnsupportedException
     */
    public function setVariants($variants)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return mixed
     *
     * @throws ObjectUnsupportedException
     */
    public function getClassificationStore()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param mixed $classificationStore
     *
     * @throws ObjectUnsupportedException
     */
    public function setClassificationStore($classificationStore)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }
}
