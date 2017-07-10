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
use CoreShop\Component\Payment\Model\PaymentInterface;

interface OrderInterface extends SaleInterface
{
    /**
     * @return string
     */
    public function getOrderLanguage();

    /**
     * @param $orderLanguage
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
     * @return PaymentInterface[]
     */
    public function getPayments();

    /**
     * @return int
     */
    public function getTotalPayed();

    /**
     * @return bool
     */
    public function getIsPayed();

    /**
     * @param int $paymentFee
     * @param bool $withTax
     */
    public function setPaymentFee($paymentFee, $withTax = true);

    /**
     * @param bool $withTax
     * @return int
     */
    public function getPaymentFee($withTax = true);

    /**
     * @param int $paymentFee
     * @param bool $withTax
     */
    public function setBasePaymentFee($paymentFee, $withTax = true);

    /**
     * @param bool $withTax
     * @return int
     */
    public function getBasePaymentFee($withTax = true);

    /**
     * @return mixed
     */
    public function getPaymentProvider();

    /**
     * @param $paymentProvider
     *
     * @return mixed
     */
    public function setPaymentProvider($paymentProvider);

    /**
     * @return int
     */
    public function getPaymentFeeTaxRate();

    /**
     * @param int $paymentFeeTaxRate
     */
    public function setPaymentFeeTaxRate($paymentFeeTaxRate);
}
