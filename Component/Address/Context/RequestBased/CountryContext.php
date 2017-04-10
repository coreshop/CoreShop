<?php

namespace CoreShop\Component\Address\Context\RequestBased;

use CoreShop\Component\Address\Context\CountryContextInterface;
use CoreShop\Component\Address\Context\CountryNotFoundException;
use CoreShop\Component\Address\Model\CountryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class CountryContext implements CountryContextInterface
{
    /**
     * @var RequestResolverInterface
     */
    private $requestResolver;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RequestResolverInterface $requestResolver
     * @param RequestStack $requestStack
     */
    public function __construct(RequestResolverInterface $requestResolver, RequestStack $requestStack)
    {
        $this->requestResolver = $requestResolver;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountry()
    {
        try {
            return $this->getStoreForRequest($this->getMasterRequest());
        } catch (\UnexpectedValueException $exception) {
            throw new CountryNotFoundException($exception);
        }
    }

    /**
     * @param Request $request
     *
     * @return CountryInterface
     */
    private function getStoreForRequest(Request $request)
    {
        $country = $this->requestResolver->findCountry($request);

        $this->assertCountryWasFound($country);

        return $country;
    }

    /**
     * @return Request
     */
    private function getMasterRequest()
    {
        $masterRequest = $this->requestStack->getMasterRequest();
        if (null === $masterRequest) {
            throw new \UnexpectedValueException('There are not any requests on request stack');
        }

        return $masterRequest;
    }

    /**
     * @param CountryInterface|null $country
     */
    private function assertCountryWasFound(CountryInterface $country = null)
    {
        if (null === $country) {
            throw new \UnexpectedValueException('Country was not found for given request');
        }
    }
}
