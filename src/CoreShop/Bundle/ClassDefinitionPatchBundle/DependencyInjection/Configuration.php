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

namespace CoreShop\Bundle\ClassDefinitionPatchBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('core_shop_class_definition_patch');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
            ->arrayNode('patches')
            ->useAttributeAsKey('class_name')
                ->arrayPrototype()
                    ->children()
                        ->arrayNode('interface')
                            ->beforeNormalization()->
                                castToArray()->end()
                            ->scalarPrototype()->end()
                        ->end()
                        ->scalarNode('parent_class')->end()
                        ->scalarNode('group')->end()
                        ->scalarNode('description')->end()
                        ->scalarNode('listing_parent_class')->end()
                        ->arrayNode('use_traits')
                            ->beforeNormalization()->
                                castToArray()->end()
                            ->scalarPrototype()->end()
                        ->end()
                        ->arrayNode('listing_use_traits')
                            ->beforeNormalization()->
                                castToArray()->end()
                            ->scalarPrototype()->end()
                        ->end()
                        ->arrayNode('fields')
                            ->useAttributeAsKey('field_name')
                            ->arrayPrototype()
                                ->children()
                                    ->scalarNode('after')->end()
                                    ->scalarNode('before')->end()
                                    ->booleanNode('replace')->defaultTrue()->end()
                                    ->variableNode('definition')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
