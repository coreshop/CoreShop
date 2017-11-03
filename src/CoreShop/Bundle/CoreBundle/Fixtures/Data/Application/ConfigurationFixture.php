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

namespace CoreShop\Bundle\CoreBundle\Fixtures\Application;

use CoreShop\Bundle\FixtureBundle\Fixture\VersionedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ConfigurationFixture extends AbstractFixture implements ContainerAwareInterface, VersionedFixtureInterface
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
        $configurations = [
            'system.guest.checkout' => true,
            'system.category.list.mode' => 'list',
            'system.category.list.per_page' => [12, 24, 36],
            'system.category.list.per_page.default' => 12,
            'system.category.grid.per_page' => [5, 10, 15, 20, 25],
            'system.category.grid.per_page.default' => 10,
            'system.category.variant_mode' => 'hide',
        ];

        foreach ($configurations as $key => $value) {
            $this->container->get('coreshop.configuration.service')->set($key, $value);
        }
    }
}
