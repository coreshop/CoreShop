<?php

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Shipping\Model\Carrier as BaseCarrier;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Carrier extends BaseCarrier implements CarrierInterface
{
    /**
     * @var Collection|StoreInterface[]
     */
    private $stores;

    /**
     * @var TaxRuleGroupInterface
     */
    private $taxRule;

    public function __construct()
    {
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

    /**
     * {@inheritdoc}
     */
    public function getTaxRule()
    {
        return $this->getTaxRule();
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxRule(TaxRuleGroupInterface $taxRule)
    {
        $this->taxRule = $taxRule;
    }
}