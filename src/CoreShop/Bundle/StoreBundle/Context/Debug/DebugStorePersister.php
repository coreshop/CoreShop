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

namespace CoreShop\Bundle\StoreBundle\Context\Debug;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class DebugStorePersister
{
    public function __construct(private DebugStoreProviderInterface $debugStoreProvider)
    {
    }

    public function onKernelResponse(ResponseEvent $filterResponseEvent): void
    {
        if (HttpKernelInterface::SUB_REQUEST === $filterResponseEvent->getRequestType()) {
            return;
        }

        $debugStoreCode = $this->debugStoreProvider->getStoreId($filterResponseEvent->getRequest());

        if (null === $debugStoreCode) {
            return;
        }

        $response = $filterResponseEvent->getResponse();
        $response->headers->setCookie(new Cookie('_store_id', $debugStoreCode));
    }
}
