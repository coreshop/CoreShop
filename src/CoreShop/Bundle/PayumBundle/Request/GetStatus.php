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

namespace CoreShop\Bundle\PayumBundle\Request;

use CoreShop\Component\Payment\Model\PaymentInterface;
use Payum\Core\Request\BaseGetStatus;

class GetStatus extends BaseGetStatus
{
    /**
     * {@inheritdoc}
     */
    public function markNew()
    {
        $this->status = PaymentInterface::STATE_NEW;
    }

    /**
     * {@inheritdoc}
     */
    public function isNew()
    {
        return PaymentInterface::STATE_NEW === $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function markSuspended()
    {
        $this->status = PaymentInterface::STATE_PROCESSING;
    }

    /**
     * {@inheritdoc}
     */
    public function isSuspended()
    {
        return PaymentInterface::STATE_PROCESSING === $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function markExpired()
    {
        $this->status = PaymentInterface::STATE_FAILED;
    }

    /**
     * {@inheritdoc}
     */
    public function isExpired()
    {
        return PaymentInterface::STATE_FAILED === $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function markCanceled()
    {
        $this->status = PaymentInterface::STATE_CANCELLED;
    }

    /**
     * {@inheritdoc}
     */
    public function isCanceled()
    {
        return PaymentInterface::STATE_CANCELLED === $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function markPending()
    {
        $this->status = PaymentInterface::STATE_PROCESSING;
    }

    /**
     * {@inheritdoc}
     */
    public function isPending()
    {
        return PaymentInterface::STATE_PROCESSING === $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function markFailed()
    {
        $this->status = PaymentInterface::STATE_FAILED;
    }

    /**
     * {@inheritdoc}
     */
    public function isFailed()
    {
        return PaymentInterface::STATE_FAILED === $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function markUnknown()
    {
        $this->status = PaymentInterface::STATE_UNKNOWN;
    }

    /**
     * {@inheritdoc}
     */
    public function isUnknown()
    {
        return PaymentInterface::STATE_UNKNOWN === $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function markCaptured()
    {
        $this->status = PaymentInterface::STATE_COMPLETED;
    }

    /**
     * {@inheritdoc}
     */
    public function isCaptured()
    {
        return PaymentInterface::STATE_COMPLETED === $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthorized()
    {
        return PaymentInterface::STATE_PROCESSING === $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function markAuthorized()
    {
        $this->status = PaymentInterface::STATE_PROCESSING;
    }

    /**
     * {@inheritdoc}
     */
    public function isRefunded()
    {
        return PaymentInterface::STATE_REFUNDED === $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function markRefunded()
    {
        $this->status = PaymentInterface::STATE_REFUNDED;
    }

    /**
     * {@inheritdoc}
     */
    public function isPayedout()
    {
        return PaymentInterface::STATE_REFUNDED === $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function markPayedout()
    {
        $this->status = PaymentInterface::STATE_REFUNDED;
    }
}
