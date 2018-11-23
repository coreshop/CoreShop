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

namespace CoreShop\Bundle\TrackingBundle\DependencyInjection;

use CoreShop\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractModelExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class CoreShopTrackingExtension extends AbstractModelExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $this->configureTrackers($config, $container);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    protected function configureTrackers(array $config, ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds('coreshop.tracking.tracker') as $id => $attributes) {
            if (!isset($attributes[0]['type'])) {
                continue;
            }

            $type = $attributes[0]['type'];

            if (!array_key_exists($type, $config['trackers'])) {
                $container->getDefinition($id)->addMethodCall('setEnabled', [false]);
            } else {
                $container->getDefinition($id)->addMethodCall('setEnabled', [$config['trackers'][$type]['enabled']]);
            }
        }
    }
}
