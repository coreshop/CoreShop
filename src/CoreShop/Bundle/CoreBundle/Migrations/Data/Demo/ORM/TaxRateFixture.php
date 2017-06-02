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

namespace CoreShop\Bundle\CoreBundle\Migrations\Data\Demo\ORM;

use CoreShop\Component\Taxation\Model\TaxRateInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Okvpn\Bundle\MigrationBundle\Fixture\VersionedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TaxRateFixture extends AbstractFixture implements ContainerAwareInterface, VersionedFixtureInterface
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
        if (!count($this->container->get('coreshop.repository.tax_rate')->findAll())) {
            /**
             * @var $taxRate TaxRateInterface
             */
            $taxRate = $this->container->get('coreshop.factory.tax_rate')->createNew();
            $taxRate->setName('20AT', 'de');
            $taxRate->setName('20AT', 'en');
            $taxRate->setActive(1);
            $taxRate->setRate(20);

            $taxRate10 = $this->container->get('coreshop.factory.tax_rate')->createNew();
            $taxRate10->setName('10AT', 'de');
            $taxRate10->setName('10AT', 'en');
            $taxRate10->setActive(1);
            $taxRate10->setRate(10);

            $manager->persist($taxRate);
            $manager->persist($taxRate10);
            $manager->flush();

            $this->addReference('taxRate', $taxRate);
            $this->addReference('taxRate10', $taxRate10);
        }
    }
}
