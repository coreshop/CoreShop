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

namespace CoreShop\Bundle\ResourceBundle\Installer\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class RouteConfiguration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('staticroutes');

        $rootNode
            ->children()
            ->arrayNode('routes')
            ->useAttributeAsKey('name')
            ->arrayPrototype()
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('name')->cannotBeEmpty()->end()
            ->scalarNode('pattern')->cannotBeEmpty()->end()
            ->scalarNode('reverse')->cannotBeEmpty()->end()
            ->scalarNode('module')->cannotBeEmpty()->end()
            ->scalarNode('controller')->cannotBeEmpty()->end()
            ->scalarNode('action')->cannotBeEmpty()->end()
            ->scalarNode('variables')->defaultValue('')->end()
            ->integerNode('priority')->defaultValue(1)->end()
            ->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
