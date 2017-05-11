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
use Rinvex\Country\Country;
use Rinvex\Country\CountryLoader;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
        return '2.0';
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
                '%Text(nr);',
            ],
            'postalcode' => ' %Text(postcode); ',
            'city' => '%Text(city);',
            'country' => [
                '%Object(country,{"method" : "getName"});',
                '%Text(phone);',
            ],
        ];
        $defaultAddressFormat = "{{recipient}}\n{{street}}\n{{postalcode}} {{city}}\n{{country}}";

        foreach ($countries as $country) {
            /**
             * @var CountryInterface
             */
            $newCountry = $this->container->get('coreshop.factory.country')->createNew();

            if ($country instanceof Country) {
                $newCountry->setName($country->getName());
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
                    $replaceFrom = '{{'.$replaceKey.'}}';

                    $addressFormat = str_replace($replaceFrom, $replaceTo, $addressFormat);
                }

                $newCountry->setAddressFormat($addressFormat);
                $manager->persist($newCountry);
            }
        }

        $manager->flush();

        $store = $this->container->get('coreshop.repository.store')->findStandard();
        $store->setBaseCurrency($this->container->get('coreshop.repository.currency')->getByCode('EUR'));
        $store->setBaseCountry($this->container->get('coreshop.repository.country')->findOneBy(['isoCode' => 'AT']));

        $manager->persist($store);
        $manager->flush();
    }
}
