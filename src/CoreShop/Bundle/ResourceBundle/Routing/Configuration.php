<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

declare(strict_types=1);

namespace CoreShop\Bundle\ResourceBundle\Routing;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('routing');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('alias')->cannotBeEmpty()->end()
                ->booleanNode('expose')->defaultTrue()->end()
                ->scalarNode('path')->cannotBeEmpty()->end()
                ->scalarNode('identifier')->defaultValue('id')->end()
                ->arrayNode('only')
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('no_default_routes')->defaultFalse()->end()
                ->arrayNode('additional_routes')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('path')->end()
                            ->scalarNode('action')->end()
                            ->arrayNode('options')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->booleanNode('expose')->defaultTrue()->end()
                                ->end()
                            ->end()
                            ->arrayNode('methods')
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
