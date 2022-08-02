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

namespace CoreShop\Component\Store\Context\RequestBased;

use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Store\Context\StoreNotFoundException;
use CoreShop\Component\Store\Model\StoreInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class StoreContext implements StoreContextInterface
{
    public function __construct(private RequestResolverInterface $requestResolver, private RequestStack $requestStack)
    {
    }

    public function getStore(): StoreInterface
    {
        try {
            return $this->getStoreForRequest($this->getMainRequest());
        } catch (\UnexpectedValueException $exception) {
            throw new StoreNotFoundException($exception);
        }
    }

    private function getStoreForRequest(Request $request): StoreInterface
    {
        $store = $this->requestResolver->findStore($request);

        $this->assertStoreWasFound($store);

        return $store;
    }

    private function getMainRequest(): Request
    {
        $masterRequest = $this->requestStack->getMainRequest();
        if (null === $masterRequest) {
            throw new \UnexpectedValueException('There are not any requests on request stack');
        }

        return $masterRequest;
    }

    private function assertStoreWasFound(StoreInterface $store = null): void
    {
        if (null === $store) {
            throw new \UnexpectedValueException('Store was not found for given request');
        }
    }
}
