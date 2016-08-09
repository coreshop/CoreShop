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

namespace CoreShop\Model\PriceRule\Condition;

use CoreShop\Exception;
use CoreShop\Model\Cart\PriceRule;
use CoreShop\Model\Cart;
use CoreShop\Model\Product as ProductModel;
use CoreShop\Model\Zone as ZoneModel;
use CoreShop\Tool;

/**
 * Class Zones
 * @package CoreShop\Model\PriceRule\Condition
 */
class Zones extends AbstractCondition
{
    /**
     * @var int[]
     */
    public $zones;

    /**
     * @var string
     */
    public $type = 'zones';

    /**
     * @return int[]
     */
    public function getZones()
    {
        return $this->zones;
    }

    /**
     * @param int[] $zones
     */
    public function setZones($zones)
    {
        $this->zones = $zones;
    }

    /**
     * Check if Cart is Valid for Condition.
     *
     * @param Cart       $cart
     * @param PriceRule  $priceRule
     * @param bool|false $throwException
     *
     * @return bool
     *
     * @throws Exception
     */
    public function checkConditionCart(Cart $cart, PriceRule $priceRule, $throwException = false)
    {
        return $this->check($throwException);
    }

    /**
     * Check if Product is Valid for Condition.
     *
     * @param ProductModel $product
     * @param ProductModel\AbstractProductPriceRule $priceRule
     *
     * @return bool
     */
    public function checkConditionProduct(ProductModel $product, ProductModel\AbstractProductPriceRule $priceRule)
    {
        return $this->check();
    }

    /**
     * @param $throwException
     * @return bool
     * @throws Exception
     */
    protected function check($throwException = false)
    {
        $found = false;

        foreach($this->getZones() as $zoneId) {
            if ($zoneId !== Tool::getCountry()->getZoneId()) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            if ($throwException) {
                throw new Exception('You cannot use this voucher in your zone of delivery');
            }

            return false;
        }

        return true;
    }
}
