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

namespace CoreShop\Bundle\PimcoreBundle\DependencyInjection;

use CoreShop\Bundle\PimcoreBundle\Attribute\AsGridAction;
use CoreShop\Bundle\PimcoreBundle\Attribute\AsGridFilter;
use CoreShop\Bundle\PimcoreBundle\Attribute\AsStudioGridFilter;
use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\RegisterGridActionPass;
use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\RegisterGridFilterPass;
use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\RegisterStudioGridFilterPass;
use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Extension\AbstractPimcoreExtension;
use CoreShop\Component\Pimcore\DataObject\Grid\GridActionInterface;
use CoreShop\Component\Pimcore\DataObject\Grid\GridFilterInterface;
use CoreShop\Component\Pimcore\DataObject\Grid\StudioGridFilterInterface;
use CoreShop\Component\Registry\Autoconfiguration;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class CoreShopPimcoreExtension extends AbstractPimcoreExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configs = $this->processConfiguration($this->getConfiguration([], $container), $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $bundles = $container->getParameter('kernel.bundles');

        if (array_key_exists('PimcoreAdminBundle', $bundles)) {
            $loader->load('services/classic_admin.yml');
        }

        if (array_key_exists('PimcoreDataHubBundle', $bundles)) {
            $loader->load('services/data_hub.yml');
        }

        if (array_key_exists('PimcoreStudioUiBundle', $bundles)) {
            $loader->load('services/studio.yml');
        }

        $this->registerPimcoreResources('coreshop', $configs['pimcore_admin'], $container);

        $loader->load('services.yml');

        Autoconfiguration::registerForAutoConfiguration(
            $container,
            GridActionInterface::class,
            RegisterGridActionPass::GRID_ACTION_TAG,
            AsGridAction::class,
            $configs['autoconfigure_with_attributes'],
        );

        Autoconfiguration::registerForAutoConfiguration(
            $container,
            GridFilterInterface::class,
            RegisterGridFilterPass::GRID_FILTER_TAG,
            AsGridFilter::class,
            $configs['autoconfigure_with_attributes'],
        );

        Autoconfiguration::registerForAutoConfiguration(
            $container,
            StudioGridFilterInterface::class,
            RegisterStudioGridFilterPass::STUDIO_GRID_FILTER_TAG,
            AsStudioGridFilter::class,
            $configs['autoconfigure_with_attributes'],
        );
    }
}
