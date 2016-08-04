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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model;

use CoreShop\Exception;
use CoreShop\Model\Carrier\AbstractRange;
use CoreShop\Model\Carrier\Dao;
use CoreShop\Model\Carrier\DeliveryPrice;
use CoreShop\Model\Carrier\ShippingRule;
use CoreShop\Model\Carrier\ShippingRuleGroup;
use CoreShop\Model\User\Address;
use CoreShop\Tool;
use Pimcore\Cache;
use Pimcore\Db;
use Pimcore\Model\Asset;

/**
 * Class Carrier
 * @package CoreShop\Model
 */
class Carrier extends AbstractModel
{
    const SHIPPING_METHOD_WEIGHT = 'weight';
    const SHIPPING_METHOD_PRICE = 'price';

    const RANGE_BEHAVIOUR_DEACTIVATE = 'deactivate';
    const RANGE_BEHAVIOUR_LARGEST = 'largest';

    /**
     * @var bool
     */
    protected static $isMultiShop = true;

    /**
     * @var array
     */
    public static $shippingMethods = array(self::SHIPPING_METHOD_PRICE, self::SHIPPING_METHOD_WEIGHT);

    /**
     * @var array
     */
    public static $rangeBehaviours = array(self::RANGE_BEHAVIOUR_LARGEST, self::RANGE_BEHAVIOUR_DEACTIVATE);

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $label;

    /**
     * @var string
     */
    public $delay;

    /**
     * @var bool
     */
    public $needsRange;

    /**
     * @var int
     */
    public $grade;

    /**
     * @var int
     */
    public $image;

    /**
     * @var string
     */
    public $trackingUrl;

    /**
     * @var bool
     */
    public $isFree;

    /**
     * @var string
     */
    public $shippingMethod;

    /**
     * @var int
     */
    public $taxRuleGroupId;

    /**
     * @var TaxRuleGroup
     */
    public $taxRuleGroup;

    /**
     * @var int
     */
    public $rangeBehaviour;

    /**
     * @var float
     */
    public $maxHeight;

    /**
     * @var float
     */
    public $maxWidth;

    /**
     * @var float
     */
    public $maxDepth;

    /**
     * @var float
     */
    public $maxWeight;

    /**
     * @var string
     */
    public $class;

    /**
     * @var int[]
     */
    public $shopIds;

    /**
     * get Carrier by ID.
     *
     * @param $id
     *
     * @return Carrier|null
     */
    public static function getById($id)
    {
        $id = intval($id);

        if ($id < 1) {
            return null;
        }

        $cacheKey = 'coreshop_carrier_'.$id;

        try {
            $carrier = \Zend_Registry::get($cacheKey);
            if (!$carrier) {
                throw new Exception('Carrier in registry is null');
            }

            return $carrier;
        } catch (\Exception $e) {
            try {
                if (!$carrier = Cache::load($cacheKey)) {
                    $db = Db::get();
                    $tableName = Dao::getTableName();

                    $data = $db->fetchRow('SELECT class FROM '.$tableName.' WHERE id = ?', $id);

                    $class = get_called_class();
                    if (is_array($data) && $data['class']) {
                        if (\Pimcore\Tool::classExists($data['class'])) {
                            $class = $data['class'];
                        } else {
                            \Logger::warning(sprintf("Carrier with ID %s has definied class '%s' which cannot be loaded.", $id, $data['class']));
                        }
                    }

                    $carrier = new $class();
                    $carrier->getDao()->getById($id);

                    \Zend_Registry::set($cacheKey, $carrier);
                    Cache::save($carrier, $cacheKey);
                } else {
                    \Zend_Registry::set($cacheKey, $carrier);
                }

                return $carrier;
            } catch (\Exception $e) {
                \Logger::warning($e->getMessage());
            }
        }

        return null;
    }

    /**
     * get all carriers.
     *
     * @return Carrier[]
     */
    public static function getAll()
    {
        $list = Carrier::getList();
        $list->setOrder('ASC');
        $list->setOrderKey('grade');

        return $list->getData();
    }

