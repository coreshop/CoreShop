<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\TaxationBundle\DependencyInjection;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Component\Core\Factory\Factory;
use CoreShop\Component\Core\Factory\ListingFactory;
use CoreShop\Component\Core\Repository\Repository;
use CoreShop\Component\Taxation\Model\TaxRate;
use CoreShop\Component\Taxation\Model\TaxRateInterface;
use CoreShop\Component\Taxation\Model\TaxRule;
use CoreShop\Component\Taxation\Model\TaxRuleGroup;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;
use CoreShop\Component\Taxation\Model\TaxRuleInterface;
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
                ->arrayNode('models')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('tax_rate')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(TaxRate::class)->cannotBeEmpty()->end()
                                        ->scalarNode('listing')->defaultValue(TaxRate\Listing::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(TaxRateInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('list_factory')->defaultValue(ListingFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(Repository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('is_pimcore_class')->defaultValue(false)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('tax_rule')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(TaxRule::class)->cannotBeEmpty()->end()
                                        ->scalarNode('listing')->defaultValue(TaxRule\Listing::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(TaxRuleInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('list_factory')->defaultValue(ListingFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(Repository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('is_pimcore_class')->defaultValue(false)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('tax_rule_group')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(TaxRuleGroup::class)->cannotBeEmpty()->end()
                                        ->scalarNode('listing')->defaultValue(TaxRuleGroup\Listing::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(TaxRuleGroupInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('list_factory')->defaultValue(ListingFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(Repository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('is_pimcore_class')->defaultValue(false)->cannotBeEmpty()->end()
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
