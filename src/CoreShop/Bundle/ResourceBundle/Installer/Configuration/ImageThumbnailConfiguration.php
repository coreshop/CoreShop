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

namespace CoreShop\Bundle\ResourceBundle\Installer\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class ImageThumbnailConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('thumbnails');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('thumbnails')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('name')->cannotBeEmpty()->end()
                            ->arrayNode('items')
                                ->arrayPrototype()
                                    ->children()
                                        ->scalarNode('method')->isRequired()->end()
                                        ->arrayNode('arguments')
                                            ->ignoreExtraKeys(false)
                                            ->children()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->scalarNode('description')->defaultValue('')->end()
                            ->scalarNode('group')->defaultValue('CoreShop')->end()
                            ->scalarNode('format')->cannotBeEmpty()->defaultValue('SOURCE')->end()
                            ->integerNode('quality')->defaultValue(90)->end()
                            ->floatNode('highResolution')->defaultValue(0.0)->end()
                            ->booleanNode('preserveColor')->defaultValue(false)->end()
                            ->booleanNode('preserveMetaData')->defaultValue(false)->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
