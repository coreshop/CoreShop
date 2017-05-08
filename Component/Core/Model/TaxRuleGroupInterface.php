<?php

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Store\Model\StoreInterface;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface as BaseTaxRuleGroupInterface;
use Doctrine\Common\Collections\Collection;

interface TaxRuleGroupInterface extends BaseTaxRuleGroupInterface
{
    /**
     * @return Collection|StoreInterface[]
     */
    public function getStores();

    /**
     * @return bool
     */
    public function hasStores();

    /**
     * @param StoreInterface $store
     */
    public function addStore(StoreInterface $store);

    /**
     * @param StoreInterface $store
     */
    public function removeStore(StoreInterface $store);

    /**
     * @param StoreInterface $store
     *
     * @return bool
     */
    public function hasStore(StoreInterface $store);
}
