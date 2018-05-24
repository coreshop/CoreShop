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

namespace CoreShop\Component\Order\Model;

use Carbon\Carbon;
use CoreShop\Component\Payment\Model\PayableInterface;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;

interface OrderInterface extends SaleInterface, PayableInterface
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
     *
     * @deprecated use getLocaleCode
     */
    public function getOrderLanguage();

    /**
     * @param $orderLanguage
     *
     * @deprecated use setLocaleCode
     */
    public function setOrderLanguage($orderLanguage);

    /**
     * @return Carbon
     */
    public function getOrderDate();

    /**
     * @param Carbon $orderDate
     */
    public function setOrderDate($orderDate);

    /**
     * @return string
     */
    public function getOrderNumber();

    /**
     * @param string $orderNumber
     */
    public function setOrderNumber($orderNumber);

    /**
     * @return string
     */
    public function getToken();

    /**
     * @param string $token
     */
    public function setToken($token);

    /**
     * @return PaymentProviderInterface
     */
    public function getPaymentProvider();

    /**
     * @param PaymentProviderInterface $paymentProvider
     * @return PaymentProviderInterface
     */
    public function setPaymentProvider($paymentProvider);
}
