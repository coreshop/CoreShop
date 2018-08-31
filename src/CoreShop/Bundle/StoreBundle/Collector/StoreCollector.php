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

namespace CoreShop\Bundle\StoreBundle\Collector;

use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Store\Context\StoreNotFoundException;
use CoreShop\Component\Store\Model\StoreInterface;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

final class StoreCollector extends DataCollector
{
    /**
     * @var StoreContextInterface
     */
    private $storeContext;

    /**
     * @param StoreRepositoryInterface $storeRepository
     * @param StoreContextInterface $storeContext
     * @param bool $storeChangeSupport
     */
    public function __construct(
        StoreRepositoryInterface $storeRepository,
        StoreContextInterface $storeContext,
        $storeChangeSupport = false
    )
    {
        $this->storeContext = $storeContext;

        $this->data = [
            'store' => null,
            'stores' => $storeRepository->findAll(),
            'store_change_support' => $storeChangeSupport,
        ];
    }

    /**
     * @return StoreInterface
     */
    public function getStore()
    {
        return $this->data['store'];
    }

    /**
     * @return StoreInterface[]
     */
    public function getStores()
    {
        return $this->data['stores'];
    }

    /**
     * @return bool
     */
    public function isStoreChangeSupported()
    {
        return $this->data['store_change_support'];
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        try {
            $this->data['store'] = $this->storeContext->getStore();
        } catch (\Exception $exception) {
            //If some goes wrong, we just ignore it
        }
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->data = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'coreshop.store_collector';
    }
}
