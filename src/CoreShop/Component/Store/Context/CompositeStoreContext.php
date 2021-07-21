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

namespace CoreShop\Component\Store\Context;

use CoreShop\Component\Store\Model\StoreInterface;
use Laminas\Stdlib\PriorityQueue;

final class CompositeStoreContext implements StoreContextInterface
{
    private PriorityQueue $storeContexts;

    public function __construct()
    {
        $this->storeContexts = new PriorityQueue();
    }

    public function addContext(StoreContextInterface $storeContext, int $priority = 0): void
    {
        $this->storeContexts->insert($storeContext, $priority);
    }

    public function getStore(): StoreInterface
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
