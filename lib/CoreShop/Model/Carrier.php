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
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model;

use CoreShop\Exception;
use CoreShop\Model\Carrier\ShippingRule;
use CoreShop\Model\Carrier\ShippingRuleGroup;
use CoreShop\Model\User\Address;
use Pimcore\Cache;
use Pimcore\Db;
use Pimcore\Logger;
use Pimcore\Model\Asset;
use Pimcore\Tool;

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
    public static $shippingMethods = [self::SHIPPING_METHOD_PRICE, self::SHIPPING_METHOD_WEIGHT];

    /**
     * @var array
     */
    public static $rangeBehaviours = [self::RANGE_BEHAVIOUR_LARGEST, self::RANGE_BEHAVIOUR_DEACTIVATE];

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

        $cacheKey = self::getClassCacheKey(get_called_class(), $id);

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

                    $obj = static::create();
                    $tableName = $obj->getDao()->getTableName();

                    $data = $db->fetchRow('SELECT class FROM '.$tableName.' WHERE id = ?', $id);

                    $class = get_called_class();
                    if (is_array($data) && $data['class']) {
                        if (Tool::classExists($data['class'])) {
                            $class = $data['class'];
                        } else {
                            Logger::warning(sprintf("Carrier with ID %s has definied class '%s' which cannot be loaded.", $id, $data['class']));
                        }
                    } else {
                        if (\Pimcore::getDiContainer()->has($class)) {
                            $class = \Pimcore::getDiContainer()->make($class);
                        }
                    }

                    /**
                     * @var $carrier static
                     */
                    $carrier = new $class();
                    $carrier->getDao()->getById($id);

                    \Zend_Registry::set($cacheKey, $carrier);
                    Cache::save($carrier, $cacheKey, [$carrier->getCacheKey()]);
                } else {
                    \Zend_Registry::set($cacheKey, $carrier);
                }

                return $carrier;
            } catch (\Exception $e) {
                Logger::warning($e->getMessage());
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
            $cart = \CoreShop::getTools()->getCart();
        }
        if (is_null($address)) {
            $address = \CoreShop::getTools()->getDeliveryAddress();
        }

        $carriers = self::getAll();
        $availableCarriers = [];

        foreach ($carriers as $carrier) {
            if ($carrier->checkCarrierForCart($cart, $address)) {
                $carrier->getDeliveryPrice($cart, true, $address); //Cache Delivery Price
                $availableCarriers[] = $carrier;
            }
        }

        $sortField = Configuration::get('SYSTEM.SHIPPING.CARRIER_SORT') ? Configuration::get('SYSTEM.SHIPPING.CARRIER_SORT') : 'price';

        if ($sortField === 'price') {
            //Hopefully this one works better...
            foreach ($availableCarriers as $carrier) {
                $carrier->getDeliveryPrice($cart, $address);
            }

            usort($availableCarriers, function ($carrier1, $carrier2) use ($cart, $address) {
                return $carrier1->getDeliveryPrice($cart, $address) > $carrier2->getDeliveryPrice($cart, $address);
            });
        } else {
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
        $cacheKey = static::getClassCacheKey(get_called_class(), "cheapest_" . $cart->getId());

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
                        } elseif ($cheapestProvider->getDeliveryPrice($cart, true) > $p->getDeliveryPrice($cart, true)) {
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
                Logger::warning($e->getMessage());
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
    public function getShippingRuleGroups()
    {
        $list = ShippingRuleGroup::getList();
        $list->setCondition("carrierId = ?", [$this->getId()]);
        $list->setOrder("ASC");
        $list->setOrderKey("priority");

        return $list->getData();
    }

    /**
     * Get all shipping rules for this carrier
     *
     * @return ShippingRule[]
     */
    public function getShippingRules()
    {
        $groups = $this->getShippingRuleGroups();

        $rules = [];

        foreach ($groups as $group) {
            if ($group instanceof ShippingRuleGroup) {
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

        foreach ($rules as $rule) {
            if ($rule instanceof ShippingRule) {
                if ($rule->checkValidity($this, $cart, $address)) {
                    //if one rule is valid, carrier is allowed
                    return true;
                }
            }
        }

        return false;
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
        if (is_null($cart)) {
            $cart = \CoreShop::getTools()->getCart();
        }

        if (is_null($address)) {
            $address = \CoreShop::getTools()->getDeliveryAddress();
        }

        $rules = $this->getShippingRules();

        if (count($rules) === 0) {
            return false;
        }

        $maxPrice = 0;

        foreach ($rules as $rule) {
            if ($rule instanceof ShippingRule) {
                $price = $rule->getPrice($this, $cart, $address);

                if ($price > $maxPrice) {
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
     * @return float
     */
    public function getDeliveryPrice(Cart $cart, $withTax = true, Address $address = null)
    {
        $price = null;

        if (is_null($address)) {
            $address = \CoreShop::getTools()->getDeliveryAddress();
        }

        if ($cart->isFreeShipping()) {
            return 0;
        }

        if ($this->getIsFree()) {
            return 0;
        }

        $rules = $this->getShippingRules();

        foreach ($rules as $rule) {
            if ($rule instanceof ShippingRule) {
                if ($rule->checkValidity($this, $cart, $address)) {
                    $price = $rule->getPrice($this, $cart, $address);
                    break;
                }
            }
        }

        if (is_null($price)) {
            if ($this->getRangeBehaviour() == self::RANGE_BEHAVIOUR_LARGEST) {
                $price = $this->getMaxDeliveryPrice($cart, $address);
            }
        }

        if ($price) {
            $calculator = $this->getTaxCalculator($cart->getCustomerAddressForTaxation() ? $cart->getCustomerAddressForTaxation() : null);
            
            if ($withTax) {
                if (!\CoreShop::getTools()->getPricesAreGross()) {
                    if ($calculator) {
                        $price = $calculator->addTaxes($price);
                    }
                }
            } else {
                if (\CoreShop::getTools()->getPricesAreGross()) {
                    if ($calculator) {
                        $price = $calculator->removeTaxes($price);
                    }
                }
            }

            return $price;
        }

        return 0;
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
            $address = \CoreShop::getTools()->getDeliveryAddress();
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
     * @return string
     */
    public function __toString()
    {
        return sprintf("%s (%s)", $this->getName(), $this->getId());
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
     * @return Asset\Image|null
     */
    public function getImageAsset() {
        return Asset\Image::getById($this->image);
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
     * @return int[]
     */
    public function getShopIds()
    {
        return $this->shopIds;
    }

    /**
     * @param int[] $shopIds
     */
    public function setShopIds($shopIds)
    {
        $this->shopIds = $shopIds;
    }
}
