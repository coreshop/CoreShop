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
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use CoreShop\Component\Store\Model\StoreInterface;
use Pimcore\Model\Object\Fieldcollection;

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
     * @return float
     */
    public function getTotalPayed();

    /**
     * @return bool
     */
    public function getIsPayed();

    /**
     * @param $paymentFee
     * @param bool $withTax
     */
    public function setPaymentFee($paymentFee, $withTax = true);

    /**
     * @param bool $withTax
     * @return float
     */
    public function getPaymentFee($withTax = true);

    /**
     * @param $paymentFee
     * @param bool $withTax
     */
    public function setBasePaymentFee($paymentFee, $withTax = true);

    /**
     * @param $taxRate
     */
    public function setPaymentFeeTaxRate($taxRate);
}
