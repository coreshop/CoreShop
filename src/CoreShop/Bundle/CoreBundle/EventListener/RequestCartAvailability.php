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

namespace CoreShop\Bundle\CoreBundle\EventListener;

use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use Pimcore\Http\RequestHelper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

final class RequestCartAvailability
{
    private $cartManager;
    private $shopperContext;
    private $pimcoreRequestHelper;
    private $session;

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

    public function checkCartAvailability(GetResponseEvent $event): void
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

        $cart = $this->shopperContext->getCart();

        if (!$cart instanceof OrderInterface) {
            return;
        }

        if (!$cart->getId()) {
            return;
        }

        if (!$cart->getNeedsRecalculation()) {
            return;
        }

        $this->session->getFlashBag()->add('coreshop_global_error', 'coreshop.global_error.cart_has_changed');
        $cart->setNeedsRecalculation(false);
        $this->cartManager->persistCart($cart);

        // redirect to same page, otherwise flashbag will show up twice. better solution?
        $event->setResponse(new RedirectResponse($event->getRequest()->getRequestUri()));
    }
}
