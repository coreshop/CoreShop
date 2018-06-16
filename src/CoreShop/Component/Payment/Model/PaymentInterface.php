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

namespace CoreShop\Component\Payment\Model;

use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\TimestampableInterface;

interface PaymentInterface extends \Payum\Core\Model\PaymentInterface, ResourceInterface, TimestampableInterface
{
    const STATE_NEW = 'new';
    const STATE_PROCESSING = 'processing';
    const STATE_COMPLETED = 'completed';
    const STATE_FAILED = 'failed';
    const STATE_CANCELLED = 'cancelled';
    const STATE_REFUNDED = 'refunded';
    const STATE_UNKNOWN = 'unknown';

    /**
     * @return PaymentProviderInterface
     */
    public function getPaymentProvider();

    /**
     * @param $paymentProvider
     *
     * @return mixed
     */
    public function setPaymentProvider(PaymentProviderInterface $paymentProvider);

    /**
     * @return mixed
     */
    public function getDatePayment();

    /**
     * @param $datePayment
     */
    public function setDatePayment($datePayment);

    /**
     * @return mixed
     */
    public function getState();

    /**
     * @param $state
     */
    public function setState($state);

    /**
     * @return CurrencyInterface
     */
    public function getCurrency();

    /**
     * @param CurrencyInterface $currency
     */
    public function setCurrency($currency);

    /**
     * @param int $amount
     */
    public function setTotalAmount($amount);

    /**
     * @param $number
     */
    public function setNumber($number);

    /**
     * @deprecated setOrderId is deprecated since 2.0.0-beta.2 and will be removed in 2.0.0, use setOrder from Core Component instead
     *
     * @param int $orderId
     */
    public function setOrderId($orderId);

    /**
     * @deprecated getOrderId is deprecated since 2.0.0-beta.2 and will be removed in 2.0.0, use getOrder from Core Component instead
     *
     * @return int
     */
    public function getOrderId();
}
