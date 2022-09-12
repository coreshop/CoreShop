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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\EventListener;

use CoreShop\Bundle\CoreBundle\Event\RequestNewsletterConfirmationEvent;
use CoreShop\Component\Core\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;

final class CustomerNewsletterConfirmListener
{
    public function __construct(
        private RouterInterface $router,
        private RequestStack $requestStack,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function checkCustomerNewsletterConfirmation(GenericEvent $event): void
    {
        Assert::isInstanceOf($event->getSubject(), CustomerInterface::class);

        /**
         * @var CustomerInterface $user
         */
        $user = $event->getSubject();

        if (null === $user->getUser()) {
            return;
        }

        if (!$this->requestStack->getMainRequest()) {
            return;
        }

        if (!$user->getNewsletterActive() || $user->getNewsletterConfirmed()) {
            return;
        }

        $confirmEvent = new RequestNewsletterConfirmationEvent(
            $user,
            $this->router->generate(
                'coreshop_customer_confirm_newsletter',
                ['_locale' => $this->requestStack->getMainRequest()->getLocale()],
                UrlGeneratorInterface::ABSOLUTE_URL,
            ),
        );
        $this->eventDispatcher->dispatch($confirmEvent, 'coreshop.customer.request_newsletter_confirm');
    }
}
