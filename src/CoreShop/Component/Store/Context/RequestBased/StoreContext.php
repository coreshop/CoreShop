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

namespace CoreShop\Component\Store\Context\RequestBased;

use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Store\Context\StoreNotFoundException;
use CoreShop\Component\Store\Model\StoreInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class StoreContext implements StoreContextInterface
{
    private $requestResolver;
    private $requestStack;

    public function __construct(RequestResolverInterface $requestResolver, RequestStack $requestStack)
    {
        $this->requestResolver = $requestResolver;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function getStore(): StoreInterface
    {
        try {
            return $this->getStoreForRequest($this->getMasterRequest());
        } catch (\UnexpectedValueException $exception) {
            throw new StoreNotFoundException($exception);
        }
    }

    /**
     * @param Request $request
     *
     * @return StoreInterface
     */
    private function getStoreForRequest(Request $request): StoreInterface
    {
        $store = $this->requestResolver->findStore($request);

        $this->assertStoreWasFound($store);

        return $store;
    }

    /**
     * @return Request
     */
    private function getMasterRequest(): Request
    {
        $masterRequest = $this->requestStack->getMasterRequest();
        if (null === $masterRequest) {
            throw new \UnexpectedValueException('There are not any requests on request stack');
        }

        return $masterRequest;
    }

    /**
     * @param StoreInterface|null $store
     */
    private function assertStoreWasFound(StoreInterface $store = null): void
    {
        if (null === $store) {
            throw new \UnexpectedValueException('Store was not found for given request');
        }
    }
}
