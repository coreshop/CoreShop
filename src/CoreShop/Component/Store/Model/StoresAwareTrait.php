<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Store\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait StoresAwareTrait
{
    /**
     * @var Collection|ArrayCollection<int, StoreInterface>
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

    public function hasStores(): bool
    {
        return !$this->stores->isEmpty();
    }

    /**
     * @return void
     */
    public function addStore(StoreInterface $store)
    {
        if (!$this->hasStore($store)) {
            $this->stores->add($store);
        }
    }

    /**
     * @return void
     */
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
