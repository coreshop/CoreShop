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

namespace CoreShop\Bundle\ShippingBundle\DependencyInjection;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\ShippingBundle\Controller\ShippingRuleController;
use CoreShop\Bundle\ShippingBundle\Form\Type\CarrierTranslationType;
use CoreShop\Bundle\ShippingBundle\Form\Type\CarrierType;
use CoreShop\Bundle\ShippingBundle\Form\Type\ShippingRuleGroupType;
use CoreShop\Bundle\ShippingBundle\Form\Type\ShippingRuleType;
use CoreShop\Component\Resource\Factory\Factory;
use CoreShop\Component\Shipping\Model\Carrier;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\CarrierTranslation;
use CoreShop\Component\Shipping\Model\CarrierTranslationInterface;
use CoreShop\Component\Shipping\Model\ShippingRule;
use CoreShop\Component\Shipping\Model\ShippingRuleGroup;
use CoreShop\Component\Shipping\Model\ShippingRuleGroupInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleInterface;
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
        $rootNode = $treeBuilder->root('coreshop_shipping');

        $rootNode
            ->children()
                ->scalarNode('driver')->defaultValue(CoreShopResourceBundle::DRIVER_DOCTRINE_ORM)->end()
            ->end()
        ;

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('default_resolver')->defaultValue('coreshop.shipping.default_resolver.cheapest')->cannotBeEmpty()->end()
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
                        ->arrayNode('carrier')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('permission')->defaultValue('carrier')->cannotBeOverwritten()->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Carrier::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(CarrierInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(CarrierType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                                ->arrayNode('translation')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->variableNode('options')->end()
                                        ->arrayNode('classes')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->defaultValue(CarrierTranslation::class)->cannotBeEmpty()->end()
                                                ->scalarNode('interface')->defaultValue(CarrierTranslationInterface::class)->cannotBeEmpty()->end()
                                                ->scalarNode('repository')->cannotBeEmpty()->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->scalarNode('form')->defaultValue(CarrierTranslationType::class)->cannotBeEmpty()->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('shipping_rule')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('permission')->defaultValue('shipping_rule')->cannotBeOverwritten()->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(ShippingRule::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ShippingRuleInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->defaultValue(ShippingRuleController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(ShippingRuleType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('shipping_rule_group')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(ShippingRuleGroup::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ShippingRuleGroupInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(ShippingRuleGroupType::class)->cannotBeEmpty()->end()
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
                            ->scalarNode('resource')->defaultValue('/bundles/coreshopshipping/pimcore/js/resource.js')->end()
                            ->scalarNode('carrier_item')->defaultValue('/bundles/coreshopshipping/pimcore/js/carrier/item.js')->end()
                            ->scalarNode('carrier_panel')->defaultValue('/bundles/coreshopshipping/pimcore/js/carrier/panel.js')->end()
                            ->scalarNode('shipping_rule_item')->defaultValue('/bundles/coreshopshipping/pimcore/js/shippingrule/item.js')->end()
                            ->scalarNode('shipping_rule_panel')->defaultValue('/bundles/coreshopshipping/pimcore/js/shippingrule/panel.js')->end()
                            ->scalarNode('shipping_rule_action')->defaultValue('/bundles/coreshopshipping/pimcore/js/shippingrule/action.js')->end()
                            ->scalarNode('shipping_rule_condition')->defaultValue('/bundles/coreshopshipping/pimcore/js/shippingrule/condition.js')->end()
                            ->scalarNode('shipping_rule_actions_addition_amount')->defaultValue('/bundles/coreshopshipping/pimcore/js/shippingrule/actions/additionAmount.js')->end()
                            ->scalarNode('shipping_rule_actions_addition_percent')->defaultValue('/bundles/coreshopshipping/pimcore/js/shippingrule/actions/additionPercent.js')->end()
                            ->scalarNode('shipping_rule_actions_discount_amount')->defaultValue('/bundles/coreshopshipping/pimcore/js/shippingrule/actions/discountAmount.js')->end()
                            ->scalarNode('shipping_rule_actions_discount_percent')->defaultValue('/bundles/coreshopshipping/pimcore/js/shippingrule/actions/discountPercent.js')->end()
                            ->scalarNode('shipping_rule_actions_price')->defaultValue('/bundles/coreshopshipping/pimcore/js/shippingrule/actions/price.js')->end()
                            ->scalarNode('shipping_rule_actions_shipping_rule')->defaultValue('/bundles/coreshopshipping/pimcore/js/shippingrule/actions/shippingRule.js')->end()
                            ->scalarNode('shipping_rule_conditions_amount')->defaultValue('/bundles/coreshopshipping/pimcore/js/shippingrule/conditions/amount.js')->end()
                            ->scalarNode('shipping_rule_conditions_dimension')->defaultValue('/bundles/coreshopshipping/pimcore/js/shippingrule/conditions/dimension.js')->end()
                            ->scalarNode('shipping_rule_conditions_nested')->defaultValue('/bundles/coreshopshipping/pimcore/js/shippingrule/conditions/nested.js')->end()
                            ->scalarNode('shipping_rule_conditions_postcodes')->defaultValue('/bundles/coreshopshipping/pimcore/js/shippingrule/conditions/postcodes.js')->end()
                            ->scalarNode('shipping_rule_conditions_shippingRule')->defaultValue('/bundles/coreshopshipping/pimcore/js/shippingrule/conditions/shippingRule.js')->end()
                            ->scalarNode('shipping_rule_conditions_weight')->defaultValue('/bundles/coreshopshipping/pimcore/js/shippingrule/conditions/weight.js')->end()
                            ->scalarNode('core_extension_data_carrier')->defaultValue('/bundles/coreshopshipping/pimcore/js/coreExtension/data/coreShopCarrier.js')->end()
                            ->scalarNode('core_extension_tag_carrier')->defaultValue('/bundles/coreshopshipping/pimcore/js/coreExtension/tags/coreShopCarrier.js')->end()
                            ->scalarNode('core_extension_data_carrier_multiselect')->defaultValue('/bundles/coreshopshipping/pimcore/js/coreExtension/data/coreShopCarrierMultiselect.js')->end()
                            ->scalarNode('core_extension_tag_carrier_multiselect')->defaultValue('/bundles/coreshopshipping/pimcore/js/coreExtension/tags/coreShopCarrierMultiselect.js')->end()
                        ->end()
                    ->end()
                    ->arrayNode('css')
                        ->addDefaultsIfNotSet()
                        ->ignoreExtraKeys(false)
                        ->children()
                            ->scalarNode('shipping')->defaultValue('/bundles/coreshopshipping/pimcore/css/shipping.css')->end()
                        ->end()
                    ->end()
                    ->arrayNode('permissions')
                        ->scalarPrototype()
                            ->cannotBeOverwritten()
                            ->defaultValue(['carrier', 'shipping_rule'])
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }
}
