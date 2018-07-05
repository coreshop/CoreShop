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

namespace CoreShop\Component\Store\Context;

use Zend\Stdlib\PriorityQueue;

final class CompositeStoreContext implements StoreContextInterface
{
    /**
     * @var PriorityQueue|StoreContextInterface[]
     */
    private $storeContexts;

    public function __construct()
    {
        $this->storeContexts = new PriorityQueue();
    }

    /**
     * @param StoreContextInterface $storeContext
     * @param int                   $priority
     */
    public function addContext(StoreContextInterface $storeContext, $priority = 0)
    {
        $this->storeContexts->insert($storeContext, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function getStore()
    {
        foreach ($this->storeContexts as $storeContext) {
            try {
                return $storeContext->getStore();
            } catch (StoreNotFoundException $exception) {
                continue;
            }
        }

        throw new StoreNotFoundException();
    }
}
