<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\MessengerBundle\DependencyInjection;

use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Extension\AbstractPimcoreExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class CoreShopMessengerExtension extends AbstractPimcoreExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $bundles = $container->getParameter('kernel.bundles');

        if (!array_key_exists('CoreShopCoreBundle', $bundles)) {
            $loader->load('services/menu.yml');
        } else {
            $loader->load('services/resource.yml');
        }

        $this->registerPimcoreResources('coreshop', $config['pimcore_admin'], $container);
    }
}
