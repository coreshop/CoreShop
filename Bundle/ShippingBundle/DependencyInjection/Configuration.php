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
use CoreShop\Bundle\ShippingBundle\Form\Type\CarrierType;
use CoreShop\Bundle\ShippingBundle\Form\Type\ShippingRuleGroupType;
use CoreShop\Bundle\ShippingBundle\Form\Type\ShippingRuleType;
use CoreShop\Component\Resource\Factory\Factory;
use CoreShop\Component\Shipping\Model\Carrier;
use CoreShop\Component\Shipping\Model\CarrierInterface;
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
        $this->addModelsSection($rootNode);

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
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Carrier::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(CarrierInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('is_pimcore_class')->defaultValue(false)->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(CarrierType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('shipping_rule')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(ShippingRule::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ShippingRuleInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->defaultValue(ShippingRuleController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('is_pimcore_class')->defaultValue(false)->cannotBeEmpty()->end()
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
                                        //->scalarNode('admin_controller')->defaultValue(ShippingRuleController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('is_pimcore_class')->defaultValue(false)->cannotBeEmpty()->end()
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
}
