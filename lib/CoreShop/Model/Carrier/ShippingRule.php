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

namespace CoreShop\Model\Carrier;

use CoreShop\Exception;
use CoreShop\Model\Carrier\ShippingRule\Action\AbstractAction;
use CoreShop\Model\Carrier\ShippingRule\Condition\AbstractCondition;
use CoreShop\Model\Cart;
use CoreShop\Model\Rules\AbstractRule;
use CoreShop\Model\User\Address;
use Pimcore\Cache;

/**
 * Class ShippingRule
 * @package CoreShop\Model\Carrier
 */
class ShippingRule extends AbstractRule
{
    /**
     * possible types of a condition.
     *
     * @var array
     */
    public static $availableConditions = array('countries', 'amount', 'weight');

    /**
     * possible types of a action.
     *
     * @var array
     */
    public static $availableActions = array('fixedPrice', 'additionAmount', 'additionPercent', 'discountAmount', 'discountPercent');

    /**
     * save model to database.
     */
    public function save()
    {
        parent::save();

        Cache::clearTag("coreshop_carrier_shipping_rule");
    }

    /**
     * Check if Shipping Rule is valid
     *
     * @param Cart $cart
     * @param Address $address
     *
     * @return bool
     */
    public function checkValidity(Cart $cart, Address $address)
    {
        $cacheKey = self::getCacheKey(get_called_class(), $this->getId() . "checkValidity");

        try {
            $valid = \Zend_Registry::get($cacheKey);
            if ($valid === false) {
                throw new Exception('Validation in registry is null');
            }

            return $valid;
        } catch (\Exception $e) {
            try {
                $valid = Cache::load($cacheKey);
                if ($valid === false) {
                    $valid = true;

                    foreach($this->getConditions() as $condition) {
                        if($condition instanceof AbstractCondition) {
                            if(!$condition->checkCondition($cart, $address, $this)) {
                                $valid = false;
                                break;
                            }
                        }
                    }

                    \Zend_Registry::set($cacheKey, $valid ? 1 : 0);
                    Cache::save( $valid ? 1 : 0, $cacheKey, array($cacheKey, 'coreshop_carrier_shipping_rule'));
                } else {
                    \Zend_Registry::set($cacheKey,  $valid ? 1 : 0);
                }

                return $valid;
            } catch (\Exception $e) {
                \Logger::warning($e->getMessage());
            }
        }

        return false;
    }

    /**
     * get price modifications
     *
     * @param Cart $cart
     * @param Address $address
     * @param $price
     * @return float
     */
    public function getPriceModification(Cart $cart, Address $address, $price) {
        $priceModification = 0;

        foreach($this->getActions() as $action) {
            if($action instanceof AbstractAction) {
                $priceModificator = $action->getPriceModification($cart, $address, $price);
                if($priceModificator !== 0) {
                    $priceModification += $action->getPriceModification($cart, $address, $price);
                }
            }
        }

        return $priceModification;
    }

    /**
     * get price
     *
     * @param Cart $cart
     * @param Address $address
     *
     * @return float
     */
    public function getPrice(Cart $cart, Address $address) {
        $price = 0;

        foreach($this->getActions() as $action) {
            if($action instanceof AbstractAction) {
                if($action->getPrice($cart, $address)) {
                    $price = $action->getPrice($cart, $address);
                }
            }
        }

        return $price + $this->getPriceModification($cart, $address, $price);
    }
}