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

namespace CoreShop\Bundle\TaxationBundle\DependencyInjection;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\TaxationBundle\Controller\TaxRuleGroupController;
use CoreShop\Bundle\TaxationBundle\Doctrine\ORM\TaxRateRepository;
use CoreShop\Bundle\TaxationBundle\Doctrine\ORM\TaxRuleRepository;
use CoreShop\Bundle\TaxationBundle\Form\Type\TaxRateTranslationType;
use CoreShop\Bundle\TaxationBundle\Form\Type\TaxRateType;
use CoreShop\Bundle\TaxationBundle\Form\Type\TaxRuleGroupType;
use CoreShop\Bundle\TaxationBundle\Form\Type\TaxRuleType;
use CoreShop\Component\Resource\Factory\Factory;
use CoreShop\Component\Resource\Factory\PimcoreFactory;
use CoreShop\Component\Resource\Factory\TranslatableFactory;
use CoreShop\Component\Taxation\Model\TaxItemInterface;
use CoreShop\Component\Taxation\Model\TaxRate;
use CoreShop\Component\Taxation\Model\TaxRateInterface;
use CoreShop\Component\Taxation\Model\TaxRateTranslation;
use CoreShop\Component\Taxation\Model\TaxRateTranslationInterface;
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
        $rootNode = $treeBuilder->root('coreshop_taxation');

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
                        ->arrayNode('tax_rate')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('permission')->defaultValue('tax_rate')->cannotBeOverwritten()->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(TaxRate::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(TaxRateInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(TranslatableFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(TaxRateRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(TaxRateType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                                ->arrayNode('translation')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->variableNode('options')->end()
                                        ->arrayNode('classes')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->defaultValue(TaxRateTranslation::class)->cannotBeEmpty()->end()
                                                ->scalarNode('interface')->defaultValue(TaxRateTranslationInterface::class)->cannotBeEmpty()->end()
                                                ->scalarNode('repository')->cannotBeEmpty()->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->scalarNode('form')->defaultValue(TaxRateTranslationType::class)->cannotBeEmpty()->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('tax_rule_group')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('permission')->defaultValue('tax_rule_group')->cannotBeOverwritten()->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(TaxRuleGroup::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(TaxRuleGroupInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->defaultValue(TaxRuleGroupController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(TaxRuleGroupType::class)->cannotBeEmpty()->end()
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
                                        ->scalarNode('interface')->defaultValue(TaxRuleInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(TaxRuleRepository::class)->end()
                                        ->scalarNode('form')->defaultValue(TaxRuleType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('pimcore')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('tax_item')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\DataObject\Fieldcollection\Data\CoreShopTaxItem')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(TaxItemInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('install_file')->defaultValue('@CoreShopTaxationBundle/Resources/install/pimcore/fieldcollections/CoreShopTaxItem.json')->end()
                                        ->scalarNode('type')->defaultValue(CoreShopResourceBundle::PIMCORE_MODEL_TYPE_FIELD_COLLECTION)->cannotBeOverwritten(true)->end()
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
                            ->scalarNode('resource')->defaultValue('/bundles/coreshoptaxation/pimcore/js/resource.js')->end()
                            ->scalarNode('resource_tax_rate')->defaultValue('/bundles/coreshoptaxation/pimcore/js/resource/taxRate.js')->end()
                            ->scalarNode('resource_tax_rule_group')->defaultValue('/bundles/coreshoptaxation/pimcore/js/resource/taxRuleGroup.js')->end()
                            ->scalarNode('tax_item')->defaultValue('/bundles/coreshoptaxation/pimcore/js/tax/item.js')->end()
                            ->scalarNode('tax_panel')->defaultValue('/bundles/coreshoptaxation/pimcore/js/tax/panel.js')->end()
                            ->scalarNode('taxrulegroup_item')->defaultValue('/bundles/coreshoptaxation/pimcore/js/taxrulegroup/item.js')->end()
                            ->scalarNode('taxrulegroup_panel')->defaultValue('/bundles/coreshoptaxation/pimcore/js/taxrulegroup/panel.js')->end()
                            ->scalarNode('core_extension_data_tax_rule_group')->defaultValue('/bundles/coreshoptaxation/pimcore/js/coreExtension/data/coreShopTaxRuleGroup.js')->end()
                            ->scalarNode('core_extension_tag_tax_rule_group')->defaultValue('/bundles/coreshoptaxation/pimcore/js/coreExtension/tags/coreShopTaxRuleGroup.js')->end()
                        ->end()
                    ->end()
                    ->arrayNode('css')
                        ->addDefaultsIfNotSet()
                        ->ignoreExtraKeys(false)
                        ->children()
                            ->scalarNode('tax_item')->defaultValue('/bundles/coreshoptaxation/pimcore/css/taxation.css')->end()
                        ->end()
                    ->end()
                    ->scalarNode('permissions')
                        ->cannotBeOverwritten()
                        ->defaultValue(['tax_rate', 'tax_rule_group'])
                    ->end()
                ->end()
            ->end()
        ->end();
    }
}
