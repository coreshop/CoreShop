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

namespace CoreShop\Model\PriceRule\Condition;

use CoreShop\Exception;
use CoreShop\Model\Cart\PriceRule;
use CoreShop\Model\Cart;
use CoreShop\Model\Product as ProductModel;
use CoreShop\Model\User;

/**
 * Class Personas
 * @package CoreShop\Model\PriceRule\Condition
 */
class Personas extends AbstractCondition
{
    /**
     * @var string
     */
    public static $type = 'personas';

    /**
     * @var int[]
     */
    public $personas;

    /**
     * @return int[]
     */
    public function getPersonas()
    {
        return $this->personas;
    }

    /**
     * @param int[] $personas
     */
    public function setPersonas($personas)
    {
        $this->personas = $personas;
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
     * @param bool $throwException
     * @return bool
     * @throws Exception
     */
    protected function check($throwException = false)
    {
        $targetingRulesIds = json_decode("[" . $_REQUEST["_ptc"] . "]", true);
        $found = false;

        foreach ($targetingRulesIds as $id) {
            foreach ($this->getPersonas() as $persona) {
                if ($id === $persona) {
                    $found = true;
                    break;
                }
            }

            if ($found) {
                break;
            }
        }

        if ($found) {
            return true;
        } else {
            if ($throwException) {
                throw new Exception('You cannot use this voucher');
            }
        }

        return false;
    }
}
