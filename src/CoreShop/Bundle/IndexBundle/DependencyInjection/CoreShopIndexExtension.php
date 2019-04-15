<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\IndexBundle\DependencyInjection;

use CoreShop\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractModelExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class CoreShopIndexExtension extends AbstractModelExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->registerResources('coreshop', $config['driver'], $config['resources'], $container);

        $bundles = $container->getParameter('kernel.bundles');

        $container->setParameter('coreshop.index.mapping_types', array_keys($config['mapping_types']));

        $loader->load('services.yml');

        if (array_key_exists('ProcessManagerBundle', $bundles)) {
            $loader->load('services/process_manager.yml');
        }

        if (!array_key_exists('CoreShopCoreBundle', $bundles)) {
            $loader->load('services/menu.yml');

            $config['pimcore_admin']['js']['menu'] = '/admin/coreshop/coreshop.index/menu.js';
        }

        $this->registerPimcoreResources('coreshop', $config['pimcore_admin'], $container);
    }
}
