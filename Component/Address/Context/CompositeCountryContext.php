<?php

namespace CoreShop\Component\Address\Context;

use Zend\Stdlib\PriorityQueue;

final class CompositeCountryContext implements CountryContextInterface
{
    /**
     * @var PriorityQueue|CountryContextInterface[]
     */
    private $channelContexts;

    public function __construct()
    {
        $this->channelContexts = new PriorityQueue();
    }

    /**
     * @param CountryContextInterface $channelContext
     * @param int $priority
     */
    public function addContext(CountryContextInterface $channelContext, $priority = 0)
    {
        $this->channelContexts->insert($channelContext, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function getCountry()
    {
        foreach ($this->channelContexts as $channelContext) {
            try {
                return $channelContext->getCountry();
            } catch (CountryNotFoundException $exception) {
                continue;
            }
        }

        throw new CountryNotFoundException();
    }
}
