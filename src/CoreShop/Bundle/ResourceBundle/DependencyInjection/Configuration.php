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

namespace CoreShop\Bundle\ResourceBundle\DependencyInjection;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Component\Resource\Factory\Factory;
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
        $rootNode = $treeBuilder->root('coreshop_resource');

        $this->addResourcesSection($rootNode);
        $this->addTranslationsSection($rootNode);
        $this->addDriversSection($rootNode);
        $this->addPimcoreResourcesSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addResourcesSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('resources')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('driver')->defaultValue(CoreShopResourceBundle::DRIVER_DOCTRINE_ORM)->end()
                            ->variableNode('options')->end()
                            ->scalarNode('templates')->cannotBeEmpty()->end()
                            ->arrayNode('classes')
                                ->isRequired()
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('model')->isRequired()->cannotBeEmpty()->end()
                                    ->scalarNode('interface')->cannotBeEmpty()->end()
                                    ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                    ->scalarNode('repository')->cannotBeEmpty()->end()
                                    ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                ->end()
                            ->end()
                            ->arrayNode('translation')
                                ->children()
                                    ->variableNode('options')->end()
                                    ->arrayNode('classes')
                                        ->isRequired()
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->scalarNode('model')->isRequired()->cannotBeEmpty()->end()
                                            ->scalarNode('interface')->cannotBeEmpty()->end()
                                            ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                            ->scalarNode('repository')->cannotBeEmpty()->end()
                                            ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addTranslationsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('translation')
                    ->canBeDisabled()
                    ->children()
                        ->scalarNode('locale_provider')->defaultValue('coreshop.translation_locale_provider.pimcore')->cannotBeEmpty()->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addDriversSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('drivers')
                    ->defaultValue([CoreShopResourceBundle::DRIVER_DOCTRINE_ORM])
                    ->prototype('enum')->values(CoreShopResourceBundle::getAvailableDrivers())->end()
                ->end()
            ->end()
        ;
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
                            ->scalarNode('global')->defaultValue('/bundles/coreshopresource/pimcore/js/global.js')->end()
                            ->scalarNode('plugin')->defaultValue('/bundles/coreshopresource/pimcore/js/plugin.js')->end()
                            ->scalarNode('event_manager')->defaultValue('/bundles/coreshopresource/pimcore/js/eventManager.js')->end()
                            ->scalarNode('resource')->defaultValue('/bundles/coreshopresource/pimcore/js/resource.js')->end()
                            ->scalarNode('resource_panel')->defaultValue('/bundles/coreshopresource/pimcore/js/resource/panel.js')->end()
                            ->scalarNode('resource_item')->defaultValue('/bundles/coreshopresource/pimcore/js/resource/item.js')->end()
                            ->scalarNode('resource_combo')->defaultValue('/bundles/coreshopresource/pimcore/js/resource/comboBox.js')->end()
                            ->scalarNode('object_element_href')->defaultValue('/bundles/coreshopresource/pimcore/js/object/elementHref.js')->end()
                            ->scalarNode('object_object_multihref')->defaultValue('/bundles/coreshopresource/pimcore/js/object/objectMultihref.js')->end()
                            ->scalarNode('core_extension_data_data')->defaultValue('/bundles/coreshopresource/pimcore/js/coreExtension/data/data.js')->end()
                            ->scalarNode('core_extension_data_data_multiselect')->defaultValue('/bundles/coreshopresource/pimcore/js/coreExtension/data/dataMultiselect.js')->end()
                            ->scalarNode('core_extension_data_select')->defaultValue('/bundles/coreshopresource/pimcore/js/coreExtension/data/select.js')->end()
                            ->scalarNode('core_extension_tag_select')->defaultValue('/bundles/coreshopresource/pimcore/js/coreExtension/tags/select.js')->end()
                            ->scalarNode('core_extension_tag_multiselect')->defaultValue('/bundles/coreshopresource/pimcore/js/coreExtension/tags/multiselect.js')->end()
                        ->end()
                    ->end()
                    ->arrayNode('css')
                        ->addDefaultsIfNotSet()
                        ->ignoreExtraKeys(false)
                        ->children()
                            ->scalarNode('resource')->defaultValue('/bundles/coreshopresource/pimcore/css/resource.css')->end()
                        ->end()
                    ->end()
                    ->arrayNode('editmode_js')
                        ->addDefaultsIfNotSet()
                        ->ignoreExtraKeys(false)
                        ->children()
                            ->scalarNode('core_extension_document:tag_select')->defaultValue('/bundles/coreshopresource/pimcore/js/coreExtension/document/select.js')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }
}
