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

namespace CoreShop\Bundle\CoreBundle\EventListener;

use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use Pimcore\Http\RequestHelper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final class RequestCartAvailability
{
    public function __construct(private CartManagerInterface $cartManager, private ShopperContextInterface $shopperContext, private RequestHelper $pimcoreRequestHelper)
    {
    }

    public function checkCartAvailability(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
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

        $session = $event->getRequest()->getSession();

        if (!$session instanceof Session) {
            return;
        }

        $session->getFlashBag()->add('coreshop_global_error', 'coreshop.ui.global_error.cart_has_changed');
        $cart->setNeedsRecalculation(false);
        $this->cartManager->persistCart($cart);

        // redirect to same page, otherwise flashbag will show up twice. better solution?
        $event->setResponse(new RedirectResponse($event->getRequest()->getRequestUri()));
    }
}
