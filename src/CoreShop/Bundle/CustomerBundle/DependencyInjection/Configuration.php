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

namespace CoreShop\Bundle\CustomerBundle\DependencyInjection;

use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Component\Customer\Model\CustomerGroupInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
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
        $rootNode = $treeBuilder->root('coreshop_customer');

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
                ->arrayNode('pimcore')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('customer')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('path')->defaultValue('customers')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\Object\CoreShopCustomer')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(CustomerInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->cannotBeEmpty()->end()
                                        ->scalarNode('install_file')->defaultValue('@CoreShopCustomerBundle/Resources/install/pimcore/classes/CoreShopCustomer.json')->end()
                                        ->scalarNode('type')->defaultValue(CoreShopResourceBundle::PIMCORE_MODEL_TYPE_OBJECT)->cannotBeOverwritten(true)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('customer_group')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('path')->defaultValue('customer_groups')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\Object\CoreShopCustomerGroup')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(CustomerGroupInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->cannotBeEmpty()->end()
                                        ->scalarNode('install_file')->defaultValue('@CoreShopCustomerBundle/Resources/install/pimcore/classes/CoreShopCustomerGroup.json')->end()
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
                        ->children()
                            ->scalarNode('resource')->defaultValue('/bundles/coreshopnotification/pimcore/js/resource.js')->end()
                        ->end()
                    ->end()
                    ->arrayNode('css')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('notification_rule')->defaultValue('/bundles/coreshopnotification/pimcore/css/notification.css')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }
}
