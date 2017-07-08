<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Migrations\Data\ORM;

use CoreShop\Component\Core\Model\CountryInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Okvpn\Bundle\MigrationBundle\Fixture\VersionedFixtureInterface;
use Pimcore\Tool;
use Rinvex\Country\Country;
use Rinvex\Country\CountryLoader;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Intl\Intl;

class CountryFixture extends AbstractFixture implements ContainerAwareInterface, VersionedFixtureInterface, DependentFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return '2.0.1';
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
            'CoreShop\Bundle\CoreBundle\Migrations\Data\ORM\ZoneFixture',
            'CoreShop\Bundle\CoreBundle\Migrations\Data\ORM\CurrencyFixture',
            'CoreShop\Bundle\CoreBundle\Migrations\Data\ORM\StoreFixture',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $countries = CountryLoader::countries(true, true);

        $addressFormatReplaces = [
            'recipient' => [
                '%Text(company);',
                PHP_EOL,
                '%Text(firstname);',
                '%Text(lastname);',
            ],
            'street' => [
                '%Text(street);',
                '%Text(number);',
            ],
            'postalcode' => ' %Text(postcode); ',
            'city' => '%Text(city);',
            'country' => [
                '%Object(country,{"method" : "getName"});',
                '%Text(phone);',
            ],
            'region' => ''
        ];
        $defaultAddressFormat = "{{recipient}}\n{{street}}\n{{postalcode}} {{city}}\n{{country}}";
        $languages = Tool::getValidLanguages();
        $alpha3CodeMap = [];

        foreach ($languages as $lang) {
            if (strpos($lang, '_')) {
                $lang = explode('_', $lang)[0];
            }

            $alpha3CodeMap[$lang] = Intl::getLanguageBundle()->getAlpha3Code($lang);
        }

        foreach ($countries as $country) {
            if ($country instanceof Country) {
                /**
                 * @var $newCountry CountryInterface
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
                $newCountry->setActive($country->getIsoAlpha2() === 'AT');
                $newCountry->setZone($this->container->get('coreshop.repository.zone')->findOneBy(['name' => $country->getContinent()]));
                $newCountry->addStore($this->container->get('coreshop.repository.store')->findStandard());
                $newCountry->setCurrency($this->container->get('coreshop.repository.currency')->getByCode($country->getCurrency()['iso_4217_code']));

                $extra = $country->getExtra();
                $addressFormat = $defaultAddressFormat;

                if (array_key_exists('address_format', $extra) && !empty($extra['address_format'])) {
                    $addressFormat = $extra['address_format'];
                }

                foreach ($addressFormatReplaces as $replaceKey => $replaces) {
                    if (!is_array($replaces)) {
                        $replaces = [];
                    }

                    $replaceTo = trim(implode(' ', $replaces));
                    $replaceFrom = '{{' . $replaceKey . '}}';

                    $addressFormat = str_replace($replaceFrom, $replaceTo, $addressFormat);
                }

                $newCountry->setAddressFormat($addressFormat);
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
            }
        }

        $manager->flush();

        $store = $this->container->get('coreshop.repository.store')->findStandard();
        $store->setCurrency($this->container->get('coreshop.repository.currency')->getByCode('EUR'));
        $store->setBaseCountry($this->container->get('coreshop.repository.country')->findOneBy(['isoCode' => 'AT']));

        $manager->persist($store);
        $manager->flush();
    }
}
