<?php

namespace CoreShop\Bundle\CoreBundle\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Okvpn\Bundle\MigrationBundle\Fixture\VersionedFixtureInterface;
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
            'system.catalog.mode' => false,
            'system.guest.checkout' => true,
            'system.stock.default_out_of_stock.behaviour' => 1,
            'system.taxation.address' => 'shipping',
            'system.prices.gross' => false,
            'system.shipment.create' => true,
            'system.shipment.prefix' => 'SH',
            'system.shipment.suffix' => '',
            'system.shipment.wkhtml' => '-T 40mm -B 15mm -L 10mm -R 10mm --header-spacing 5 --footer-spacing 5',
            'system.product.fallback_image' => null,
            'system.category.fallback_image' => null,
            'system.category.list.mode' => 'list',
            'system.category.list.per_page' => [12, 24, 36],
            'system.category.list.per_page.default' => 12,
            'system.category.grid.per_page' => [5, 10, 15, 20, 25],
            'system.category.grid.per_page.default' => 10,
            'system.category.variant_mode' => 'hide',
            'system.order.prefix' => 'O',
            'system.order.suffix' => '',
            'system.invoice.create' => true,
            'system.invoice.prefix' => 'IN',
            'system.invoice.suffix' => '',
            'system.invoice.wkhtml' => '-T 40mm -B 15mm -L 10mm -R 10mm --header-spacing 5 --footer-spacing 5',
        ];

        foreach ($configurations as $key => $value) {
            $this->container->get('coreshop.configuration.service')->set($key, $value);
        }
    }
}