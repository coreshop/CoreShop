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
                        ->scalarNode('interface')->end()
                        ->scalarNode('parent_class')->end()
                        ->scalarNode('group')->end()
                        ->scalarNode('description')->end()
                        ->scalarNode('listing_parent_class')->end()
                        ->scalarNode('use_traits')->end()
                        ->scalarNode('listing_use_traits')->end()
                        ->arrayNode('fields')
                            ->useAttributeAsKey('field_name')
                            ->arrayPrototype()
                                ->children()
                                    ->scalarNode('after')->end()
                                    ->scalarNode('before')->end()
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
