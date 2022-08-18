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

namespace CoreShop\Bundle\StorageListBundle\Core\EventListener;

use CoreShop\Component\Core\Model\WishlistInterface;
use CoreShop\Component\Order\Context\CartNotFoundException;
use CoreShop\Component\StorageList\Context\StorageListContextInterface;
use CoreShop\Component\Store\Model\StoreAwareInterface;
use Pimcore\Http\Request\Resolver\PimcoreContextResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class SessionStoreStorageListSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private PimcoreContextResolver $pimcoreContext,
        private StorageListContextInterface $context,
        private string $sessionKeyName
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse'],
        ];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if ($this->pimcoreContext->matchesPimcoreContext($event->getRequest(), PimcoreContextResolver::CONTEXT_ADMIN)) {
            return;
        }

        if (!$event->isMainRequest()) {
            return;
        }

        if ($event->getRequest()->attributes->get('_route') === '_wdt') {
            return;
        }

        /** @var Request $request */
        $request = $event->getRequest();

        if (!$request->hasSession()) {
            return;
        }

        try {
            $storageList = $this->context->getStorageList();
        } catch (CartNotFoundException) {
            return;
        }
        
        if (!$storageList instanceof StoreAwareInterface) {
            return;
        }

        if (0 !== $storageList->getId() && null !== $storageList->getStore()) {
            $session = $request->getSession();

            $session->set(
                sprintf('%s.%s', $this->sessionKeyName, $storageList->getStore()->getId()),
                $storageList->getId()
            );
        }
    }
}
