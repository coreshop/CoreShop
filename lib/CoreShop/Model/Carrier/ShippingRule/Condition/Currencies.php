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

namespace CoreShop\Model\Carrier\ShippingRule\Condition;

use CoreShop\Model;
use CoreShop\Model\Carrier\ShippingRule;
use CoreShop\Tool;

/**
 * Class Currencies
 * @package CoreShop\Model\Carrier\ShippingRule\Condition
 */
class Currencies extends AbstractCondition
{
    /**
     * @var string
     */
    public $type = 'currencies';

    /**
     * @var int[]
     */
    public $currencies;

    /**
     * Check if Cart is Valid for Condition.
     *
     * @param Model\Carrier $carrier
     * @param Model\Cart $cart
     * @param Model\User\Address $address;
     * @param ShippingRule $shippingRule
     *
     * @return mixed
     */
    public function checkCondition(Model\Carrier $carrier, Model\Cart $cart, Model\User\Address $address, ShippingRule $shippingRule)
    {
        foreach ($this->getCurrencies() as $currency) {
            if (Tool::getCurrency()->getId() === $currency) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return \int[]
     */
    public function getCurrencies()
    {
        return $this->currencies;
    }

    /**
     * @param \int[] $currencies
     */
    public function setCurrencies($currencies)
    {
        $this->currencies = $currencies;
    }
}
