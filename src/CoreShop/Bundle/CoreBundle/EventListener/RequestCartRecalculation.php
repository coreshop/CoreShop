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

use CoreShop\Component\Core\Configuration\ConfigurationServiceInterface;
use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use Pimcore\Http\RequestHelper;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final class RequestCartRecalculation
{
    public function __construct(
        private CartManagerInterface $cartManager,
        private ShopperContextInterface $shopperContext,
        private ConfigurationServiceInterface $configurationService,
        private RequestHelper $pimcoreRequestHelper,
    ) {
    }

    public function checkPriceRuleState(RequestEvent $event): void
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
