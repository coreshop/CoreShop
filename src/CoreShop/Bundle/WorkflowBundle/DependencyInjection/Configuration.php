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

namespace CoreShop\Bundle\WorkflowBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('coreshop_workflow');

        $smNode = $rootNode
            ->children()
                ->arrayNode('state_machine')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children();

        $this->addStateMachineSection($smNode);
        $this->addColorSection($smNode);
        $this->addCallBackSection($smNode);

        $smNode->end()->end()->end()->end();

        return $treeBuilder;
    }

    /**
     * @param NodeBuilder $node
     */
    private function addStateMachineSection(NodeBuilder $node)
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
                    ->then(function($transitions) {
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
                ->prototype('array')
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
                                ->then(function($v) { return array($v); })
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
                                ->then(function($v) { return array($v); })
                            ->end()
                            ->requiresAtLeastOneElement()
                            ->prototype('scalar')
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

    }

    /**
     * @param NodeBuilder $node
     */
    private function addColorSection(NodeBuilder $node)
    {
        $node
            ->arrayNode('place_colors')
                ->useAttributeAsKey('name')
                ->prototype('scalar')->end()
            ->end();

        $node
            ->arrayNode('transition_colors')
                ->useAttributeAsKey('name')
                ->prototype('scalar')->end()
            ->end();
    }

    /**
     * @param NodeBuilder $node
     */
    private function addCallBackSection(NodeBuilder $node)
    {
        $callbacks = $node
            ->arrayNode('callbacks');

        $this->addSubCallbackSection($callbacks, 'guard');
        $this->addSubCallbackSection($callbacks, 'before');
        $this->addSubCallbackSection($callbacks, 'after');

        $callbacks->end()->end();
    }

    /**
     * @param ArrayNodeDefinition $callbacks
     * @param string      $type
     */
    protected function addSubCallbackSection(ArrayNodeDefinition $callbacks, $type)
    {
        $callbacks
            ->children()
                ->arrayNode($type)
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->variableNode('on')->end()
                            ->variableNode('do')->end()
                            ->scalarNode('priority')->defaultValue(0)->end()
                            ->arrayNode('args')->performNoDeepMerging()->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
