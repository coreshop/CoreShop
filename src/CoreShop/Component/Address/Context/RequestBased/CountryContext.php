<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

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

    public function __construct(RequestResolverInterface $requestResolver, RequestStack $requestStack)
    {
        $this->requestResolver = $requestResolver;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountry(): CountryInterface
    {
        try {
            return $this->getCountryForRequest($this->getMasterRequest());
        } catch (\UnexpectedValueException $exception) {
            throw new CountryNotFoundException($exception);
        }
    }

    /**
     * @param Request $request
     *
     * @return CountryInterface
     */
    private function getCountryForRequest(Request $request): CountryInterface
    {
        $country = $this->requestResolver->findCountry($request);

        $this->assertCountryWasFound($country);

        return $country;
    }

    /**
     * @return Request
     */
    private function getMasterRequest(): Request
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
    private function assertCountryWasFound(CountryInterface $country = null): void
    {
        if (null === $country) {
            throw new \UnexpectedValueException('Country was not found for given request');
        }
    }
}
