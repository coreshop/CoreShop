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

namespace CoreShop\Component\Store\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait StoresAwareTrait
{
    /**
     * @var Collection|ArrayCollection<StoreInterface>
     */
    protected $stores;

    public function __construct()
    {
        $this->stores = new ArrayCollection();
    }

    public function getStores()
    {
        return $this->stores;
    }

    public function hasStores()
    {
        return !$this->stores->isEmpty();
    }

    public function addStore(StoreInterface $store)
    {
        if (!$this->hasStore($store)) {
            $this->stores->add($store);
        }
    }

    public function removeStore(StoreInterface $store)
    {
        if ($this->hasStore($store)) {
            $this->stores->removeElement($store);
        }
    }

    public function hasStore(StoreInterface $store)
    {
        return $this->stores->contains($store);
    }
}
