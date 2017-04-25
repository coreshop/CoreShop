<?php

namespace CoreShop\Component\Address\Context;

use Zend\Stdlib\PriorityQueue;

final class CompositeCountryContext implements CountryContextInterface
{
    /**
     * @var PriorityQueue|CountryContextInterface[]
     */
    private $countryContexts;

    public function __construct()
    {
        $this->countryContexts = new PriorityQueue();
    }

    /**
     * @param CountryContextInterface $countryContexts
     * @param int $priority
     */
    public function addContext(CountryContextInterface $countryContexts, $priority = 0)
    {
        $this->countryContexts->insert($countryContexts, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function getCountry()
    {
        foreach ($this->countryContexts as $countryContexts) {
            try {
                return $countryContexts->getCountry();
            } catch (CountryNotFoundException $exception) {
                continue;
            }
        }

        throw new CountryNotFoundException();
    }
}
