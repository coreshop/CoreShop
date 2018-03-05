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
use CoreShop\Component\Order\Context\CartContextInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use Pimcore\Http\RequestHelper;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

final class RequestCartRecalculation
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
     * @param CartManagerInterface $cartManager
     * @param CartContextInterface $cartContext
     * @param ConfigurationServiceInterface $configurationService
     * @param RequestHelper $pimcoreRequestHelper
     */
    public function __construct(
        CartManagerInterface $cartManager,
        CartContextInterface $cartContext,
        ConfigurationServiceInterface $configurationService,
        RequestHelper $pimcoreRequestHelper
    )
    {
        $this->cartManager = $cartManager;
        $this->cartContext = $cartContext;
        $this->configurationService = $configurationService;
        $this->pimcoreRequestHelper = $pimcoreRequestHelper;
    }

    /**
     * Force Cart to be recalculated
     *
     * @param GetResponseEvent $event
     */
    public function checkPriceRuleState(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        if (!$this->pimcoreRequestHelper->isFrontendRequest($event->getRequest())) {
            return;
        }

        $cart = $this->cartContext->getCart();

        if ($cart->getId()) {
            if ($this->configurationService->get('SYSTEM.PRICE_RULE.UPDATE') > $cart->getModificationDate()) {
                $this->cartManager->persistCart($cart);
            }
        }
    }
}