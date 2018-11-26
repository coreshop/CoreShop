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

namespace CoreShop\Bundle\OrderBundle\EventListener;

use CoreShop\Component\Order\Context\CartContextInterface;
use CoreShop\Component\Order\Context\CartNotFoundException;
use Pimcore\Http\Request\Resolver\PimcoreContextResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class SessionCartSubscriber implements EventSubscriberInterface
{
    /**
     * @var PimcoreContextResolver
     */
    private $pimcoreContext;

    /**
     * @var CartContextInterface
     */
    private $cartContext;

    /**
     * @var string
     */
    private $sessionKeyName;

    /**
     * @param PimcoreContextResolver $pimcoreContextResolver
     * @param CartContextInterface   $cartContext
     * @param string                 $sessionKeyName
     */
    public function __construct(PimcoreContextResolver $pimcoreContextResolver, CartContextInterface $cartContext, $sessionKeyName)
    {
        $this->pimcoreContext = $pimcoreContextResolver;
        $this->cartContext = $cartContext;
        $this->sessionKeyName = $sessionKeyName;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse'],
        ];
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if ($this->pimcoreContext->matchesPimcoreContext($event->getRequest(), PimcoreContextResolver::CONTEXT_ADMIN)) {
            return;
        }

        if (!$event->isMasterRequest()) {
            return;
        }

        /** @var Request $request */
        $request = $event->getRequest();

        try {
            $cart = $this->cartContext->getCart();
        } catch (CartNotFoundException $exception) {
            return;
        }

        if (null !== $cart && 0 !== $cart->getId() && null !== $cart->getStore()) {
            $session = $request->getSession();

            if ($session instanceof SessionInterface) {
                $session->set(
                    sprintf('%s.%s', $this->sessionKeyName, $cart->getStore()->getId()),
                    $cart->getId()
                );
            }
        }
    }
}
