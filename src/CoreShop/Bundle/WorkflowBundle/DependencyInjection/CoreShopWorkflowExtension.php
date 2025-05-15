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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\WorkflowBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class CoreShopWorkflowExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configs = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.yml');

        $callbackConfig = [];
        $colorConfig = [];

        $coreShopStateMachines = [];

        if (is_array($configs['state_machine'])) {
            foreach ($configs['state_machine'] as $stateMachineName => $stateMachineConfig) {
                $coreShopStateMachines[] = $stateMachineName;

                $data = [];
                if (isset($stateMachineConfig['places'])) {
                    $data['places'] = $stateMachineConfig['places'];
                }
                if (isset($stateMachineConfig['transitions'])) {
                    $data['transitions'] = $stateMachineConfig['transitions'];
                }
                $container->prependExtensionConfig('framework', ['workflows' => [$stateMachineName => $data]]);

                if (isset($stateMachineConfig['callbacks'])) {
                    $callbackConfig[$stateMachineName] = $stateMachineConfig['callbacks'];
                }

                if (isset($stateMachineConfig['callbacks'])) {
                    $callbackConfig[$stateMachineName] = $stateMachineConfig['callbacks'];
                }

                if (isset($stateMachineConfig['place_colors'])) {
                    $colorConfig[$stateMachineName]['place_colors'] = $stateMachineConfig['place_colors'];
                }

                if (isset($stateMachineConfig['transition_colors'])) {
                    $colorConfig[$stateMachineName]['transition_colors'] = $stateMachineConfig['transition_colors'];
                }
            }
        }

        $container->setParameter('coreshop.state_machines', $coreShopStateMachines);
        $container->setParameter('coreshop.state_machine.callbacks', $callbackConfig);
        $container->setParameter('coreshop.state_machine.colors', $colorConfig);
    }
}
