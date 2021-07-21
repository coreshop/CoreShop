<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
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
     */
    protected $status;

    public function markNew()
    {
        $this->status = PaymentInterface::STATE_NEW;
    }

    public function isNew()
    {
        return $this->status === PaymentInterface::STATE_NEW;
    }

    public function markSuspended()
    {
        $this->status = PaymentInterface::STATE_PROCESSING;
    }

    public function isSuspended()
    {
        return $this->status === PaymentInterface::STATE_PROCESSING;
    }

    public function markExpired()
    {
        $this->status = PaymentInterface::STATE_FAILED;
    }

    public function isExpired()
    {
        return $this->status === PaymentInterface::STATE_FAILED;
    }

    public function markCanceled()
    {
        $this->status = PaymentInterface::STATE_CANCELLED;
    }

    public function isCanceled()
    {
        return $this->status === PaymentInterface::STATE_CANCELLED;
    }

    public function markPending()
    {
        $this->status = PaymentInterface::STATE_PROCESSING;
    }

    public function isPending()
    {
        return $this->status === PaymentInterface::STATE_PROCESSING;
    }

    public function markFailed()
    {
        $this->status = PaymentInterface::STATE_FAILED;
    }

    public function isFailed()
    {
        return $this->status === PaymentInterface::STATE_FAILED;
    }

    public function markUnknown()
    {
        $this->status = PaymentInterface::STATE_UNKNOWN;
    }

    public function isUnknown()
    {
        return $this->status === PaymentInterface::STATE_UNKNOWN;
    }

    public function markCaptured()
    {
        $this->status = PaymentInterface::STATE_COMPLETED;
    }

    public function isCaptured()
    {
        return $this->status === PaymentInterface::STATE_COMPLETED;
    }

    public function isAuthorized()
    {
        return $this->status === PaymentInterface::STATE_AUTHORIZED;
    }

    public function markAuthorized()
    {
        $this->status = PaymentInterface::STATE_AUTHORIZED;
    }

    public function isRefunded()
    {
        return $this->status === PaymentInterface::STATE_REFUNDED;
    }

    public function markRefunded()
    {
        $this->status = PaymentInterface::STATE_REFUNDED;
    }

    public function isPayedout()
    {
        return $this->status === PaymentInterface::STATE_REFUNDED;
    }

    public function markPayedout()
    {
        $this->status = PaymentInterface::STATE_REFUNDED;
    }
}
