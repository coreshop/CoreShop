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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Payment\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\TimestampableInterface;

interface PaymentInterface extends ResourceInterface, TimestampableInterface
{
    public const STATE_NEW = 'new';

    public const STATE_AUTHORIZED = 'authorized';

    public const STATE_PROCESSING = 'processing';

    public const STATE_COMPLETED = 'completed';

    public const STATE_FAILED = 'failed';

    public const STATE_CANCELLED = 'cancelled';

    public const STATE_REFUNDED = 'refunded';

    public const STATE_UNKNOWN = 'unknown';

    public function getId(): ?int;

    /**
     * @return PaymentProviderInterface
     */
    public function getPaymentProvider();

    public function setPaymentProvider(PaymentProviderInterface $paymentProvider);

    /**
     * @return \DateTime
     */
    public function getDatePayment();

    /**
     * @param \DateTime $datePayment
     */
    public function setDatePayment($datePayment);

    /**
     * @return string
     */
    public function getState();

    /**
     * @param string $state
     */
    public function setState($state);

    /**
     * @return int
     */
    public function getTotalAmount();

    /**
     * @param int $amount
     */
    public function setTotalAmount($amount);

    /**
     * @return string
     */
    public function getNumber();

    /**
     * @param string $number
     */
    public function setNumber($number);

    /**
     * @return string
     */
    public function getDescription();

    public function setDescription(string $description);

    public function getDetails(): array;

    public function setDetails(array $details);

    /**
     * @return string
     */
    public function getCurrencyCode();

    /**
     * @param string $currencyCode
     */
    public function setCurrencyCode($currencyCode);
}
