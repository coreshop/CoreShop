<?php

namespace CoreShop\Component\Store\Model;

trait StoreAwareTrait
{
    /**
     * @var StoreInterface
     */
    protected $store;

    /**
     * {@inheritdoc}
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * {@inheritdoc}
     */
    public function setStore(StoreInterface $store)
    {
        $this->store = $store;
    }
}