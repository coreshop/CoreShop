<?php

namespace CoreShop\Component\Store\Model;

use Doctrine\Common\Collections\Collection;

interface StoresAwareInterface
{
    /**
     * @return Collection|StoreInterface[]
     */
    public function getStores();

    /**
     * @param StoreInterface $store
     *
     * @return bool
     */
    public function hasStore(StoreInterface $store);

    /**
     * @param StoreInterface $store
     */
    public function addStore(StoreInterface $store);

    /**
     * @param StoreInterface $store
     */
    public function removeStore(StoreInterface $store);
}
