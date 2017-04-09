<?php

namespace CoreShop\Component\Store\Context;

use Zend\Stdlib\PriorityQueue;

final class CompositeStoreContext implements StoreContextInterface
{
    /**
     * @var PriorityQueue|StoreContextInterface[]
     */
    private $channelContexts;

    public function __construct()
    {
        $this->channelContexts = new PriorityQueue();
    }

    /**
     * @param StoreContextInterface $channelContext
     * @param int $priority
     */
    public function addContext(StoreContextInterface $channelContext, $priority = 0)
    {
        $this->channelContexts->insert($channelContext, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function getStore()
    {
        foreach ($this->channelContexts as $channelContext) {
            try {
                return $channelContext->getStore();
            } catch (StoreNotFoundException $exception) {
                continue;
            }
        }

        throw new StoreNotFoundException();
    }
}
