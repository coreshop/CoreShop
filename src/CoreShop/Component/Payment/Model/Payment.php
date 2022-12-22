<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Payment\Model;

use CoreShop\Component\Resource\Model\SetValuesTrait;
use CoreShop\Component\Resource\Model\TimestampableTrait;

/**
 * @psalm-suppress MissingConstructor
 */
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
    protected $state = PaymentInterface::STATE_NEW;

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
}
