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

namespace CoreShop\Bundle\IndexBundle\DependencyInjection;

use CoreShop\Bundle\IndexBundle\Attribute\AsDynamicOrderRenderer;
use CoreShop\Bundle\IndexBundle\Attribute\AsDynamicRenderer;
use CoreShop\Bundle\IndexBundle\Attribute\AsExtension;
use CoreShop\Bundle\IndexBundle\Attribute\AsFilterCondition;
use CoreShop\Bundle\IndexBundle\Attribute\AsFilterPreCondition;
use CoreShop\Bundle\IndexBundle\Attribute\AsFilterUserCondition;
use CoreShop\Bundle\IndexBundle\Attribute\AsGetter;
use CoreShop\Bundle\IndexBundle\Attribute\AsInterpreter;
use CoreShop\Bundle\IndexBundle\Attribute\AsWorker;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterConditionRendererTypesPass;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterExtensionsPass;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterFilterConditionTypesPass;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterFilterPreConditionTypesPass;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterFilterUserConditionTypesPass;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterGetterPass;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterIndexWorkerPass;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterInterpreterPass;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterOrderRendererTypesPass;
use CoreShop\Bundle\IndexBundle\Worker\MysqlWorker;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractModelExtension;
use CoreShop\Component\Index\Condition\DynamicRendererInterface;
use CoreShop\Component\Index\Extension\IndexExtensionInterface;
use CoreShop\Component\Index\Filter\FilterConditionProcessorInterface;
use CoreShop\Component\Index\Filter\FilterPreConditionProcessorInterface;
use CoreShop\Component\Index\Filter\FilterUserConditionProcessorInterface;
use CoreShop\Component\Index\Getter\GetterInterface;
use CoreShop\Component\Index\Interpreter\InterpreterInterface;
use CoreShop\Component\Index\Order\DynamicOrderRendererInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use CoreShop\Component\Registry\Autoconfiguration;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class CoreShopIndexExtension extends AbstractModelExtension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container): void
    {
        $bundles = $container->getParameter('kernel.bundles');

        if (array_key_exists('PimcoreStudioBackendBundle', $bundles)) {
            $container->prependExtensionConfig('pimcore_studio_backend', [
                'data_object_data_adapter_mapping' => [
                    'CoreShop\\Bundle\\ResourceBundle\\StudioBackend\\DataAdapter\\CoreShopSelectAdapter' => [
                        'coreShopFilter',
                    ],
                ],
            ]);
        }
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configs = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->registerResources('coreshop', CoreShopResourceBundle::DRIVER_DOCTRINE_ORM, $configs['resources'], $container);

        $bundles = $container->getParameter('kernel.bundles');

        if (isset($configs['mapping_types'])) {
            $configs['worker_mapping_types']['mysql'] = array_merge(
                $configs['mapping_types'],
                $configs['worker_mapping_types']['mysql'] ?? [],
            );
        }

        $container->setParameter('coreshop.index.mapping_types', array_keys($configs['mapping_types'] ?? []));
        $container->setParameter('coreshop.index.worker_mapping_types', $configs['worker_mapping_types']);

        $loader->load('services.yml');

        if (!array_key_exists('CoreShopCoreBundle', $bundles)) {
            $loader->load('services/menu.yml');
            $loader->load('services/installer.yml');
        }

        if (array_key_exists('PimcoreDataHubBundle', $bundles)) {
            $loader->load('services/data_hub.yml');
        }

        if (array_key_exists('PimcoreStudioUiBundle', $bundles)) {
            $loader->load('services/studio.yml');
        }

        $this->registerPimcoreResources('coreshop', $configs['pimcore_admin'], $container);

        $container->getDefinition(MysqlWorker::class)->setArgument(
            7,
            $configs['mysql_auto_generate_migrations'],
        );

        Autoconfiguration::registerForAutoConfiguration(
            $container,
            DynamicRendererInterface::class,
            RegisterConditionRendererTypesPass::INDEX_CONDITION_RENDERER_TAG,
            AsDynamicRenderer::class,
            $configs['autoconfigure_with_attributes'],
        );

        Autoconfiguration::registerForAutoConfiguration(
            $container,
            DynamicOrderRendererInterface::class,
            RegisterOrderRendererTypesPass::INDEX_ORDER_RENDERER_TAG,
            AsDynamicOrderRenderer::class,
            $configs['autoconfigure_with_attributes'],
        );

        Autoconfiguration::registerForAutoConfiguration(
            $container,
            IndexExtensionInterface::class,
            RegisterExtensionsPass::INDEX_EXTENSION_TAG,
            AsExtension::class,
            $configs['autoconfigure_with_attributes'],
        );

        Autoconfiguration::registerForAutoConfiguration(
            $container,
            FilterConditionProcessorInterface::class,
            RegisterFilterConditionTypesPass::INDEX_FILTER_CONDITION_TAG,
            AsFilterCondition::class,
            $configs['autoconfigure_with_attributes'],
        );

        Autoconfiguration::registerForAutoConfiguration(
            $container,
            FilterPreConditionProcessorInterface::class,
            RegisterFilterPreConditionTypesPass::INDEX_FILTER_PRE_CONDITION_TAG,
            AsFilterPreCondition::class,
            $configs['autoconfigure_with_attributes'],
        );

        Autoconfiguration::registerForAutoConfiguration(
            $container,
            FilterUserConditionProcessorInterface::class,
            RegisterFilterUserConditionTypesPass::INDEX_FILTER_USER_CONDITION_TAG,
            AsFilterUserCondition::class,
            $configs['autoconfigure_with_attributes'],
        );

        Autoconfiguration::registerForAutoConfiguration(
            $container,
            GetterInterface::class,
            RegisterGetterPass::INDEX_GETTER_TAG,
            AsGetter::class,
            $configs['autoconfigure_with_attributes'],
        );

        Autoconfiguration::registerForAutoConfiguration(
            $container,
            WorkerInterface::class,
            RegisterIndexWorkerPass::INDEX_WORKER_TAG,
            AsWorker::class,
            $configs['autoconfigure_with_attributes'],
        );

        Autoconfiguration::registerForAutoConfiguration(
            $container,
            InterpreterInterface::class,
            RegisterInterpreterPass::INDEX_INTERPRETER_TAG,
            AsInterpreter::class,
            $configs['autoconfigure_with_attributes'],
        );
    }
}
