<?php

namespace CoreShop\Component\Core\Context;

use CoreShop\Component\Store\Context\StoreContextInterface;

class ShopperContext implements ShopperContextInterface
{
    /**
     * @var StoreContextInterface
     */
    private $storeContext;

    /**
     * @param StoreContextInterface $storeContext
     */
    public function __construct(
        StoreContextInterface $storeContext
    ) {
        $this->storeContext = $storeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getStore()
    {
        return $this->storeContext->getStore();
    }
}
