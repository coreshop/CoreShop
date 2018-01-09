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

use CoreShop\Component\Order\Model\OrderInterface as BaseOrderInterface;
use CoreShop\Component\Shipping\Model\CarrierAwareInterface;

interface OrderInterface extends BaseOrderInterface, CarrierAwareInterface
{
    /**
     * @return string
     */
    public function getOrderState();

    /**
     * @param string $orderState
     */
    public function setOrderState($orderState);

    /**
     * @return string
     */
    public function getPaymentState();

    /**
     * @param string $paymentState
     */
    public function setPaymentState($paymentState);

    /**
     * @return string
     */
    public function getShippingState();

    /**
     * @param string $shippingState
     */
    public function setShippingState($shippingState);

    /**
     * @return string
     */
    public function getInvoiceState();

    /**
     * @param string $invoiceState
     */
    public function setInvoiceState($invoiceState);
}