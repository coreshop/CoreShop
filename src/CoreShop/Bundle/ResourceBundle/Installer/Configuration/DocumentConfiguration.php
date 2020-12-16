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

namespace CoreShop\Bundle\ResourceBundle\Installer\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class DocumentConfiguration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('documents');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('documents')
                    ->arrayPrototype()
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('key')->cannotBeEmpty()->end()
                            ->scalarNode('type')->cannotBeEmpty()->end()
                            ->scalarNode('path')->end()
                            ->scalarNode('module')->end()
                            ->scalarNode('controller')->end()
                            ->scalarNode('action')->end()
                            ->arrayNode('content')
                                ->useAttributeAsKey('language')
                                ->arrayPrototype()
                                    ->arrayPrototype()
                                        ->children()
                                            ->scalarNode('type')->isRequired()->end()
                                            ->scalarNode('value')->isRequired()->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
