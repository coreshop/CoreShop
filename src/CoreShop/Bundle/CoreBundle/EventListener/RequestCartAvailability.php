<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\EventListener;

use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use Pimcore\Http\RequestHelper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

final class RequestCartAvailability
{
    /**
     * @var CartManagerInterface
     */
    private $cartManager;

    /**
     * @var ShopperContextInterface
     */
    private $shopperContext;

    /**
     * @var RequestHelper
     */
    private $pimcoreRequestHelper;

    /**
     * @var Session
     */
    private $session;

    /**
     * @param CartManagerInterface    $cartManager
     * @param ShopperContextInterface $shopperContext
     * @param RequestHelper           $pimcoreRequestHelper
     * @param Session                 $session
     */
    public function __construct(
        CartManagerInterface $cartManager,
        ShopperContextInterface $shopperContext,
        RequestHelper $pimcoreRequestHelper,
        Session $session
    ) {
        $this->cartManager = $cartManager;
        $this->shopperContext = $shopperContext;
        $this->pimcoreRequestHelper = $pimcoreRequestHelper;
        $this->session = $session;
    }

    /**
     * Check if Cart needs a recalculation because of changed items from system.
     *
     * @param GetResponseEvent $event
     */
    public function checkCartAvailability(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        if (!$this->pimcoreRequestHelper->isFrontendRequest($event->getRequest())) {
            return;
        }

        if ($event->getRequest()->attributes->get('_route') === '_wdt') {
            return;
        }

        if (!$this->shopperContext->hasStore()) {
            return;
        }

        /** @var CartInterface $cart */
        $cart = $this->shopperContext->getCart();

        if ($cart->getId()) {
            if ($cart->getNeedsRecalculation() === true) {
                $this->session->getFlashBag()->add('coreshop_global_error', 'coreshop.global_error.cart_has_changed');
                $cart->setNeedsRecalculation(false);
                $this->cartManager->persistCart($cart);
                // redirect to same page, otherwise flashbag will show up twice. better solution?
                $event->setResponse(new RedirectResponse($event->getRequest()->getRequestUri()));
            }
        }
    }
}
