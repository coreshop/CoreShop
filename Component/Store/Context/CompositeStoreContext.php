<?php

namespace CoreShop\Component\Store\Context;

use Zend\Stdlib\PriorityQueue;

final class CompositeStoreContext implements StoreContextInterface
{
    /**
     * @var PriorityQueue|StoreContextInterface[]
     */
    private $storeContexts;

    public function __construct()
    {
        $this->storeContexts = new PriorityQueue();
    }

    /**
     * @param StoreContextInterface $storeContext
     * @param int $priority
     */
    public function addContext(StoreContextInterface $storeContext, $priority = 0)
    {
        $this->storeContexts->insert($storeContext, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function getStore()
    {
        foreach ($this->storeContexts as $storeContext) {
            try {
                return $storeContext->getStore();
            } catch (StoreNotFoundException $exception) {
                continue;
            }
        }

        throw new StoreNotFoundException();
    }
}
