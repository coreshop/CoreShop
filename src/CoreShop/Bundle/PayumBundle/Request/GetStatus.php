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

namespace CoreShop\Bundle\PayumBundle\Request;

use CoreShop\Component\Core\Model\PaymentInterface;
use Payum\Core\Request\BaseGetStatus;

class GetStatus extends BaseGetStatus
{
    /**
     * @var string
     *
     * @psalm-suppress NonInvariantDocblockPropertyType
     */
    protected $status;

    public function markNew(): void
    {
        $this->status = PaymentInterface::STATE_NEW;
    }

    public function isNew(): bool
    {
        return $this->status === PaymentInterface::STATE_NEW;
    }

    public function markSuspended(): void
    {
        $this->status = PaymentInterface::STATE_PROCESSING;
    }

    public function isSuspended(): bool
    {
        return $this->status === PaymentInterface::STATE_PROCESSING;
    }

    public function markExpired(): void
    {
        $this->status = PaymentInterface::STATE_FAILED;
    }

    public function isExpired(): bool
    {
        return $this->status === PaymentInterface::STATE_FAILED;
    }

    public function markCanceled(): void
    {
        $this->status = PaymentInterface::STATE_CANCELLED;
    }

    public function isCanceled(): bool
    {
        return $this->status === PaymentInterface::STATE_CANCELLED;
    }

    public function markPending(): void
    {
        $this->status = PaymentInterface::STATE_PROCESSING;
    }

    public function isPending(): bool
    {
        return $this->status === PaymentInterface::STATE_PROCESSING;
    }

    public function markFailed(): void
    {
        $this->status = PaymentInterface::STATE_FAILED;
    }

    public function isFailed(): bool
    {
        return $this->status === PaymentInterface::STATE_FAILED;
    }

    public function markUnknown(): void
    {
        $this->status = PaymentInterface::STATE_UNKNOWN;
    }

    public function isUnknown(): bool
    {
        return $this->status === PaymentInterface::STATE_UNKNOWN;
    }

    public function markCaptured(): void
    {
        $this->status = PaymentInterface::STATE_COMPLETED;
    }

    public function isCaptured(): bool
    {
        return $this->status === PaymentInterface::STATE_COMPLETED;
    }

    public function isAuthorized(): bool
    {
        return $this->status === PaymentInterface::STATE_AUTHORIZED;
    }

    public function markAuthorized(): void
    {
        $this->status = PaymentInterface::STATE_AUTHORIZED;
    }

    public function isRefunded(): bool
    {
        return $this->status === PaymentInterface::STATE_REFUNDED;
    }

    public function markRefunded(): void
    {
        $this->status = PaymentInterface::STATE_REFUNDED;
    }

    public function isPayedout(): bool
    {
        return $this->status === PaymentInterface::STATE_REFUNDED;
    }

    public function markPayedout(): void
    {
        $this->status = PaymentInterface::STATE_REFUNDED;
    }
}
