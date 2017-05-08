<?php

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Store\Model\StoreInterface;
use CoreShop\Component\Taxation\Model\TaxRuleGroup as BaseTaxRuleGroup;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class TaxRuleGroup extends BaseTaxRuleGroup implements TaxRuleGroupInterface
{
    /**
     * @var Collection|StoreInterface[]
     */
    protected $stores;

    public function __construct()
    {
        parent::__construct();

        $this->stores = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getStores()
    {
        return $this->stores;
    }

    /**
     * {@inheritdoc}
     */
    public function hasStores()
    {
        return !$this->stores->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function addStore(StoreInterface $store)
    {
        if (!$this->hasStore($store)) {
            $this->stores->add($store);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeStore(StoreInterface $store)
    {
        if ($this->hasStore($store)) {
            $this->stores->removeElement($store);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasStore(StoreInterface $store)
    {
        return $this->stores->contains($store);
    }
}
