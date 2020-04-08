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

namespace CoreShop\Bundle\StoreBundle\Context\Debug;

use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Store\Context\StoreNotFoundException;
use CoreShop\Component\Store\Model\StoreInterface;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class DebugStoreContext implements StoreContextInterface
{
    private $debugStoreProvider;
    private $storeRepository;
    private $requestStack;

    public function __construct(
        DebugStoreProviderInterface $debugStoreProvider,
        StoreRepositoryInterface $storeRepository,
        RequestStack $requestStack
    ) {
        $this->debugStoreProvider = $debugStoreProvider;
        $this->storeRepository = $storeRepository;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function getStore(): StoreInterface
    {
        $debugStoreId = $this->debugStoreProvider->getStoreId($this->getMasterRequest());

        if (null === $debugStoreId) {
            throw new StoreNotFoundException();
        }

        /**
         * @var StoreInterface $store
         */
        $store = $this->storeRepository->find($debugStoreId);

        if (null === $store) {
            throw new StoreNotFoundException();
        }

        return $store;
    }

    /**
     * @throws StoreNotFoundException
     */
    private function getMasterRequest(): Request
    {
        $masterRequest = $this->requestStack->getMasterRequest();
        if (null === $masterRequest) {
            throw new StoreNotFoundException();
        }

        return $masterRequest;
    }
}
