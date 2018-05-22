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

namespace CoreShop\Bundle\CoreBundle\Payment\Resolver;

use CoreShop\Bundle\CoreBundle\Event\PaymentProviderSupportsEvent;
use CoreShop\Component\Core\Events;
use CoreShop\Component\Payment\Resolver\PaymentProviderResolverInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventBasedPaymentProviderResolver implements PaymentProviderResolverInterface
{
    /**
     * @var PaymentProviderResolverInterface
     */
    private $inner;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * EventBasedPaymentProviderResolver constructor.
     * @param PaymentProviderResolverInterface $inner
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(PaymentProviderResolverInterface $inner, EventDispatcherInterface $eventDispatcher)
    {
        $this->inner = $inner;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function resolvePaymentProviders(ResourceInterface $subject = null)
    {
        $allowedPaymentProviders = [];

        foreach ($this->inner->resolvePaymentProviders($subject) as $paymentProvider) {
            $event = new PaymentProviderSupportsEvent($paymentProvider, $subject);

            $this->eventDispatcher->dispatch(Events::SUPPORTS_PAYMENT_PROVIDER, $event);

            if ($event->isSupported()) {
                $allowedPaymentProviders[] = $paymentProvider;
            }
        }

        return $allowedPaymentProviders;
    }
}