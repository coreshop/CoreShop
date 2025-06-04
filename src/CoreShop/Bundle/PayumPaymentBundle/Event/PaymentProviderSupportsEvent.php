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

namespace CoreShop\Bundle\PayumPaymentBundle\Event;

use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use Symfony\Contracts\EventDispatcher\Event;

class PaymentProviderSupportsEvent extends Event
{
    private bool $supported = true;

    public function __construct(
        private PaymentProviderInterface $paymentProvider,
        private ?\CoreShop\Component\Resource\Model\ResourceInterface $subject = null,
    ) {
    }

    public function getPaymentProvider(): PaymentProviderInterface
    {
        return $this->paymentProvider;
    }

    public function getSubject(): ?ResourceInterface
    {
        return $this->subject;
    }

    public function isSupported(): bool
    {
        return $this->supported;
    }

    public function setSupported(bool $supported): void
    {
        $this->supported = $supported;
    }
}
