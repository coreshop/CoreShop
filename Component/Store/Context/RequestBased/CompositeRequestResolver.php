<?php

namespace CoreShop\Component\Store\Context\RequestBased;

use Symfony\Component\HttpFoundation\Request;
use Zend\Stdlib\PriorityQueue;

final class CompositeRequestResolver implements RequestResolverInterface
{
    /**
     * @var PriorityQueue|RequestResolverInterface[]
     */
    private $requestResolvers;

    public function __construct()
    {
        $this->requestResolvers = new PriorityQueue();
    }

    /**
     * @param RequestResolverInterface $requestResolver
     * @param int $priority
     */
    public function addResolver(RequestResolverInterface $requestResolver, $priority = 0)
    {
        $this->requestResolvers->insert($requestResolver, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function findStore(Request $request)
    {
        foreach ($this->requestResolvers as $requestResolver) {
            $channel = $requestResolver->findStore($request);

            if (null !== $channel) {
                return $channel;
            }
        }

        return null;
    }
}
