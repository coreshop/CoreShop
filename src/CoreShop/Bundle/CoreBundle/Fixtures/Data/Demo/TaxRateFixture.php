<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Fixtures\Data\Demo;

use CoreShop\Bundle\FixtureBundle\Fixture\VersionedFixtureInterface;
use CoreShop\Component\Taxation\Model\TaxRateInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TaxRateFixture extends AbstractFixture implements ContainerAwareInterface, VersionedFixtureInterface
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
        if (!count($this->container->get('coreshop.repository.tax_rate')->findAll())) {
            /**
             * @var TaxRateInterface $taxRate
             */
            $taxRate = $this->container->get('coreshop.factory.tax_rate')->createNew();
            $taxRate->setName('20AT', 'de');
            $taxRate->setName('20AT', 'en');
            $taxRate->setActive(true);
            $taxRate->setRate(20);

            $taxRate10 = $this->container->get('coreshop.factory.tax_rate')->createNew();
            $taxRate10->setName('10AT', 'de');
            $taxRate10->setName('10AT', 'en');
            $taxRate10->setActive(true);
            $taxRate10->setRate(10);

            $manager->persist($taxRate);
            $manager->persist($taxRate10);
            $manager->flush();

            $this->addReference('taxRate', $taxRate);
            $this->addReference('taxRate10', $taxRate10);
        }
    }
}
