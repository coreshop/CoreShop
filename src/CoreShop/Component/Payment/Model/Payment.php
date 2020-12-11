<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Payment\Model;

use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Resource\Model\SetValuesTrait;
use CoreShop\Component\Resource\Model\TimestampableTrait;

class Payment extends \Payum\Core\Model\Payment implements PaymentInterface
{
    use SetValuesTrait;
    use TimestampableTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var PaymentProviderInterface
     */
    protected $paymentProvider;

    /**
     * @var string
     */
    protected $currencyCode;

    /**
     * @var string
     */
    protected $state = PaymentInterface::STATE_NEW;

    /**
     * @var array|object
     */
    protected $details = [];

    /**
     * @var CurrencyInterface
     */
    protected $currency;

    /**
     * @var \DateTime
     */
    protected $datePayment;

    /**
     * @var int
     */
    protected $orderId;

    public function getId()
    {
        return $this->id;
    }

    public function getPaymentProvider()
    {
        return $this->paymentProvider;
    }

    public function setPaymentProvider(PaymentProviderInterface $paymentProvider)
    {
        $this->paymentProvider = $paymentProvider;
    }

    public function getCurrencyCode()
    {
        return $this->currency->getIsoCode();
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    public function getDatePayment()
    {
        return $this->datePayment;
    }

    public function setDatePayment($datePayment)
    {
        $this->datePayment = $datePayment;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return object|array|null
     */
    public function getDetails()
    {
        return $this->details;
    }

    public function setDetails($details)
    {
        if ($details instanceof \Traversable) {
            $details = iterator_to_array($details);
        }

        if (!is_array($details)) {
            $details = [];
        }

        $this->details = $details;
    }
}
