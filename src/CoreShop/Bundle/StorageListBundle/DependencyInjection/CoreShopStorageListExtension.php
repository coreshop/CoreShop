<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\StorageListBundle\DependencyInjection;

use CoreShop\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractModelExtension;
use CoreShop\Bundle\StorageListBundle\DependencyInjection\Compiler\RegisterStorageListPass;
use CoreShop\Bundle\StorageListBundle\DependencyInjection\Compiler\RegisterStorageListProcessorPass;
use CoreShop\Bundle\StorageListBundle\EventListener\SessionSubscriber;
use CoreShop\Component\StorageList\Context\CompositeStorageListContext;
use CoreShop\Component\StorageList\Context\StorageListContextInterface;
use CoreShop\Component\StorageList\Context\StorageListFactoryContext;
use CoreShop\Component\StorageList\Processor\CompositeStorageListProcessor;
use CoreShop\Component\StorageList\Processor\StorageListProcessorInterface;
use CoreShop\Component\StorageList\StorageListsManager;
use Pimcore\Http\Request\Resolver\PimcoreContextResolver;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

final class CoreShopStorageListExtension extends AbstractModelExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configs = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.yml');

        $manager = $container->findDefinition(StorageListsManager::class);

        foreach ($configs['list'] as $name => $list) {
            $isDefaultContextInterface = $list['context']['interface'] === StorageListContextInterface::class;
            $isDefaultContextComposite = $list['context']['composite'] === CompositeStorageListContext::class;
            $isDefaultProcessorInterface = $list['processor']['interface'] === StorageListProcessorInterface::class;
            $isDefaultProcessorComposite = $list['processor']['composite'] === CompositeStorageListProcessor::class;

            $contextCompositeServiceName = $isDefaultContextComposite ? 'coreshop.context.storage_list.' . $name : $list['context']['composite'];
            $processorCompositeServiceName = $isDefaultProcessorComposite ? 'coreshop.processor.storage_list.' . $name : $list['processor']['composite'];

            if (!interface_exists($list['context']['interface'])) {
                throw new \RuntimeException(
                    sprintf(
                        'Interface %s for Storage List Context "%s" does not exist',
                        $list['context']['interface'],
                        $name,
                    )
                );
            }

            if (!interface_exists($list['processor']['interface'])) {
                throw new \RuntimeException(
                    sprintf(
                        'Interface %s for Storage List Processor "%s" does not exist',
                        $list['context']['interface'],
                        $name,
                    )
                );
            }

            if (!$container->hasDefinition($contextCompositeServiceName)) {
                $compositeService = new Definition($list['context']['composite']);
                $compositeService->setPublic(true);

                $container->setDefinition($contextCompositeServiceName, $compositeService);
            }

            if (!$isDefaultContextInterface && !$container->has($list['context']['interface'])) {
                $interfaceAlias = new Alias($contextCompositeServiceName, true);

                $container->setAlias($list['context']['interface'], $interfaceAlias);
            }

            if (!$container->has($list['processor']['composite'])) {
                $compositeService = new Definition($list['processor']['composite']);
                $compositeService->setPublic(true);

                $container->setDefinition($processorCompositeServiceName, $compositeService);
            }

            if (!$isDefaultProcessorInterface && !$container->has($list['processor']['interface'])) {
                $interfaceAlias = new Alias($processorCompositeServiceName, true);

                $container->setAlias($list['processor']['interface'], $interfaceAlias);
            }

            if (!$isDefaultContextInterface) {
                $container
                    ->registerForAutoconfiguration($list['context']['interface'])
                    ->addTag($list['context']['tag']);
            }

            if (!$isDefaultProcessorInterface) {
                $container
                    ->registerForAutoconfiguration($list['processor']['interface'])
                    ->addTag($list['processor']['tag']);
            }

            $factoryContextDefinition = new Definition(StorageListFactoryContext::class);
            $factoryContextDefinition->setArgument('$storageListFactory', new Reference($list['resource']['factory']));
            $factoryContextDefinition->addTag($list['context']['tag'], ['priority' => 0]);

            $container->setDefinition('coreshop.storage_list.context.factory.' . $name, $factoryContextDefinition);

            if ($list['session']['enabled']) {
                $sessionSubscriber = new Definition(SessionSubscriber::class, [
                    new Reference(PimcoreContextResolver::class),
                    new Reference($processorCompositeServiceName),
                    $list['session']['key']
                ]);
                
                $container->setDefinition('coreshop.storage_list.session_subscriber.' . $name, $sessionSubscriber);
            }
            if ($list['controller']['enabled']) {
                $class = $list['controller']['class'];

                if ($container->has($class)) {
                    $controllerDefinition = $container->getDefinition($class);
                }
                else {
                    $controllerDefinition = new Definition($class);
                }

                $controllerDefinition->setArgument('$formFactory', new Reference('form.factory'));
                $controllerDefinition->setArgument('$repository', new Reference($list['resource']['repository']));
                $controllerDefinition->setArgument('$productRepository', new Reference($list['resource']['product_repository']));
                $controllerDefinition->setArgument('$itemRepository', new Reference($list['resource']['item_repository']));
                $controllerDefinition->setArgument('$context', new Reference($contextCompositeServiceName));
                $controllerDefinition->setArgument('$storageListItemFactory', new Reference($list['resource']['item_factory']));
                $controllerDefinition->setArgument('$addToStorageListFactory', new Reference($list['resource']['add_to_list_factory']));
                $controllerDefinition->setArgument('$modifier', new Reference($list['services']['modifier']));
                $controllerDefinition->setArgument('$manager', new Reference($list['services']['manager']));
                $controllerDefinition->setArgument('$addToStorageListForm', $list['form']['add_type']);
                $controllerDefinition->setArgument('$form', $list['form']['type']);
                $controllerDefinition->setArgument('$summaryRoute', $list['routes']['summary']);
                $controllerDefinition->setArgument('$indexRoute', $list['routes']['index']);
                $controllerDefinition->setArgument('$templateSummary', $list['templates']['summary']);
                $controllerDefinition->setArgument('$templateAddToList', $list['templates']['add_to_cart']);
                $controllerDefinition->addTag('controller.service_arguments');
                $controllerDefinition->addMethodCall('setContainer', [new Reference('service_container')]);

                $container->setDefinition('coreshop.storage_list.controller.' . $name, $controllerDefinition);
            }

            $manager->addMethodCall('addList', [
                $name,
                new Reference($list['services']['manager']),
                new Reference($contextCompositeServiceName),
                new Reference($processorCompositeServiceName),
                new Reference($list['services']['modifier']),
            ]);

            (new RegisterStorageListPass(
                $list['context']['interface'],
                $contextCompositeServiceName,
                $list['context']['tag']
            ))->process($container);
            (new RegisterStorageListProcessorPass(
                $list['processor']['interface'],
                $processorCompositeServiceName,
                $list['processor']['tag']
            ))->process($container);
        }
    }
}
