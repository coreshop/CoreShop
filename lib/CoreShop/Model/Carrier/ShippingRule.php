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

namespace CoreShop\Model\Carrier;

use CoreShop\Exception;
use CoreShop\Model\Carrier;
use CoreShop\Model\Carrier\ShippingRule\Action;
use CoreShop\Model\Carrier\ShippingRule\Condition;
use CoreShop\Model\Carrier\ShippingRule\Action\AbstractAction;
use CoreShop\Model\Carrier\ShippingRule\Condition\AbstractCondition;
use CoreShop\Model\Cart;
use CoreShop\Model\Rules\AbstractRule;
use CoreShop\Model\User\Address;
use Pimcore\Cache;
use Pimcore\Logger;
use CoreShop\Composite\Dispatcher;

/**
 * Class ShippingRule
 * @package CoreShop\Model\Carrier
 */
class ShippingRule extends AbstractRule
{
    /**
     * @var string
     */
    public static $type = 'shippingRule';

    /**
     * @param Dispatcher $dispatcher
     */
    protected static function initConditionDispatcher(Dispatcher $dispatcher)
    {
        $dispatcher->addTypes([
            Condition\Conditions::class,
            Condition\Countries::class,
            Condition\Amount::class,
            Condition\Weight::class,
            Condition\Dimension::class,
            Condition\Zones::class,
            Condition\Postcodes::class,
            Condition\Products::class,
            Condition\Categories::class,
            Condition\CustomerGroups::class,
            Condition\Currencies::class,
            Condition\ShippingRule::class
        ]);
    }
    /**
     * @param Dispatcher $dispatcher
     */
    protected static function initActionDispatcher(Dispatcher $dispatcher)
    {
        $dispatcher->addTypes([
            Action\FixedPrice::class,
            Action\AdditionAmount::class,
            Action\AdditionPercent::class,
            Action\DiscountAmount::class,
            Action\DiscountPercent::class,
            Action\ShippingRule::class
        ]);
    }

    /**
     * @deprecated will be removed with 1.3
     *
     * @param $condition
     */
    public static function addCondition($condition)
    {
        $class = '\\CoreShop\\Model\\Carrier\\ShippingRule\\Condition\\' . ucfirst($condition);

        static::getConditionDispatcher()->addType($class);
    }

    /**
     * @deprecated will be removed with 1.3
     *
     * @param $action
     */
    public static function addAction($action)
    {
        $class = '\\CoreShop\\Model\\Carrier\\ShippingRule\\Action\\' . ucfirst($action);

        static::getActionDispatcher()->addType($class);
    }

    /**
     * Check if Shipping Rule is valid
     *
     * @param Carrier $carrier
     * @param Cart $cart
     * @param Address $address
     *
     * @return bool
     */
    public function checkValidity(Carrier $carrier, Cart $cart, Address $address)
    {
        $cacheKey = $this->getValidationCacheKey($carrier, $cart, $address);

        try {
            $valid = \Zend_Registry::get($cacheKey);
            if ($valid === false) {
                throw new Exception('Validation in registry is null');
            }

            return $valid;
        } catch (\Exception $e) {
            try {
                if (Cache::test($cacheKey)) {
                    $valid = Cache::load($cacheKey);

                    \Zend_Registry::set($cacheKey, $valid ? 1 : 0);
                } else {
                    $valid = true;

                    foreach ($this->getConditions() as $condition) {
                        if ($condition instanceof AbstractCondition) {
                            if (!$condition->checkCondition($carrier, $cart, $address, $this)) {
                                $valid = false;
                                break;
                            }
                        }
                    }


                    \Zend_Registry::set($cacheKey, $valid ? 1 : 0);
                    Cache::save($valid ? 1 : 0, $cacheKey, [$cacheKey, 'coreshop_carrier_shipping_rule']);
                }

                return $valid;
            } catch (\Exception $e) {
                Logger::warning($e->getMessage());
            }
        }

        return false;
    }

    /**
     * get cache key for shipping rule validation
     *
     * @param Carrier $carrier
     * @param Address $address
     * @param Cart $cart
     * @return string
     */
    public function getValidationCacheKey(Carrier $carrier, Cart $cart, Address $address)
    {
        $cacheKey = \CoreShop::getTools()->getFingerprint() . $carrier->getId() . $address->getCacheKey() . $cart->getCacheKey() . $this->getId();

        foreach ($this->getConditions() as $condition) {
            if ($condition instanceof AbstractCondition) {
                $cacheKey = $cacheKey . $condition->getCacheKey();
            }
        }

        return md5($cacheKey);
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
     * get price modifications
     *
     * @param Carrier $carrier
     * @param Cart $cart
     * @param Address $address
     * @param $price
     * @return float
     */
    public function getPriceModification(Carrier $carrier, Cart $cart, Address $address, $price)
    {
        $priceModification = 0;

        foreach ($this->getActions() as $action) {
            if ($action instanceof AbstractAction) {
                $actionModificator = $action->getPriceModification($carrier, $cart, $address, $price);

                if ($actionModificator !== 0) {
                    $priceModification += $actionModificator;
                }
            }
        }

        return $priceModification;
    }

    /**
     * get price
     *
     * @param Carrier $carrier
     * @param Cart $cart
     * @param Address $address
     *
     * @return float
     */
    public function getPrice(Carrier $carrier, Cart $cart, Address $address)
    {
        $price = 0;

        foreach ($this->getActions() as $action) {
            if ($action instanceof AbstractAction) {
                $actionPrice = $action->getPrice($carrier, $cart, $address);
                if ($actionPrice) {
                    $price = $actionPrice;
                }
            }
        }

        return $price + $this->getPriceModification($carrier, $cart, $address, $price);
    }
}
