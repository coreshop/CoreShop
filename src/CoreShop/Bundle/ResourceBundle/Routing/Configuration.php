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
            ->end();

        return $treeBuilder;
    }
}
