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

namespace CoreShop\Bundle\IndexBundle\DependencyInjection;

use CoreShop\Bundle\IndexBundle\Controller\FilterController;
use CoreShop\Bundle\IndexBundle\Controller\IndexController;
use CoreShop\Bundle\IndexBundle\Form\Type\FilterConditionType;
use CoreShop\Bundle\IndexBundle\Form\Type\FilterType;
use CoreShop\Bundle\IndexBundle\Form\Type\IndexColumnType;
use CoreShop\Bundle\IndexBundle\Form\Type\IndexType;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Component\Index\Model\Filter;
use CoreShop\Component\Index\Model\FilterCondition;
use CoreShop\Component\Index\Model\FilterConditionInterface;
use CoreShop\Component\Index\Model\FilterInterface;
use CoreShop\Component\Index\Model\Index;
use CoreShop\Component\Index\Model\IndexColumn;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Index\Model\IndexInterface;
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
        $rootNode = $treeBuilder->root('coreshop_index');

        $rootNode
            ->children()
                ->scalarNode('driver')->defaultValue(CoreShopResourceBundle::DRIVER_DOCTRINE_ORM)->end()
            ->end()
        ;
        $this->addModelsSection($rootNode);
        $this->addPimcoreResourcesSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addModelsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('index')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('permission')->defaultValue('index')->cannotBeOverwritten()->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Index::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(IndexInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->defaultValue(IndexController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(IndexType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('index_column')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(IndexColumn::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(IndexColumnInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(IndexColumnType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('filter')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('permission')->defaultValue('filter')->cannotBeOverwritten()->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Filter::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(FilterInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->defaultValue(FilterController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(FilterType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('filter_condition')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(FilterCondition::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(FilterConditionInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(FilterConditionType::class)->cannotBeEmpty()->end()
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
                            ->scalarNode('resource')->defaultValue('/bundles/coreshopindex/pimcore/js/resource.js')->end()
                            ->scalarNode('index_item')->defaultValue('/bundles/coreshopindex/pimcore/js/index/item.js')->end()
                            ->scalarNode('index_panel')->defaultValue('/bundles/coreshopindex/pimcore/js/index/panel.js')->end()
                            ->scalarNode('index_fields')->defaultValue('/bundles/coreshopindex/pimcore/js/index/fields.js')->end()
                            ->scalarNode('index_getter_abstract')->defaultValue('/bundles/coreshopindex/pimcore/js/index/getters/abstract.js')->end()
                            ->scalarNode('index_getter_brick')->defaultValue('/bundles/coreshopindex/pimcore/js/index/getters/brick.js')->end()
                            ->scalarNode('index_getter_classificationstore')->defaultValue('/bundles/coreshopindex/pimcore/js/index/getters/classificationstore.js')->end()
                            ->scalarNode('index_getter_fieldcollection')->defaultValue('/bundles/coreshopindex/pimcore/js/index/getters/fieldcollection.js')->end()
                            ->scalarNode('index_interpreter_abstract')->defaultValue('/bundles/coreshopindex/pimcore/js/index/interpreters/abstract.js')->end()
                            ->scalarNode('index_interpreter_empty')->defaultValue('/bundles/coreshopindex/pimcore/js/index/interpreters/empty.js')->end()
                            ->scalarNode('index_interpreter_nestedcontainer')->defaultValue('/bundles/coreshopindex/pimcore/js/index/interpreters/nestedcontainer.js')->end()
                            ->scalarNode('index_interpreter_nested')->defaultValue('/bundles/coreshopindex/pimcore/js/index/interpreters/nested.js')->end()
                            ->scalarNode('index_interpreter_nested_localized')->defaultValue('/bundles/coreshopindex/pimcore/js/index/interpreters/nestedlocalized.js')->end()
                            ->scalarNode('index_interpreter_nested_relational')->defaultValue('/bundles/coreshopindex/pimcore/js/index/interpreters/nestedrelational.js')->end()
                            ->scalarNode('index_interpreter_objectproperty')->defaultValue('/bundles/coreshopindex/pimcore/js/index/interpreters/objectproperty.js')->end()
                            ->scalarNode('index_interpreter_expression')->defaultValue('/bundles/coreshopindex/pimcore/js/index/interpreters/expression.js')->end()
                            ->scalarNode('index_interpreter_objecttype')->defaultValue('/bundles/coreshopindex/pimcore/js/index/objecttype/abstract.js')->end()
                            ->scalarNode('index_worker_abstract')->defaultValue('/bundles/coreshopindex/pimcore/js/index/worker/abstract.js')->end()
                            ->scalarNode('index_worker_mysql')->defaultValue('/bundles/coreshopindex/pimcore/js/index/worker/mysql.js')->end()
                            ->scalarNode('filter_item')->defaultValue('/bundles/coreshopindex/pimcore/js/filter/item.js')->end()
                            ->scalarNode('filter_panel')->defaultValue('/bundles/coreshopindex/pimcore/js/filter/panel.js')->end()
                            ->scalarNode('filter_abstract')->defaultValue('/bundles/coreshopindex/pimcore/js/filter/abstract.js')->end()
                            ->scalarNode('filter_condition')->defaultValue('/bundles/coreshopindex/pimcore/js/filter/condition.js')->end()
                            ->scalarNode('filter_similarity')->defaultValue('/bundles/coreshopindex/pimcore/js/filter/similarity.js')->end()
                            ->scalarNode('filter_condition_abstract')->defaultValue('/bundles/coreshopindex/pimcore/js/filter/conditions/abstract.js')->end()
                            ->scalarNode('filter_condition_nested')->defaultValue('/bundles/coreshopindex/pimcore/js/filter/conditions/nested.js')->end()
                            ->scalarNode('filter_condition_multiselect')->defaultValue('/bundles/coreshopindex/pimcore/js/filter/conditions/multiselect.js')->end()
                            ->scalarNode('filter_condition_range')->defaultValue('/bundles/coreshopindex/pimcore/js/filter/conditions/range.js')->end()
                            ->scalarNode('filter_condition_select')->defaultValue('/bundles/coreshopindex/pimcore/js/filter/conditions/select.js')->end()
                            ->scalarNode('filter_condition_relational_multiselect')->defaultValue('/bundles/coreshopindex/pimcore/js/filter/conditions/relational_multiselect.js')->end()
                            ->scalarNode('filter_condition_category_select')->defaultValue('/bundles/coreshopindex/pimcore/js/filter/conditions/category_select.js')->end()
                            ->scalarNode('filter_similarity_abstract')->defaultValue('/bundles/coreshopindex/pimcore/js/filter/similarities/abstract.js')->end()
                            ->scalarNode('filter_similarity_field')->defaultValue('/bundles/coreshopindex/pimcore/js/filter/similarities/field.js')->end()
                            ->scalarNode('core_extension_data_filter')->defaultValue('/bundles/coreshopindex/pimcore/js/coreExtension/data/coreShopFilter.js')->end()
                            ->scalarNode('core_extension_tag_filter')->defaultValue('/bundles/coreshopindex/pimcore/js/coreExtension/tags/coreShopFilter.js')->end()
                        ->end()
                    ->end()
                    ->arrayNode('css')
                        ->addDefaultsIfNotSet()
                        ->ignoreExtraKeys(false)
                        ->children()
                            ->scalarNode('index')->defaultValue('/bundles/coreshopindex/pimcore/css/index.css')->end()
                        ->end()
                    ->end()
                    ->scalarNode('permissions')
                        ->cannotBeOverwritten()
                        ->defaultValue(['index', 'filter'])
                    ->end()
                ->end()
            ->end()
        ->end();
    }
}
