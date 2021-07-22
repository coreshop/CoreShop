<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Address\Context\CountryContextInterface;
use CoreShop\Component\Address\Context\RequestBased\GeoLiteBasedRequestResolver;
use CoreShop\Component\Address\Formatter\AddressFormatterInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CountryInterface;
use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Core\Repository\CountryRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

final class CountryContext implements Context
{
    private SharedStorageInterface $sharedStorage;
    private CountryRepositoryInterface $countryRepository;
    private CountryContextInterface $countryContext;
    private AddressFormatterInterface $addressFormatter;
    private GeoLiteBasedRequestResolver $geoLiteResolver;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        CountryRepositoryInterface $countryRepository,
        CountryContextInterface $countryContext,
        AddressFormatterInterface $addressFormatter,
        GeoLiteBasedRequestResolver $geoLiteResolver
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->countryRepository = $countryRepository;
        $this->countryContext = $countryContext;
        $this->addressFormatter = $addressFormatter;
        $this->geoLiteResolver = $geoLiteResolver;
    }

    /**
     * @Then /^there should be a country "([^"]+)"$/
     */
    public function thereShouldBeACountryCalled($name): void
    {
        $countries = $this->countryRepository->findByName($name, 'en');

        Assert::eq(
            count($countries),
            1,
            sprintf('%d countries has been found with name "%s".', count($countries), $name)
        );
    }

    /**
     * @Then /^the (country "[^"]+") should use (currency "[^"]+")$/
     */
    public function theCountryShouldUseCurrency(CountryInterface $country, CurrencyInterface $currency): void
    {
        Assert::eq(
            $country->getCurrency()->getId(),
            $currency->getId(),
            sprintf('%s country should use currency %s but uses %s instead.', $country->getName(),
                $currency->getIsoCode(), $country->getCurrency()->getIsoCode())
        );
    }

    /**
     * @Then /^I (?:|still )should be in (country "[^"]+")$/
     */
    public function iShouldBeInCountry(CountryInterface $country): void
    {
        $actualCountry = $this->countryContext->getCountry();

        Assert::eq(
            $country->getId(),
            $actualCountry->getId(),
            sprintf(
                'I should be in country %s (%d), but I am in country %s (%d) instead.',
                $country->getName(),
                $country->getId(),
                $actualCountry->getName(),
                $actualCountry->getId()
            )
        );
    }

    /**
     * @Then /^the (address) should format to "([^"]+)"$/
     */
    public function theAddressShouldFormatTo(AddressInterface $address, $formattedAddress): void
    {
        $actualFormattedAddress = $this->addressFormatter->formatAddress($address);

        Assert::eq(
            $actualFormattedAddress,
            $formattedAddress,
            sprintf(
                'expected the address to be formatted like "%s" but got "%s" instead.',
                $formattedAddress,
                $actualFormattedAddress
            )
        );
    }

    /**
     * @Then /^when I check the geo-lite resolver with IP-Address "([^"]+)" we should be in country "([^"]+)"$/
     * @Then /^when I check the geo-lite resolver again with IP-Address "([^"]+)" we should be in country "([^"]+)"$/
     */
    public function whenIcheckTheGeoLiteResolver($ipAddress, $countryIso): void
    {
        $request = Request::create(
            'localhost',
            'GET',
            [],
            [],
            [],
            [
                'REMOTE_ADDR' => $ipAddress
            ]
        );

        $country = $this->geoLiteResolver->findCountry($request);

        Assert::eq($country ? $country->getIsoCode() : null, $countryIso);
    }
}
