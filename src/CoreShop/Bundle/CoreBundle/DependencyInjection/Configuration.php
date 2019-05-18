<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Bundle\CoreBundle\DependencyInjection;

use CoreShop\Bundle\CoreBundle\Doctrine\ORM\ProductStorePriceRepository;
use CoreShop\Bundle\CoreBundle\Doctrine\ORM\ProductStoreValuesRepository;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Component\Core\Model\ProductStorePrice;
use CoreShop\Component\Core\Model\ProductStorePriceInterface;
use CoreShop\Component\Core\Model\ProductStoreValues;
use CoreShop\Component\Core\Model\ProductStoreValuesInterface;
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
        $rootNode = $treeBuilder->root('coreshop_core');

        $rootNode
            ->children()
                ->scalarNode('driver')->defaultValue(CoreShopResourceBundle::DRIVER_DOCTRINE_ORM)->end()
                ->scalarNode('send_usage_log')->defaultValue(true)->end()
                ->scalarNode('checkout_manager_factory')->cannotBeEmpty()->end()
                ->scalarNode('after_logout_redirect_route')->defaultValue('coreshop_index')->cannotBeEmpty()->end()
            ->end();
        $this->addModelsSection($rootNode);
        $this->addPimcoreResourcesSection($rootNode);
        $this->addCheckoutConfigurationSection($rootNode);

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
                        # deprecated
                        ->arrayNode('product_store_price')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(ProductStorePrice::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ProductStorePriceInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(ProductStorePriceRepository::class)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('product_store_values')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(ProductStoreValues::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ProductStoreValuesInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(ProductStoreValuesRepository::class)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
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
                        ->useAttributeAsKey('name')
                        ->prototype('scalar')->end()
                    ->end()
                    ->arrayNode('css')
                        ->addDefaultsIfNotSet()
                        ->ignoreExtraKeys(false)
                        ->children()
                            ->scalarNode('core')->defaultValue('/bundles/coreshopcore/pimcore/css/core.css')->end()
                        ->end()
                    ->end()
                    ->arrayNode('editmode_js')
                        ->useAttributeAsKey('name')
                        ->prototype('scalar')->end()
                    ->end()
                    ->arrayNode('editmode_css')
                        ->useAttributeAsKey('name')
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addCheckoutConfigurationSection(ArrayNodeDefinition $node)
    {
        $node->children()
            ->arrayNode('checkout')
                ->isRequired()
                ->useAttributeAsKey('name')
                ->requiresAtLeastOneElement()
                ->arrayPrototype()
                    ->children()
                        ->arrayNode('steps')
                        ->useAttributeAsKey('identifier')
                            ->arrayPrototype()
                                ->canBeUnset(true)
                                ->children()
                                    ->scalarNode('step')->isRequired()->end()
                                    ->integerNode('priority')->isRequired()->end()
                                ->end()
                            ->end()
                            ->validate()
                                ->ifTrue(function ($array) {
                                    $notValid = false;
                                    foreach ($array as $key => $value) {
                                        if ($key === 'cart') {
                                            $notValid = true;

                                            break;
                                        }
                                    }

                                    return $notValid;
                                })
                                ->thenInvalid('"cart" is a coreshop reserved checkout step. please use another name.')
                            ->end()
                        ->end()

                    ->end()
                ->end()
            ->end()
        ->end();
    }
}
