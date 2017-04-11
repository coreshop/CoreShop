<?php

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Shipping\Model\CarrierInterface as BaseCarrierInterface;
use Doctrine\Common\Collections\Collection;

interface CarrierInterface extends BaseCarrierInterface
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

    /**
     * @return TaxRuleInterface
     */
    public function getTaxRule();

    /**
     * @param TaxRuleGroupInterface $taxRule
     *
     * @return mixed
     */
    public function setTaxRule(TaxRuleGroupInterface $taxRule);
}