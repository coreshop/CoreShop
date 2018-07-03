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

namespace CoreShop\Bundle\ProductBundle\DependencyInjection;

use CoreShop\Bundle\ProductBundle\Controller\ProductPriceRuleController;
use CoreShop\Bundle\ProductBundle\Doctrine\ORM\ProductPriceRuleRepository;
use CoreShop\Bundle\ProductBundle\Doctrine\ORM\ProductSpecificPriceRuleRepository;
use CoreShop\Bundle\ProductBundle\Form\Type\ProductPriceRuleType;
use CoreShop\Bundle\ProductBundle\Form\Type\ProductSpecificPriceRuleType;
use CoreShop\Bundle\ProductBundle\Pimcore\Repository\CategoryRepository;
use CoreShop\Bundle\ProductBundle\Pimcore\Repository\ProductRepository;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Component\Product\Model\CategoryInterface;
use CoreShop\Component\Product\Model\ManufacturerInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Product\Model\ProductPriceRule;
use CoreShop\Component\Product\Model\ProductPriceRuleInterface;
use CoreShop\Component\Product\Model\ProductSpecificPriceRule;
use CoreShop\Component\Product\Model\ProductSpecificPriceRuleInterface;
use CoreShop\Component\Resource\Factory\Factory;
use CoreShop\Component\Resource\Factory\PimcoreFactory;
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
        $rootNode = $treeBuilder->root('coreshop_product');

        $rootNode
            ->children()
                ->scalarNode('driver')->defaultValue(CoreShopResourceBundle::DRIVER_DOCTRINE_ORM)->end()
            ->end()
        ;

        $this->addModelsSection($rootNode);
        $this->addPimcoreResourcesSection($rootNode);
        $this->addStack($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addStack(ArrayNodeDefinition $node) {
        $node->children()
            ->arrayNode('stack')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('product')->defaultValue(ProductInterface::class)->cannotBeEmpty()->end()
                    ->scalarNode('category')->defaultValue(CategoryInterface::class)->cannotBeEmpty()->end()
                ->end()
            ->end()
        ->end();
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
                        ->arrayNode('product_price_rule')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('permission')->defaultValue('product_price_rule')->cannotBeOverwritten()->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(ProductPriceRule::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ProductPriceRuleInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->defaultValue(ProductPriceRuleController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(ProductPriceRuleRepository::class)->end()
                                        ->scalarNode('form')->defaultValue(ProductPriceRuleType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('product_specific_price_rule')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(ProductSpecificPriceRule::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ProductSpecificPriceRuleInterface::class)->cannotBeEmpty()->end()
                                        //->scalarNode('admin_controller')->defaultValue(ProductSpecificPriceRuleController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(ProductSpecificPriceRuleRepository::class)->end()
                                        ->scalarNode('form')->defaultValue(ProductSpecificPriceRuleType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('pimcore')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('product')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('path')->defaultValue('products')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\DataObject\CoreShopProduct')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ProductInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(ProductRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('install_file')->defaultValue('@CoreShopProductBundle/Resources/install/pimcore/classes/CoreShopProduct.json')->end()
                                        ->scalarNode('type')->defaultValue(CoreShopResourceBundle::PIMCORE_MODEL_TYPE_OBJECT)->cannotBeOverwritten(true)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('category')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('path')->defaultValue('categories')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\DataObject\CoreShopCategory')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(CategoryInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(CategoryRepository::class)->end()
                                        ->scalarNode('install_file')->defaultValue('@CoreShopProductBundle/Resources/install/pimcore/classes/CoreShopCategory.json')->end()
                                        ->scalarNode('type')->defaultValue(CoreShopResourceBundle::PIMCORE_MODEL_TYPE_OBJECT)->cannotBeOverwritten(true)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('manufacturer')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('path')->defaultValue('manufacturers')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\DataObject\CoreShopManufacturer')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ManufacturerInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('install_file')->defaultValue('@CoreShopProductBundle/Resources/install/pimcore/classes/CoreShopManufacturer.json')->end()
                                        ->scalarNode('type')->defaultValue(CoreShopResourceBundle::PIMCORE_MODEL_TYPE_OBJECT)->cannotBeOverwritten(true)->end()
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
                            ->scalarNode('resource')->defaultValue('/bundles/coreshopproduct/pimcore/js/resource.js')->end()
                            ->scalarNode('product_grid')->defaultValue('/bundles/coreshopproduct/pimcore/js/grid.js')->end()
                            ->scalarNode('product_price_rule_panel')->defaultValue('/bundles/coreshopproduct/pimcore/js/product/pricerule/panel.js')->end()
                            ->scalarNode('product_price_rule_item')->defaultValue('/bundles/coreshopproduct/pimcore/js/product/pricerule/item.js')->end()
                            ->scalarNode('product_price_rule_action')->defaultValue('/bundles/coreshopproduct/pimcore/js/product/pricerule/action.js')->end()
                            ->scalarNode('product_price_rule_condition')->defaultValue('/bundles/coreshopproduct/pimcore/js/product/pricerule/condition.js')->end()
                            ->scalarNode('product_price_rule_condition_nested')->defaultValue('/bundles/coreshopproduct/pimcore/js/product/pricerule/conditions/nested.js')->end()
                            ->scalarNode('product_price_rule_condition_products')->defaultValue('/bundles/coreshopproduct/pimcore/js/product/pricerule/conditions/products.js')->end()
                            ->scalarNode('product_price_rule_condition_timespan')->defaultValue('/bundles/coreshopproduct/pimcore/js/product/pricerule/conditions/timespan.js')->end()
                            ->scalarNode('product_price_rule_condition_weight')->defaultValue('/bundles/coreshopproduct/pimcore/js/product/pricerule/conditions/weight.js')->end()
                            ->scalarNode('product_price_rule_action_discount_amount')->defaultValue('/bundles/coreshopproduct/pimcore/js/product/pricerule/actions/discountAmount.js')->end()
                            ->scalarNode('product_price_rule_action_discount_percent')->defaultValue('/bundles/coreshopproduct/pimcore/js/product/pricerule/actions/discountPercent.js')->end()
                            ->scalarNode('product_price_rule_action_price')->defaultValue('/bundles/coreshopproduct/pimcore/js/product/pricerule/actions/price.js')->end()
                            ->scalarNode('product_price_rule_action_discount_price')->defaultValue('/bundles/coreshopproduct/pimcore/js/product/pricerule/actions/discountPrice.js')->end()
                            ->scalarNode('product_specific_price_rule_action')->defaultValue('/bundles/coreshopproduct/pimcore/js/specificprice/action.js')->end()
                            ->scalarNode('product_specific_price_rule_condition')->defaultValue('/bundles/coreshopproduct/pimcore/js/specificprice//condition.js')->end()
                            ->scalarNode('product_specific_price_action_price')->defaultValue('/bundles/coreshopproduct/pimcore/js/specificprice/actions/price.js')->end()
                            ->scalarNode('product_specific_price_action_discount_price')->defaultValue('/bundles/coreshopproduct/pimcore/js/specificprice/actions/discountPrice.js')->end()
                            ->scalarNode('product_specific_price_action_discount_amount')->defaultValue('/bundles/coreshopproduct/pimcore/js/specificprice/actions/discountAmount.js')->end()
                            ->scalarNode('product_specific_price_action_discount_percent')->defaultValue('/bundles/coreshopproduct/pimcore/js/specificprice/actions/discountPercent.js')->end()
                            ->scalarNode('product_specific_price_condition_nested')->defaultValue('/bundles/coreshopproduct/pimcore/js/specificprice/conditions/nested.js')->end()
                            ->scalarNode('product_specific_price_condition_timespan')->defaultValue('/bundles/coreshopproduct/pimcore/js/specificprice/conditions/timespan.js')->end()
                            ->scalarNode('product_specific_price_object_item')->defaultValue('/bundles/coreshopproduct/pimcore/js/specificprice/object/item.js')->end()
                            ->scalarNode('core_extension_data_specific_price')->defaultValue('/bundles/coreshopproduct/pimcore/js/coreExtension/data/coreShopProductSpecificPriceRules.js')->end()
                            ->scalarNode('core_extension_tag_specific_price')->defaultValue('/bundles/coreshopproduct/pimcore/js/coreExtension/tags/coreShopProductSpecificPriceRules.js')->end()
                        ->end()
                    ->end()
                    ->arrayNode('css')
                        ->addDefaultsIfNotSet()
                        ->ignoreExtraKeys(false)
                        ->children()
                            ->scalarNode('product')->defaultValue('/bundles/coreshopproduct/pimcore/css/product.css')->end()
                        ->end()
                    ->end()
                    ->arrayNode('permissions')
                        ->scalarPrototype()
                            ->cannotBeOverwritten()
                            ->defaultValue(['product_price_rule'])
                        ->end()
                    ->end()
                    ->arrayNode('install')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->arrayNode('admin_translations')
                                ->scalarPrototype()
                                    ->defaultValue(['@CoreShopProductBundle/Resources/install/pimcore/admin-translations.yml'])
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }
}
