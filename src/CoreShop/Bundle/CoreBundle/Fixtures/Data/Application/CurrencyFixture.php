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
use CoreShop\Component\Core\Model\CurrencyInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Rinvex\Country\Country;
use Rinvex\Country\CountryLoader;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Intl\Currencies;

class CurrencyFixture extends AbstractFixture implements ContainerAwareInterface, VersionedFixtureInterface
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
            /**
             * @var CurrencyInterface
             */
            $currency = $this->container->get('coreshop.repository.currency')->getByCode($iso);
            if (null === $currency) {
                $currency = $this->container->get('coreshop.factory.currency')->createNew();
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