    /**
     * Get all available Carriers for cart.
     *
     * @param Cart|null $cart
     * @param Address   $address
     *
     * @return Carrier[]
     */
    public static function getCarriersForCart(Cart $cart = null, Address $address = null)
    {
        if (is_null($cart)) {
            $cart = Tool::prepareCart();
        }
        if (is_null($address)) {
            $address = Tool::getDeliveryAddress();
        }

        $carriers = self::getAll();
        $availableCarriers = array();

        foreach ($carriers as $carrier) {
            if ($carrier->checkCarrierForCart($cart, $address)) {
                $carrier->getDeliveryPrice($cart, true, $address); //Cache Delivery Price
                $availableCarriers[] = $carrier;
            }
        }

        $sortField = Configuration::get('SYSTEM.SHIPPING.CARRIER_SORT') ? Configuration::get('SYSTEM.SHIPPING.CARRIER_SORT') : 'price';

        if ($sortField === 'price') {
            $carriers = [];

            foreach ($availableCarriers as $carrier) {
                $price = $carrier->getDeliveryPrice($cart, $address);

                $carriers[$price . '' . $carrier->getId()] = $carrier;
            }

            ksort($carriers);

            $availableCarriers = $carriers;
        }
        else {
            usort($availableCarriers, function ($carrier1, $carrier2) use ($sortField, $cart, $address) {
                if ($carrier1->getGrade() === $carrier2->getGrade()) {
                    return 0;
                }

                return $carrier1->getGrade() < $carrier2->getGrade() ? -1 : 1;
            });
        }

        return $availableCarriers;
    }

    /**
     * Get cheapest carrier for cart.
     *
     * @param Cart $cart
     * @param Address $address
     *
     * @return Carrier|null
     */
    public static function getCheapestCarrierForCart(Cart $cart, Address $address = null)
    {
        $cacheKey = 'cheapest_carrier_' - $cart->getId();

        try {
            $cheapestProvider = \Zend_Registry::get($cacheKey);
            if (!$cheapestProvider) {
                throw new Exception($cacheKey.' in registry is null');
            }

            return $cheapestProvider;
        } catch (\Exception $e) {
            try {
                if (!$cheapestProvider = Cache::load($cacheKey)) {
                    $providers = self::getCarriersForCart($cart, $address);
                    $cheapestProvider = null;

                    foreach ($providers as $p) {
                        if ($cheapestProvider === null) {
                            $cheapestProvider = $p;
                        }
                        elseif ($cheapestProvider->getDeliveryPrice($cart, true) > $p->getDeliveryPrice($cart, true)) {
                            $cheapestProvider = $p;
                        }
                    }

                    if ($cheapestProvider instanceof self) {
                        return $cheapestProvider;
                    }

                    \Zend_Registry::set($cacheKey, $cheapestProvider);
                    Cache::save($cheapestProvider, $cacheKey);
                } else {
                    \Zend_Registry::set($cacheKey, $cheapestProvider);
                }

                return $cheapestProvider;
            } catch (\Exception $e) {
                \Logger::warning($e->getMessage());
            }
        }

        return null;
    }

    /**
     * save model to database.
     */
    public function save()
    {
        parent::save();

        Cache::clearTag("coreshop_carrier_shipping_rule");
    }

    /**
     * get shipping rule groups
     *
     * @return ShippingRuleGroup[]
     */
    public function getShippingRuleGroups() {
        $list = ShippingRuleGroup::getList();
        $list->setCondition("carrierId = ?", array($this->getId()));
        $list->setOrder("ASC");
        $list->setOrderKey("priority");

        return $list->getData();
    }

    /**
     * Get all shipping rules for this carrier
     *
     * @return ShippingRule[]
     */
    public function getShippingRules() {
        $groups = $this->getShippingRuleGroups();

        $rules = [];

        foreach($groups as $group) {
            if($group instanceof ShippingRuleGroup) {
                $rules[] = $group->getShippingRule();
            }
        }

        return $rules;
    }

