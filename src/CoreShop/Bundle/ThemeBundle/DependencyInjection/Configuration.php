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

namespace CoreShop\Bundle\ThemeBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('core_shop_theme');

        $rootNode
            ->children()
                ->arrayNode('default_resolvers')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('pimcore_site')->defaultFalse()->end()
                        ->booleanNode('pimcore_document_property')->defaultFalse()->end()
                    ->end()
                ->end()
                ->arrayNode('inheritance')
                    ->normalizeKeys(false)
                    ->useAttributeAsKey('theme_name')
                    ->beforeNormalization()
                        ->always()
                            ->then(function ($config) {
                                if (!\is_array($config)) {
                                    return [];
                                }
                                // If XML config with only one routing attribute
                                if (2 === \count($config) && isset($config['theme_name']) && isset($config['parent_themes'])) {
                                    $config = [0 => $config];
                                }

                                $newConfig = [];
                                foreach ($config as $k => $v) {
                                    if (!\is_int($k)) {
                                        $newConfig[$k] = [
                                            'parent_themes' => $v['parent_themes'] ?? (\is_array($v) ? array_values($v) : [$v]),
                                        ];
                                    } else {
                                        $newConfig[$v['theme_name']]['parent_themes'] = array_map(
                                            function ($a) {
                                                return \is_string($a) ? $a : $a['service'];
                                            },
                                            array_values($v['parent_themes'])
                                        );
                                    }
                                }

                                return $newConfig;
                            })
                        ->end()
                        ->prototype('array')
                            ->performNoDeepMerging()
                            ->children()
                                ->arrayNode('parent_themes')
                                    ->requiresAtLeastOneElement()
                                    ->prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
