<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\MenuBundle\DependencyInjection;

use CoreShop\Bundle\MenuBundle\Attribute\AsMenuBuilder;
use CoreShop\Bundle\MenuBundle\AdminClass\EventListener\MenuAdminListener;
use CoreShop\Bundle\MenuBundle\Builder\MenuBuilderInterface;
use CoreShop\Bundle\MenuBundle\DependencyInjection\CompilerPass\MenuBuilderPass;
use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Extension\AbstractPimcoreExtension;
use CoreShop\Component\Registry\Autoconfiguration;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class CoreShopMenuExtension extends AbstractPimcoreExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configs = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $bundles = $container->getParameter('kernel.bundles');

        if (!array_key_exists('CoreShopCoreBundle', $bundles)) {
            $loader->load('services/menu.yml');
        }

        if (array_key_exists('PimcoreAdminBundle', $bundles)) {
            $loader->load('services/classic_admin.yml');

            if (array_key_exists('CoreShopCoreBundle', $bundles)) {
                $container->removeDefinition(MenuAdminListener::class);
            }
        }

        if (array_key_exists('PimcoreStudioUiBundle', $bundles)) {
            $loader->load('services/studio.yml');
        }

        Autoconfiguration::registerForAutoConfiguration(
            $container,
            MenuBuilderInterface::class,
            MenuBuilderPass::MENU_BUILDER_TAG,
            AsMenuBuilder::class,
            $configs['autoconfigure_with_attributes'],
        );
    }
}
