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

use CoreShop\Model\Carrier\AbstractRange;
use CoreShop\Model\Carrier\DeliveryPrice;
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
                throw new \Exception('Carrier in registry is null');
            }

            return $carrier;
        } catch (\Exception $e) {
            try {
                if (!$carrier = Cache::load($cacheKey)) {
                    $db = Db::get();
                    //Todo: TableName already definied within 2 Dao files
                    $data = $db->fetchRow('SELECT class FROM coreshop_carriers WHERE id = ?', $id);

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
     * @param Zone      $zone
     *
     * @return Carrier[]
     */
    public static function getCarriersForCart(Cart $cart = null, Zone $zone = null)
    {
        if (is_null($cart)) {
            $cart = Tool::prepareCart();
        }
        if (is_null($zone)) {
            $zone = Tool::getCountry()->getZone();
        }

        $carriers = self::getAll();
        $availableCarriers = array();

        foreach ($carriers as $carrier) {
            if ($carrier->checkCarrierForCart($cart, $zone)) {
                $carrier->getDeliveryPrice($cart, true, $zone); //Cache Delivery Price
                $availableCarriers[] = $carrier;
            }
        }

        $sortField = Configuration::get('SYSTEM.SHIPPING.CARRIER_SORT') ? Configuration::get('SYSTEM.SHIPPING.CARRIER_SORT') : 'price';

        usort($availableCarriers, function ($carrier1, $carrier2) use ($sortField, $cart, $zone) {
            if ($sortField === 'price') {
                $deliveryPriceCarrier1 = $carrier1->getDeliveryPrice($cart, $zone);
                $deliveryPriceCarrier2 = $carrier2->getDeliveryPrice($cart, $zone);

                if ($deliveryPriceCarrier1 === $deliveryPriceCarrier2) {
                    return 0;
                }

                return $deliveryPriceCarrier1 < $deliveryPriceCarrier2 ? -1 : 1;
            } else {
                if ($carrier1->getGrade() === $carrier2->getGrade()) {
                    return 0;
                }

                return $carrier1->getGrade() < $carrier2->getGrade() ? -1 : 1;
            }
        });

        return $availableCarriers;
    }

    /**
     * Get cheapest carrier for cart.
     *
     * @param Cart $cart
     * @param Zone $zone
     *
     * @return Carrier|null
     */
    public static function getCheapestCarrierForCart(Cart $cart, Zone $zone = null)
    {
        $cacheKey = 'cheapest_carrier_' - $cart->getId();

        try {
            $cheapestProvider = \Zend_Registry::get($cacheKey);
            if (!$cheapestProvider) {
                throw new \Exception($cacheKey.' in registry is null');
            }

            return $cheapestProvider;
        } catch (\Exception $e) {
            try {
                if (!$cheapestProvider = Cache::load($cacheKey)) {
                    $providers = self::getCarriersForCart($cart, $zone);
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
                \Logger::warning($e->getMessage());
            }
        }

        return nul;
    }

    /**
     * Check if carrier is allowed for cart and zone.
     *
     * @param Cart|null $cart
     * @param Zone|null $zone
     *
     * @return bool
     *
     * @throws \CoreShop\Exception\UnsupportedException
     */
    public function checkCarrierForCart(Cart $cart = null, Zone $zone = null)
    {
        if (!$this->getMaxDeliveryPrice()) {
            return false;
        }

        //Check for Ranges
        if ($this->getRangeBehaviour() == self::RANGE_BEHAVIOUR_DEACTIVATE) {
            if ($this->getShippingMethod() == self::SHIPPING_METHOD_PRICE) {
                if (!$this->checkDeliveryPriceByValue($zone, $cart->getTotal())) {
                    return false;
                }
            }

            if ($this->getShippingMethod() == self::SHIPPING_METHOD_WEIGHT) {
                if (!$this->checkDeliveryPriceByValue($zone, $cart->getTotalWeight())) {
                    return false;
                }
            }
        }

        $carrierIsAllowed = true;

        //Check for max-size
        foreach ($cart->getItems() as $item) {
            $product = $item->getProduct();

            if (($this->getMaxWidth() > 0 && $product->getWidth() > $this->getMaxWidth())
                || ($this->getMaxHeight() > 0 && $product->getHeight() > $this->getMaxHeight())
                || ($this->getMaxDepth() > 0 && $product->getDepth() > $this->getMaxDepth())
                || ($this->getMaxWeight() > 0 && $product->getWeight() > $this->getMaxWeight())) {
                $carrierIsAllowed = false;
                break;
            }
        }

        if (!$carrierIsAllowed) {
            return false;
        }

        return true;
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
     * @param Zone $zone
     *
     * @return float|bool
     */
    public function getMaxDeliveryPrice(Zone $zone = null)
    {
        if (is_null($zone)) {
            $zone = Tool::getCountry()->getZone();
        }

        $ranges = $this->getRanges();

        if (count($ranges) === 0) {
            return false;
        }

        $maxPrice = 0;

        foreach ($ranges as $range) {
            $price = $range->getPriceForZone($zone);

            if ($price instanceof DeliveryPrice) {
                if ($price->getPrice() > $maxPrice) {
                    $maxPrice = $price->getPrice();
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
     * @param Zone|null $zone
     * @return bool|DeliveryPrice|float|null
     */
    public function getDeliveryPrice(Cart $cart, $withTax = true, Zone $zone = null) {
        $price = false;
        
        if (is_null($zone)) {
            $zone = Tool::getCountry()->getZone();
        }

        if ($this->getShippingMethod() === self::SHIPPING_METHOD_PRICE) {
            $value = $cart->getTotal();
        } else {
            $value = $cart->getTotalWeight();
        }

        $ranges = $this->getRanges();

        foreach ($ranges as $range) {
            $price = $range->getPriceForZone($zone);

            if ($price instanceof DeliveryPrice) {
                if ($value >= $range->getDelimiter1() && $value < $range->getDelimiter2()) {
                    $deliveryPrice = $price->getPrice();

                    $price = $deliveryPrice;
                    break;
                }
            }
        }

        if($price === false) {
            if ($this->getRangeBehaviour() === self::RANGE_BEHAVIOUR_LARGEST) {
                $deliveryPrice = $this->getMaxDeliveryPrice($zone);

                $price = $deliveryPrice;
            }
        }

        if($price) {
            $calculator = $this->getTaxCalculator($cart->getCustomerAddressForTaxation() ? $cart->getCustomerAddressForTaxation() : null);
            
            if($withTax) {
                if(!Tool::getPricesAreGross()) {
                    if ($calculator) {
                        $price = $calculator->addTaxes($price);
                    }
                }
            }
            else {
                if(Tool::getPricesAreGross()) {
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
     * @param Zone|null $zone
     *
     * @return float
     */
    public function getTaxAmount(Cart $cart, Zone $zone = null)
    {
        $taxCalculator = $this->getTaxCalculator($cart->getCustomerAddressForTaxation() ? $cart->getCustomerAddressForTaxation() : null);
        $deliveryPrice = $this->getDeliveryPrice($cart, false, $zone);

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
            $address = Address::create();
            $address->setCountry(Tool::getCountry());
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
     * Check if carrier is available for zone and value.
     *
     * @param Zone $zone
     * @param $value
     *
     * @return bool
     */
    public function checkDeliveryPriceByValue(Zone $zone, $value)
    {
        $ranges = $this->getRanges();

        foreach ($ranges as $range) {
            $price = $range->getPriceForZone($zone);

            if ($price instanceof DeliveryPrice) {
                if ($value >= $range->getDelimiter1() && $value < $range->getDelimiter2()) {
                    return true;
                }
            }
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
     * @throws \Exception
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
     * @throws \Exception
     */
    public function setTaxRuleGroup($taxRuleGroup)
    {
        if (!$taxRuleGroup instanceof TaxRuleGroup) {
            throw new \Exception('$taxRuleGroup must be instance of TaxRuleGroup');
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
}
