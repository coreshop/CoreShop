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

use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterConditionRendererTypesPass;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterExtensionsPass;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterFilterConditionTypesPass;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterGetterPass;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterIndexWorkerPass;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterInterpreterPass;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterOrderRendererTypesPass;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractModelExtension;
use CoreShop\Component\Index\Condition\DynamicRendererInterface;
use CoreShop\Component\Index\Extension\IndexExtensionInterface;
use CoreShop\Component\Index\Filter\FilterConditionProcessorInterface;
use CoreShop\Component\Index\Getter\GetterInterface;
use CoreShop\Component\Index\Interpreter\InterpreterInterface;
use CoreShop\Component\Index\Order\DynamicOrderRendererInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
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
            $loader->load('services/installer.yml');
        }

        if (array_key_exists('PimcoreDataHubBundle', $bundles)) {
            $loader->load('services/data_hub.yml');
        }

        $this->registerPimcoreResources('coreshop', $config['pimcore_admin'], $container);

        $container
            ->registerForAutoconfiguration(DynamicRendererInterface::class)
            ->addTag(RegisterConditionRendererTypesPass::INDEX_CONDITION_RENDERER_TAG)
        ;

        $container
            ->registerForAutoconfiguration(DynamicOrderRendererInterface::class)
            ->addTag(RegisterOrderRendererTypesPass::INDEX_ORDER_RENDERER_TAG)
        ;

        $container
            ->registerForAutoconfiguration(IndexExtensionInterface::class)
            ->addTag(RegisterExtensionsPass::INDEX_EXTENSION_TAG)
        ;

        $container
            ->registerForAutoconfiguration(FilterConditionProcessorInterface::class)
            ->addTag(RegisterFilterConditionTypesPass::INDEX_FILTER_CONDITION_TAG)
        ;

        $container
            ->registerForAutoconfiguration(GetterInterface::class)
            ->addTag(RegisterGetterPass::INDEX_GETTER_TAG)
        ;

        $container
            ->registerForAutoconfiguration(WorkerInterface::class)
            ->addTag(RegisterIndexWorkerPass::INDEX_WORKER_TAG)
        ;

        $container
            ->registerForAutoconfiguration(InterpreterInterface::class)
            ->addTag(RegisterInterpreterPass::INDEX_INTERPRETER_TAG)
        ;
    }
}
