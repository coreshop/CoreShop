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

namespace CoreShop\Bundle\CoreBundle\EventListener;

use CoreShop\Bundle\CoreBundle\Event\RequestNewsletterConfirmationEvent;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Pimcore\Routing\LinkGeneratorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

final class CustomerNewsletterConfirmListener
{
    /**
     * @var LinkGeneratorInterface
     */
    private $linkGenerator;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param LinkGeneratorInterface $linkGenerator
     * @param RequestStack $requestStack
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        LinkGeneratorInterface $linkGenerator,
        RequestStack $requestStack,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $this->linkGenerator = $linkGenerator;
        $this->requestStack = $requestStack;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param GenericEvent $event
     */
    public function checkCustomerNewsletterConfirmation(GenericEvent $event)
    {
        Assert::isInstanceOf($event->getSubject(), CustomerInterface::class);

        /**
         * @var $user CustomerInterface
         */
        $user = $event->getSubject();

        if ($user->getIsGuest() === true) {
            return;
        }

        if (!$this->requestStack->getMasterRequest()) {
            return;
        }

        if (!$user->getNewsletterActive() || $user->getNewsletterConfirmed()) {
            return;
        }

        $confirmEvent = new RequestNewsletterConfirmationEvent(
            $user,
            $this->linkGenerator->generate($event->getSubject(), 'coreshop_customer_confirm_newsletter', ['_locale' => $this->requestStack->getMasterRequest()->getLocale()], UrlGeneratorInterface::ABSOLUTE_URL)
        );
        $this->eventDispatcher->dispatch('coreshop.customer.request_newsletter_confirm', $confirmEvent);
    }
}