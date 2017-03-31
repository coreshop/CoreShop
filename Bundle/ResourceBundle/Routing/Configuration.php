<?php

namespace CoreShop\Bundle\ResourceBundle\Routing;

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
        $rootNode = $treeBuilder->root('routing');

        $rootNode
            ->children()
                ->scalarNode('alias')->cannotBeEmpty()->end()
                ->scalarNode('path')->cannotBeEmpty()->end()
                ->scalarNode('identifier')->defaultValue('id')->end()
                ->arrayNode('only')
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('no_default_routes')->defaultFalse()->end()
                ->arrayNode('additional_routes')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('path')->end()
                            ->scalarNode('action')->end()
                            ->arrayNode('methods')
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
