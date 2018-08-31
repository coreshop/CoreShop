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

namespace CoreShop\Bundle\PimcoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
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
        $rootNode = $treeBuilder->root('coreshop_pimcore');

        $this->addPimcoreResourcesSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addPimcoreResourcesSection(ArrayNodeDefinition $node)
    {
        $node->children()
            ->arrayNode('pimcore_admin')
                ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('js')
                        ->addDefaultsIfNotSet()
                        ->ignoreExtraKeys(false)
                        ->children()
                            ->scalarNode('broker')->defaultValue('/bundles/coreshoppimcore/pimcore/js/broker.js')->end()
                            ->scalarNode('clear_button')->defaultValue('/bundles/coreshoppimcore/pimcore/js/ext/ClearButton.js')->end()
                            ->scalarNode('core_extension_tag_serializedData')->defaultValue('/bundles/coreshoppimcore/pimcore/js/coreExtension/tags/coreShopSerializedData.js')->end()
                            ->scalarNode('core_extension_data_serializedData')->defaultValue('/bundles/coreshoppimcore/pimcore/js/coreExtension/data/coreShopSerializedData.js')->end()
                            ->scalarNode('core_extension_data_embeddedClass')->defaultValue('/bundles/coreshoppimcore/pimcore/js/coreExtension/data/coreShopEmbeddedClass.js')->end()
                            ->scalarNode('core_extension_tag_embeddedClass')->defaultValue('/bundles/coreshoppimcore/pimcore/js/coreExtension/tags/coreShopEmbeddedClass.js')->end()
                            ->scalarNode('core_extension_embeddedClass_container')->defaultValue('/bundles/coreshoppimcore/pimcore/js/coreExtension/embeddedClassContainer.js')->end()
                            ->scalarNode('core_extension_embeddedClass_item_container')->defaultValue('/bundles/coreshoppimcore/pimcore/js/coreExtension/embeddedClassItemContainer.js')->end()
                            ->scalarNode('core_extension_data_dynamic_dropdown')->defaultValue('/bundles/coreshoppimcore/pimcore/js/coreExtension/data/coreShopDynamicDropdown.js')->end()
                            ->scalarNode('core_extension_data_dynamic_dropdown_multiple')->defaultValue('/bundles/coreshoppimcore/pimcore/js/coreExtension/data/coreShopDynamicDropdownMultiple.js')->end()
                            ->scalarNode('core_extension_data_itemselector')->defaultValue('/bundles/coreshoppimcore/pimcore/js/coreExtension/data/coreShopItemSelector.js')->end()
                            ->scalarNode('core_extension_data_superboxselect')->defaultValue('/bundles/coreshoppimcore/pimcore/js/coreExtension/data/coreShopSuperBoxSelect.js')->end()
                            ->scalarNode('core_extension_tag_dynamic_dropdown')->defaultValue('/bundles/coreshoppimcore/pimcore/js/coreExtension/tags/coreShopDynamicDropdown.js')->end()
                            ->scalarNode('core_extension_tag_dynamic_dropdown_multiple')->defaultValue('/bundles/coreshoppimcore/pimcore/js/coreExtension/tags/coreShopDynamicDropdownMultiple.js')->end()
                            ->scalarNode('core_extension_tag_itemselector')->defaultValue('/bundles/coreshoppimcore/pimcore/js/coreExtension/tags/coreShopItemSelector.js')->end()
                            ->scalarNode('core_extension_tag_superboxselect')->defaultValue('/bundles/coreshoppimcore/pimcore/js/coreExtension/tags/coreShopSuperBoxSelect.js')->end()
                            ->scalarNode('grid_plugin')->defaultValue('/bundles/coreshoppimcore/pimcore/js/plugin/grid.js')->end()
                        ->end()
                    ->end()
                    ->arrayNode('css')
                        ->addDefaultsIfNotSet()
                        ->ignoreExtraKeys(false)
                        ->children()
                            ->scalarNode('resource')->defaultValue('/bundles/coreshoppimcore/pimcore/css/pimcore.css')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }
}
