<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\Fixtures\Data\Application;

use CoreShop\Component\Core\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Rinvex\Country\Country;
use Rinvex\Country\CountryLoader;
use Symfony\Component\Intl\Currencies;

class CurrencyFixture extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        private CurrencyRepositoryInterface $currencyRepository,
        private FactoryInterface $currencyFactory,
    ) {
    }

    public static function getGroups(): array
    {
        return ['application'];
    }

    public function load(ObjectManager $manager): void
    {
        $countries = CountryLoader::countries(true, true);
        $currencies = [];

        foreach ($countries as $country) {
            if ($country instanceof Country) {
                $currency = $country->getCurrency();

                if (null !== $currency) {
                    $isoCode = $currency['iso_4217_code'];

                    if ($isoCode) {
                        if (!array_key_exists($isoCode, $currencies)) {
                            $currencies[$isoCode] = $currency;
                        }
                    }
                }
            }
        }

        foreach ($currencies as $iso => $c) {
            $currency = $this->currencyRepository->getByCode($iso);
            if (null === $currency) {
                $currency = $this->currencyFactory->createNew();
            }
            $currency->setName($c['iso_4217_name']);
            $currency->setIsoCode($iso);
            $currency->setNumericIsoCode($c['iso_4217_numeric']);
            $currency->setSymbol(Currencies::getSymbol($iso));

            $this->setReference('currency_' . $iso, $currency);

            $manager->persist($currency);
        }

        $manager->flush();
    }
}
