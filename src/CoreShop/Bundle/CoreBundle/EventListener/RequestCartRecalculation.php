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

use CoreShop\Component\Core\Configuration\ConfigurationServiceInterface;
use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use Pimcore\Http\RequestHelper;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final class RequestCartRecalculation
{
    private CartManagerInterface $cartManager;
    private ShopperContextInterface $shopperContext;
    private ConfigurationServiceInterface $configurationService;
    private RequestHelper $pimcoreRequestHelper;

    public function __construct(
        CartManagerInterface $cartManager,
        ShopperContextInterface $shopperContext,
        ConfigurationServiceInterface $configurationService,
        RequestHelper $pimcoreRequestHelper
    ) {
        $this->cartManager = $cartManager;
        $this->shopperContext = $shopperContext;
        $this->configurationService = $configurationService;
        $this->pimcoreRequestHelper = $pimcoreRequestHelper;
    }

    public function checkPriceRuleState(RequestEvent $event): void
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

        if ($cart->getId()) {
            /**
             * @var int|null $updateTime
             */
            $updateTime = $this->configurationService->get('SYSTEM.PRICE_RULE.UPDATE');

            if (null !== $updateTime && $updateTime > $cart->getModificationDate()) {
                $this->cartManager->persistCart($cart);
            }
        }
    }
}
