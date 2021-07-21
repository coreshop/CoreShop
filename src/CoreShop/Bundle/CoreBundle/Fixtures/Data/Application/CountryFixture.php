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

namespace CoreShop\Bundle\CoreBundle\Fixtures\Data\Application;

use CoreShop\Bundle\FixtureBundle\Fixture\VersionedFixtureInterface;
use CoreShop\Component\Core\Model\CountryInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Pimcore\Tool;
use Rinvex\Country\Country;
use Rinvex\Country\CountryLoader;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Intl\Languages;

class CountryFixture extends AbstractFixture implements ContainerAwareInterface, VersionedFixtureInterface, DependentFixtureInterface
{
    private ?ContainerInterface $container;

    public function getVersion(): string
    {
        return '2.0';
    }

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    public function getDependencies(): array
    {
        return [
            ZoneFixture::class,
            CurrencyFixture::class,
            StoreFixture::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $countries = CountryLoader::countries(true, true);
        /**
         * @var StoreInterface $store
         */
        $store = $this->getReference('store');

        $addressFormatReplaces = [
            'recipient' => [
                '{{ company }}',
                PHP_EOL,
                '{{ salutation }}',
                '{{ firstname }}',
                '{{ lastname }}',
            ],
            'street' => [
                '{{ street}}',
                '{{ number }}',
            ],
            'postalcode' => ' {{ postcode }}',
            'city' => '{{ city }}',
            'country' => [
                '{{ country.name }}',
                '{{ phoneNumber }}',
            ],
            'region' => '',
            'region_short' => ''
        ];
        $defaultAddressFormat = "{{recipient}}\n{{street}}\n{{postalcode}} {{city}}\n{{country}}";
        $defaultSalutations = ['mrs', 'mr'];
        $languages = Tool::getValidLanguages();
        $alpha3CodeMap = [];

        foreach ($languages as $lang) {
            $langPart = strpos($lang, '_') ? explode('_', $lang)[0] : $lang;
            $alpha3CodeMap[$lang] = Languages::getAlpha3Code($langPart);
        }

        foreach ($countries as $country) {
            if ($country instanceof Country) {
                if (null === $country->getCurrency() || !isset($country->getCurrency()['iso_4217_code'])) {
                    continue;
                }

                /**
                 * @var CountryInterface $newCountry
                 */
                $newCountry = $this->container->get('coreshop.repository.country')->findByCode($country->getIsoAlpha2());

                if (null === $newCountry) {
                    $newCountry = $this->container->get('coreshop.factory.country')->createNew();
                }

                foreach ($languages as $lang) {
                    $translation = $country->getTranslation($alpha3CodeMap[$lang]);

                    $newCountry->setName($translation['common'], $lang);
                }

                $newCountry->setIsoCode($country->getIsoAlpha2());
                $newCountry->setActive($country->getIsoAlpha2() === 'AT' || $newCountry->getActive());
                $newCountry->setZone($this->container->get('coreshop.repository.zone')->findOneBy(['name' => $country->getContinent()]));
                $newCountry->setCurrency($this->container->get('coreshop.repository.currency')->getByCode($country->getCurrency()['iso_4217_code']));

                $this->setReference('country_' . $country->getIsoAlpha2(), $newCountry);

                $extra = $country->getExtra();
                $addressFormat = $defaultAddressFormat;

                if (array_key_exists('address_format', $extra) && !empty($extra['address_format'])) {
                    $addressFormat = $extra['address_format'];
                }

                foreach ($addressFormatReplaces as $replaceKey => $replaces) {
                    if (!is_array($replaces)) {
                        $replaces = [$replaces];
                    }

                    $replaceTo = trim(implode(' ', $replaces));
                    $replaceFrom = '{{' . $replaceKey . '}}';

                    $addressFormat = str_replace($replaceFrom, $replaceTo, $addressFormat);
                }

                $addressFormat = explode(PHP_EOL, $addressFormat);
                $addressFormat = array_map(function($entry) {
                    return trim($entry);
                }, $addressFormat);
                $addressFormat = implode(PHP_EOL, $addressFormat);

                $newCountry->setAddressFormat($addressFormat);
                $newCountry->setSalutations($defaultSalutations);
                $manager->persist($newCountry);

                if ($country->getIsoAlpha2() === 'AT') {
                    //States
                    $divisions = $country->getDivisions();

                    if (is_array($divisions)) {
                        foreach ($divisions as $isoCode => $division) {
                            if (!$division['name']) {
                                continue;
                            }

                            $state = $this->container->get('coreshop.factory.state')->createNew();

                            foreach ($languages as $lang) {
                                $state->setName($division['name'], $lang);
                            }

                            $state->setIsoCode($isoCode);
                            $state->setCountry($newCountry);
                            $state->setActive(true);

                            $manager->persist($state);
                        }
                    }
                }

                $store->addCountry($newCountry);
            }
        }

        $manager->persist($store);
        $manager->flush();

        if ($this->hasReference('currency_EUR')) {
            $store->setCurrency($this->getReference('currency_EUR'));
        }

        if ($this->hasReference('country_AT')) {
            $store->setBaseCountry($this->getReference('country_AT'));
        }

        $manager->persist($store);
        $manager->flush();
    }
}
