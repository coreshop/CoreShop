<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\StoreBundle\Collector;

use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Store\Model\StoreInterface;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;
use Pimcore\Http\Request\Resolver\PimcoreContextResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

final class StoreCollector extends DataCollector
{
    private StoreContextInterface $storeContext;
    private PimcoreContextResolver $pimcoreContext;

    public function __construct(
        StoreRepositoryInterface $storeRepository,
        StoreContextInterface $storeContext,
        PimcoreContextResolver $pimcoreContext,
        $storeChangeSupport = false
    ) {
        $this->storeContext = $storeContext;
        $this->pimcoreContext = $pimcoreContext;

        $this->data = [
            'store' => null,
            'stores' => $storeRepository->findAll(),
            'store_change_support' => $storeChangeSupport,
        ];
    }

    public function getStore(): ?StoreInterface
    {
        return $this->data['store'];
    }

    /**
     * @return StoreInterface[]
     */
    public function getStores(): array
    {
        return $this->data['stores'];
    }

    /**
     * @return bool
     */
    public function isStoreChangeSupported(): bool
    {
        return $this->data['store_change_support'];
    }

    public function collect(Request $request, Response $response, \Throwable $exception = null): void
    {
        if ($this->pimcoreContext->matchesPimcoreContext($request, PimcoreContextResolver::CONTEXT_ADMIN)) {
            $this->data['admin'] = true;

            return;
        }

        try {
            $this->data['store'] = $this->storeContext->getStore();
        } catch (\Exception $exception) {
            //If some goes wrong, we just ignore it
        }
    }

    public function reset(): void
    {
        $this->data = [];
    }

    public function getName(): string
    {
        return 'coreshop.store_collector';
    }
}
