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

namespace CoreShop\Bundle\PayumPaymentBundle\Resolver;

use CoreShop\Bundle\PayumPaymentBundle\Event\PaymentProviderSupportsEvent;
use CoreShop\Bundle\PayumPaymentBundle\Events;
use CoreShop\Component\Payment\Resolver\PaymentProviderResolverInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventBasedPaymentProviderResolver implements PaymentProviderResolverInterface
{
    public function __construct(private PaymentProviderResolverInterface $inner, private EventDispatcherInterface $eventDispatcher)
    {
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
