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

use CoreShop\Component\Core\Configuration\ConfigurationServiceInterface;
use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Order\Context\CartContextInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use Pimcore\Http\RequestHelper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

final class RequestCartAvailability
{
    /**
     * @var CartManagerInterface
     */
    private $cartManager;

    /**
     * @var CartContextInterface
     */
    private $cartContext;

    /**
     * @var ConfigurationServiceInterface
     */
    private $configurationService;

    /**
     * @var RequestHelper
     */
    private $pimcoreRequestHelper;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @param CartManagerInterface          $cartManager
     * @param CartContextInterface          $cartContext
     * @param ConfigurationServiceInterface $configurationService
     * @param RequestHelper                 $pimcoreRequestHelper
     * @param SessionInterface              $session
     */
    public function __construct(
        CartManagerInterface $cartManager,
        CartContextInterface $cartContext,
        ConfigurationServiceInterface $configurationService,
        RequestHelper $pimcoreRequestHelper,
        SessionInterface $session
    ) {
        $this->cartManager = $cartManager;
        $this->cartContext = $cartContext;
        $this->configurationService = $configurationService;
        $this->pimcoreRequestHelper = $pimcoreRequestHelper;
        $this->session = $session;
    }

    /**
     * Check if Cart needs a recalculation because of changed items from system
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

        /** @var CartInterface $cart */
        $cart = $this->cartContext->getCart();

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