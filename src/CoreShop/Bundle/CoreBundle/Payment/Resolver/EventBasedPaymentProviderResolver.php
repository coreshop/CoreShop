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

namespace CoreShop\Bundle\CoreBundle\Payment\Resolver;

use CoreShop\Bundle\CoreBundle\Event\PaymentProviderSupportsEvent;
use CoreShop\Component\Core\Events;
use CoreShop\Component\Payment\Resolver\PaymentProviderResolverInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class EventBasedPaymentProviderResolver implements PaymentProviderResolverInterface
{
    private PaymentProviderResolverInterface $inner;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        PaymentProviderResolverInterface $inner,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $this->inner = $inner;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function resolvePaymentProviders(ResourceInterface $subject = null): array
    {
        $allowedPaymentProviders = [];

        foreach ($this->inner->resolvePaymentProviders($subject) as $paymentProvider) {
            $event = new PaymentProviderSupportsEvent($paymentProvider, $subject);

            $this->eventDispatcher->dispatch($event, Events::SUPPORTS_PAYMENT_PROVIDER);

            if ($event->isSupported()) {
                $allowedPaymentProviders[] = $paymentProvider;
            }
        }

        return $allowedPaymentProviders;
    }
}
