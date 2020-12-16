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

namespace CoreShop\Bundle\CoreBundle\Event;

use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use Symfony\Contracts\EventDispatcher\Event;

class PaymentProviderSupportsEvent extends Event
{
    private $paymentProvider;
    private $subject;
    private $supported = true;

    public function __construct(PaymentProviderInterface $paymentProvider, ResourceInterface $subject = null)
    {
        $this->paymentProvider = $paymentProvider;
        $this->subject = $subject;
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
