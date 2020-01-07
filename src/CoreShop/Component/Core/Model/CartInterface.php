<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Order\Model\CartInterface as BaseCartInterface;
use CoreShop\Component\Payment\Model\PaymentSettingsAwareInterface;
use CoreShop\Component\Shipping\Model\CarrierAwareInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;

interface CartInterface extends BaseCartInterface, ShippableInterface, CarrierAwareInterface, PaymentSettingsAwareInterface
{
    /**
     * @return bool
     */
    public function hasShippableItems();

    /**
     * @return float
     */
    public function getWeight();

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getShipping($withTax = true);

    /**
     * @return int
     */
    public function getShippingTax();

    /**
     * @return float
     */
    public function getShippingTaxRate();

    /**
     * @param int $shippingTaxRate
     */
    public function setShippingTaxRate($shippingTaxRate);

    /**
     * @param bool $needsRecalculation
     */
    public function setNeedsRecalculation($needsRecalculation);

    /**
     * @return null|bool
     */
    public function getNeedsRecalculation();
}
