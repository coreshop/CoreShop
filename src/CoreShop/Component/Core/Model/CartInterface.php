<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Order\Model\CartInterface as BaseCartInterface;
use CoreShop\Component\Shipping\Model\CarrierAwareInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;

interface CartInterface extends BaseCartInterface, ShippableInterface, CarrierAwareInterface
{
    /**
     * @param bool $withTax
     *
     * @return float
     */
    public function getShipping($withTax = true);

    /**
     * @param $shipping
     * @param bool $withTax
     */
    public function setShipping($shipping, $withTax = true);

    /**
     * @return float
     */
    public function getShippingTaxRate();

    /**
     * @param $shippingTaxRate
     */
    public function setShippingTaxRate($shippingTaxRate);
}
