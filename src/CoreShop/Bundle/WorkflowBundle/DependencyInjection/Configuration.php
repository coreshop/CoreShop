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

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('core_shop_workflow');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $smNode = $rootNode
            ->children()
                ->arrayNode('state_machine')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
        ;

        $this->addStateMachineSection($smNode);
        $this->addColorSection($smNode);
        $this->addCallBackSection($smNode);

        $smNode->end()->end()->end()->end();

        return $treeBuilder;
    }

    private function addStateMachineSection(NodeBuilder $node): void
    {
        $node
            ->arrayNode('places')
                ->isRequired()
                ->requiresAtLeastOneElement()
                ->prototype('scalar')
                    ->cannotBeEmpty()
                ->end()
            ->end()
            ->arrayNode('transitions')
                ->beforeNormalization()
                    ->always()
                    ->then(function (array $transitions) {
                        // It's an indexed array, we let the validation occurs
                        if (isset($transitions[0])) {
                            return $transitions;
                        }

                        foreach ($transitions as $name => $transition) {
                            if (array_key_exists('name', $transition)) {
                                continue;
                            }
                            $transition['name'] = $name;
                            $transitions[$name] = $transition;
                        }

                        return $transitions;
                    })
                ->end()
                ->isRequired()
                ->requiresAtLeastOneElement()
                ->useAttributeAsKey('transition')
                ->arrayPrototype()
                    ->children()
                        ->scalarNode('name')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('guard')
                            ->cannotBeEmpty()
                            ->info('An expression to block the transition')
                            ->example('is_fully_authenticated() and has_role(\'ROLE_JOURNALIST\') and subject.getTitle() == \'My first article\'')
                        ->end()
                        ->arrayNode('from')
                            ->performNoDeepMerging()
                            ->beforeNormalization()
                                ->ifString()
                                ->then(function (mixed $v) {
                                    return [$v];
                                })
                            ->end()
                            ->requiresAtLeastOneElement()
                            ->prototype('scalar')
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                        ->arrayNode('to')
                            ->performNoDeepMerging()
                            ->beforeNormalization()
                                ->ifString()
                                ->then(function (mixed $v) {
                                    return [$v];
                                })
                            ->end()
                            ->requiresAtLeastOneElement()
                            ->prototype('scalar')
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addColorSection(NodeBuilder $node): void
    {
        $node
            ->arrayNode('place_colors')
                ->useAttributeAsKey('name')
                ->prototype('scalar')->end()
            ->end()
        ;

        $node
            ->arrayNode('transition_colors')
                ->useAttributeAsKey('name')
                ->prototype('scalar')->end()
            ->end()
        ;
    }

    private function addCallBackSection(NodeBuilder $node): void
    {
        $callbacks = $node
            ->arrayNode('callbacks')
        ;

        $this->addSubCallbackSection($callbacks, 'guard');
        $this->addSubCallbackSection($callbacks, 'before');
        $this->addSubCallbackSection($callbacks, 'after');

        $callbacks->end()->end();
    }

    private function addSubCallbackSection(ArrayNodeDefinition $callbacks, string $type): void
    {
        $callbacks
            ->children()
                ->arrayNode($type)
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->booleanNode('enabled')->defaultTrue()->end()
                            ->variableNode('on')->end()
                            ->variableNode('do')->end()
                            ->scalarNode('priority')->defaultValue(0)->end()
                            ->arrayNode('args')->performNoDeepMerging()->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
