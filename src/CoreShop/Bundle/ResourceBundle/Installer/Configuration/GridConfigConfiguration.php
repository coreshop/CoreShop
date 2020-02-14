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

namespace CoreShop\Bundle\ResourceBundle\Installer\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class GridConfigConfiguration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('grid_config');

        $rootNode
            ->children()
            ->arrayNode('grid_config')
            ->useAttributeAsKey('key')
            ->arrayPrototype()
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('key')->cannotBeEmpty()->end()
            ->scalarNode('name')->cannotBeEmpty()->end()
            ->scalarNode('class')->cannotBeEmpty()->end()
            ->variableNode('data')->cannotBeEmpty()->end()
            ->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