    /**
     * Check if carrier is allowed for cart and address.
     *
     * @param Cart|null $cart
     * @param Address|null $address
     *
     * @return bool
     *
     * @throws \CoreShop\Exception\UnsupportedException
     */
    public function checkCarrierForCart(Cart $cart = null, Address $address = null)
    {
        //Carrier has no price, so its invalid!
        if (!$this->getMaxDeliveryPrice()) {
            return false;
        }

        $rules = $this->getShippingRules();

        foreach($rules as $rule) {
            if($rule instanceof ShippingRule) {
                if($rule->checkValidity($this, $cart, $address)) {
                    //if one rule is valid, carrier is allowed
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get the Ranges for this carrier.
     *
     * @return AbstractRange[]
     */
    public function getRanges()
    {
        if ($this->getShippingMethod() == 'weight') {
            $list = Carrier\RangeWeight::getList();
        } else {
            $list = Carrier\RangePrice::getList();
        }

        $list->setCondition('carrierId=?', array($this->getId()));
        $list->load();

        return $list->getData();
    }

    /**
     * Get max possible delivery price for this carrier.
     *
     * @param Cart $cart
     * @param Address $address
     *
     * @return float|bool
     */
    public function getMaxDeliveryPrice(Cart $cart = null, Address $address = null)
    {
        if(is_null($cart)) {
            $cart = Tool::prepareCart();
        }

        if (is_null($address)) {
            $address = Tool::getDeliveryAddress();
        }

        $rules = $this->getShippingRules();

        if (count($rules) === 0) {
            return false;
        }

        $maxPrice = 0;

        foreach ($rules as $rule) {
            if($rule instanceof ShippingRule) {
                $price = $rule->getPrice($cart, $address);

                if($price > $maxPrice) {
                    $maxPrice = $price;
                }
            }
        }

        return $maxPrice;
    }

    /**
     * get delivery price for carrier
     * 
     * @param Cart $cart
     * @param bool $withTax
     * @param Address|null $address
     * @return bool|DeliveryPrice|float|null
     */
    public function getDeliveryPrice(Cart $cart, $withTax = true, Address $address = null)
    {
        $price = false;
        
        if (is_null($address)) {
            $address = Tool::getDeliveryAddress();
        }

        if($cart->isFreeShipping()) {
            return 0;
        }

        if($this->getIsFree()) {
            return 0;
        }

        $rules = $this->getShippingRules();

        foreach($rules as $rule) {
            if($rule instanceof ShippingRule) {
                if($rule->checkValidity($this, $cart, $address)) {
                    $price = $rule->getPrice($cart, $address);
                    break;
                }
            }
        }

        if ($price === false) {
            if ($this->getRangeBehaviour() === self::RANGE_BEHAVIOUR_LARGEST) {
                $price = $this->getMaxDeliveryPrice($cart, $address);
            }
        }

        if ($price) {
            $calculator = $this->getTaxCalculator($cart->getCustomerAddressForTaxation() ? $cart->getCustomerAddressForTaxation() : null);
            
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
        }
        
        return $price;
    }

    /**
     * get delivery Tax for cart.
     *
     * @param Cart      $cart
     * @param Address|null $address
     *
     * @return float
     */
    public function getTaxAmount(Cart $cart, Address $address = null)
    {
        $taxCalculator = $this->getTaxCalculator($cart->getCustomerAddressForTaxation() ? $cart->getCustomerAddressForTaxation() : null);
        $deliveryPrice = $this->getDeliveryPrice($cart, false, $address);

        if ($taxCalculator) {
            return $taxCalculator->getTaxesAmount($deliveryPrice);
        }

        return 0;
    }

    /**
     * get delivery Tax rate for cart.
     *
     * @param Cart $cart
     *
     * @return int
     */
    public function getTaxRate(Cart $cart)
    {
        $taxCalculator = $this->getTaxCalculator($cart->getCustomerAddressForTaxation() ? $cart->getCustomerAddressForTaxation() : null);

        if ($taxCalculator) {
            return $taxCalculator->getTotalRate();
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
            $address = Tool::getDeliveryAddress();
        }

        $taxRule = $this->getTaxRuleGroup();

        if ($taxRule instanceof TaxRuleGroup) {
            $taxManager = TaxManagerFactory::getTaxManager($address, $taxRule->getId());
            $taxCalculator = $taxManager->getTaxCalculator();

            return $taxCalculator;
        }

        return false;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getDelay()
    {
        return $this->delay;
    }

    /**
     * @param string $delay
     */
    public function setDelay($delay)
    {
        $this->delay = $delay;
    }

    /**
     * @return int
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * @param int $grade
     */
    public function setGrade($grade)
    {
        $this->grade = $grade;
    }

    /**
     * @return int
     */
    public function getImage()
    {
        if (is_string($this->image)) {
            $asset = Asset::getByPath($this->image);

            if ($asset instanceof Asset) {
                $this->image = $asset;
            }
        }

        return $this->image;
    }

    /**
     * @param int $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return string
     */
    public function getTrackingUrl()
    {
        return $this->trackingUrl;
    }

    /**
     * @param string $trackingUrl
     */
    public function setTrackingUrl($trackingUrl)
    {
        $this->trackingUrl = $trackingUrl;
    }

    /**
     * @return bool
     */
    public function getIsFree()
    {
        return $this->isFree;
    }

    /**
     * @param bool $isFree
     */
    public function setIsFree($isFree)
    {
        $this->isFree = $isFree;
    }

    /**
     * @return string
     */
    public function getShippingMethod()
    {
        return $this->shippingMethod;
    }

    /**
     * @param string $shippingMethod
     */
    public function setShippingMethod($shippingMethod)
    {
        $this->shippingMethod = $shippingMethod;
    }

    /**
     * @return int
     */
    public function getTaxRuleGroupId()
    {
        return $this->taxRuleGroupId;
    }

    /**
     * @param int $taxRuleGroupId
     *
     * @throws Exception
     */
    public function setTaxRuleGroupId($taxRuleGroupId)
    {
        $this->taxRuleGroupId = $taxRuleGroupId;
    }

    /**
     * @return TaxRuleGroup
     */
    public function getTaxRuleGroup()
    {
        if (!$this->taxRuleGroup instanceof TaxRuleGroup) {
            $this->taxRuleGroup = TaxRuleGroup::getById($this->taxRuleGroupId);
        }

        return $this->taxRuleGroup;
    }

    /**
     * @param int|TaxRuleGroup $taxRuleGroup
     *
     * @throws Exception
     */
    public function setTaxRuleGroup($taxRuleGroup)
    {
        if (!$taxRuleGroup instanceof TaxRuleGroup) {
            throw new Exception('$taxRuleGroup must be instance of TaxRuleGroup');
        }

        $this->taxRuleGroup = $taxRuleGroup;
        $this->taxRuleGroupId = $taxRuleGroup->getId();
    }

    /**
     * @return int
     */
    public function getRangeBehaviour()
    {
        return $this->rangeBehaviour;
    }

    /**
     * @param int $rangeBehaviour
     */
    public function setRangeBehaviour($rangeBehaviour)
    {
        $this->rangeBehaviour = $rangeBehaviour;
    }

    /**
     * @return float
     */
    public function getMaxHeight()
    {
        return $this->maxHeight;
    }

    /**
     * @param float $maxHeight
     */
    public function setMaxHeight($maxHeight)
    {
        $this->maxHeight = $maxHeight;
    }

    /**
     * @return float
     */
    public function getMaxWidth()
    {
        return $this->maxWidth;
    }

    /**
     * @param float $maxWidth
     */
    public function setMaxWidth($maxWidth)
    {
        $this->maxWidth = $maxWidth;
    }

    /**
     * @return float
     */
    public function getMaxDepth()
    {
        return $this->maxDepth;
    }

    /**
     * @param float $maxDepth
     */
    public function setMaxDepth($maxDepth)
    {
        $this->maxDepth = $maxDepth;
    }

    /**
     * @return float
     */
    public function getMaxWeight()
    {
        return $this->maxWeight;
    }

    /**
     * @param float $maxWeight
     */
    public function setMaxWeight($maxWeight)
    {
        $this->maxWeight = $maxWeight;
    }

    /**
     * @return bool
     */
    public function getNeedsRange()
    {
        return $this->needsRange;
    }

    /**
     * @param bool $needsRange
     */
    public function setNeedsRange($needsRange)
    {
        $this->needsRange = $needsRange;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return \int[]
     */
    public function getShopIds()
    {
        return $this->shopIds;
    }

    /**
     * @param \int[] $shopIds
     */
    public function setShopIds($shopIds)
    {
        $this->shopIds = $shopIds;
    }
}
