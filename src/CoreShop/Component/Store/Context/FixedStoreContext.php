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

namespace CoreShop\Component\Store\Context;

use CoreShop\Component\Store\Model\StoreInterface;

final class FixedStoreContext implements StoreContextInterface
{
    /**
     * @var StoreInterface
     */
    private $store = null;

    /**
     * @param StoreInterface $store
     */
    public function setStore($store)
    {
        $this->store = $store;
    }

    /**
     * {@inheritdoc}
     */
    public function getStore()
    {
        if ($this->store instanceof StoreInterface) {
            return $this->store;
        }

        throw new StoreNotFoundException();
    }
}
