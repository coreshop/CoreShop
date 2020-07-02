<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Fixtures\Data\Application;

use CoreShop\Bundle\FixtureBundle\Fixture\VersionedFixtureInterface;
use CoreShop\Component\Core\Configuration\ConfigurationServiceInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
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
            'system.order.prefix' => 'O',
            'system.order.suffix' => '',
            'system.quote.prefix' => 'Q',
            'system.quote.suffix' => '',
            'system.invoice.prefix' => 'IN',
            'system.invoice.suffix' => '',
            'system.invoice.wkhtml' => '-T 40mm -B 15mm -L 10mm -R 10mm --header-spacing 5 --footer-spacing 5',
            'system.shipment.prefix' => 'SH',
            'system.shipment.suffix' => '',
            'system.shipment.wkhtml' => '-T 40mm -B 15mm -L 10mm -R 10mm --header-spacing 5 --footer-spacing 5',
        ];

        foreach ($configurations as $key => $value) {
            $this->container->get(ConfigurationServiceInterface::class)->set($key, $value);
        }
    }
}
