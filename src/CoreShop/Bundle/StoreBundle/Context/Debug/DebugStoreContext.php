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

use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Store\Context\StoreNotFoundException;
use CoreShop\Component\Store\Model\StoreInterface;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class DebugStoreContext implements StoreContextInterface
{
    public function __construct(private DebugStoreProviderInterface $debugStoreProvider, private StoreRepositoryInterface $storeRepository, private RequestStack $requestStack)
    {
    }

    public function getStore(): StoreInterface
    {
        $debugStoreId = $this->debugStoreProvider->getStoreId($this->getMainRequest());

        if (null === $debugStoreId) {
            throw new StoreNotFoundException();
        }

        /**
         * @var StoreInterface $store
         */
        $store = $this->storeRepository->find($debugStoreId);

        /*
         * @psalm-suppress DocblockTypeContradiction
         */
        if (null === $store) {
            throw new StoreNotFoundException();
        }

        return $store;
    }

    /**
     * @throws StoreNotFoundException
     */
    private function getMainRequest(): Request
    {
        $masterRequest = $this->requestStack->getMainRequest();
        if (null === $masterRequest) {
            throw new StoreNotFoundException();
        }

        return $masterRequest;
    }
}
