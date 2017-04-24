<?php

namespace CoreShop\Bundle\CoreBundle\Migrations\Data\ORM;

use CoreShop\Component\Core\Model\CurrencyInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Okvpn\Bundle\MigrationBundle\Fixture\VersionedFixtureInterface;
use Rinvex\Country\Country;
use Rinvex\Country\CountryLoader;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Intl\Intl;

class CurrencyFixture extends AbstractFixture implements ContainerAwareInterface, VersionedFixtureInterface
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
    public function load(ObjectManager $manager)
    {
        $countries = CountryLoader::countries(true, true);
        $currencies = [];

        foreach ($countries as $country) {
            if ($country instanceof Country) {
                $currency = $country->getCurrency();

                if ($currency) {
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
            /**
             * @var $currency CurrencyInterface
             */
            $currency = $this->container->get('coreshop.factory.currency')->createNew();
            $currency->setName($c['iso_4217_name']);
            $currency->setIsoCode($iso);
            $currency->setNumericIsoCode($c['iso_4217_numeric']);
            $currency->setSymbol(Intl::getCurrencyBundle()->getCurrencySymbol($iso));
            $currency->setExchangeRate(0);

            $manager->persist($currency);
        }

        $manager->flush();
    }
}