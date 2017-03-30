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

namespace CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier\ShippingRule\Action;

use CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier\ShippingRule as CarrierShippingRule;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart;
use CoreShop\Bundle\CoreShopLegacyBundle\Model;

/**
 * Class ShippingRule
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Model\PriceRule\Action
 */
class ShippingRule extends AbstractAction
{
    /**
     * @var string
     */
    public static $type = 'shippingRule';

    /**
     * @var int
     */
    public $shippingRule;


    /**
     * get addition/discount for shipping
     *
     * @param Model\Carrier $carrier
     * @param Cart $cart
     * @param Model\User\Address $address
     * @param float $price
     *
     * @return float
     */
    public function getPriceModification(Model\Carrier $carrier, Cart $cart, Model\User\Address $address, $price)
    {
        $carrierShippingRule = CarrierShippingRule::getById($this->getShippingRule());

        if ($carrierShippingRule instanceof CarrierShippingRule) {
            return $carrierShippingRule->getPrice($carrier, $cart, $address);
        }

        return 0;
    }

    /**
     * @return int
     */
    public function getShippingRule()
    {
        return $this->shippingRule;
    }

    /**
     * @param int $shippingRule
     */
    public function setShippingRule($shippingRule)
    {
        $this->shippingRule = $shippingRule;
    }
}
