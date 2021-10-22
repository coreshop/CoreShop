<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\PayumBundle\Request;

use CoreShop\Component\Core\Model\PaymentInterface;
use Payum\Core\Request\BaseGetStatus;

class GetStatus extends BaseGetStatus
{
    /**
     * @var string
     * @psalm-suppress NonInvariantDocblockPropertyType
     */
    protected $status;

    public function markNew(): void
    {
        $this->status = PaymentInterface::STATE_NEW;
    }

    public function isNew(): bool
    {
        return PaymentInterface::STATE_NEW === $this->status;
    }

    public function markSuspended(): void
    {
        $this->status = PaymentInterface::STATE_PROCESSING;
    }

    public function isSuspended(): bool
    {
        return PaymentInterface::STATE_PROCESSING === $this->status;
    }

    public function markExpired(): void
    {
        $this->status = PaymentInterface::STATE_FAILED;
    }

    public function isExpired(): bool
    {
        return PaymentInterface::STATE_FAILED === $this->status;
    }

    public function markCanceled(): void
    {
        $this->status = PaymentInterface::STATE_CANCELLED;
    }

    public function isCanceled(): bool
    {
        return PaymentInterface::STATE_CANCELLED === $this->status;
    }

    public function markPending(): void
    {
        $this->status = PaymentInterface::STATE_PROCESSING;
    }

    public function isPending(): bool
    {
        return PaymentInterface::STATE_PROCESSING === $this->status;
    }

    public function markFailed(): void
    {
        $this->status = PaymentInterface::STATE_FAILED;
    }

    public function isFailed(): bool
    {
        return PaymentInterface::STATE_FAILED === $this->status;
    }

    public function markUnknown(): void
    {
        $this->status = PaymentInterface::STATE_UNKNOWN;
    }

    public function isUnknown(): bool
    {
        return PaymentInterface::STATE_UNKNOWN === $this->status;
    }

    public function markCaptured(): void
    {
        $this->status = PaymentInterface::STATE_COMPLETED;
    }

    public function isCaptured(): bool
    {
        return PaymentInterface::STATE_COMPLETED === $this->status;
    }

    public function isAuthorized(): bool
    {
        return PaymentInterface::STATE_AUTHORIZED === $this->status;
    }

    public function markAuthorized(): void
    {
        $this->status = PaymentInterface::STATE_AUTHORIZED;
    }

    public function isRefunded(): bool
    {
        return PaymentInterface::STATE_REFUNDED === $this->status;
    }

    public function markRefunded(): void
    {
        $this->status = PaymentInterface::STATE_REFUNDED;
    }

    public function isPayedout(): bool
    {
        return PaymentInterface::STATE_REFUNDED === $this->status;
    }

    public function markPayedout(): void
    {
        $this->status = PaymentInterface::STATE_REFUNDED;
    }
}
