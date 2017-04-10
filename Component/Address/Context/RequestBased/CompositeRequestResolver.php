<?php

namespace CoreShop\Component\Address\Context\RequestBased;

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
    public function findCountry(Request $request)
    {
        foreach ($this->requestResolvers as $requestResolver) {
            $country = $requestResolver->findCountry($request);

            if (null !== $country) {
                return $country;
            }
        }

        return null;
    }
}
