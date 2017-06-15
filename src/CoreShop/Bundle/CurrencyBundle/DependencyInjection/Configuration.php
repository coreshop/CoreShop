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

namespace CoreShop\Bundle\CurrencyBundle\DependencyInjection;

use CoreShop\Bundle\CurrencyBundle\Doctrine\ORM\CurrencyRepository;
use CoreShop\Bundle\CurrencyBundle\Form\Type\CurrencyType;
use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Component\Currency\Model\Currency;
use CoreShop\Component\Currency\Model\CurrencyInterface;
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
        $rootNode = $treeBuilder->root('coreshop_currency');

        $rootNode
            ->children()
                ->scalarNode('driver')->defaultValue(CoreShopResourceBundle::DRIVER_DOCTRINE_ORM)->end()
            ->end()
        ;
        $this->addModelsSection($rootNode);
        $this->addPimcoreJsSection($rootNode);

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
                        ->arrayNode('currency')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Currency::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(CurrencyInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(CurrencyRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(CurrencyType::class)->cannotBeEmpty()->end()
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
    private function addPimcoreJsSection(ArrayNodeDefinition $node)
    {
        $node->children()
            ->arrayNode('pimcore_admin')
                ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('js')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('currency_item')->defaultValue('/bundles/coreshopcurrency/pimcore/js/item.js')->end()
                            ->scalarNode('currency_panel')->defaultValue('/bundles/coreshopcurrency/pimcore/js/panel.js')->end()
                            ->scalarNode('core_extension_data_currency')->defaultValue('/bundles/coreshopcurrency/pimcore/js/coreExtension/data/coreShopCurrency.js')->end()
                            ->scalarNode('core_extension_tag_currency')->defaultValue('/bundles/coreshopcurrency/pimcore/js/coreExtension/tags/coreShopCurrency.js')->end()
                            ->scalarNode('core_extension_data_currency_multiselect')->defaultValue('/bundles/coreshopcurrency/pimcore/js/coreExtension/data/coreShopCurrencyMultiselect.js')->end()
                            ->scalarNode('core_extension_tag_currency_multiselect')->defaultValue('/bundles/coreshopcurrency/pimcore/js/coreExtension/tags/coreShopCurrencyMultiselect.js')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }
}
