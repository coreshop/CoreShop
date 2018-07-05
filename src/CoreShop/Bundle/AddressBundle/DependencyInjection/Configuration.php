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

namespace CoreShop\Bundle\AddressBundle\DependencyInjection;

use CoreShop\Bundle\AddressBundle\Doctrine\ORM\CountryRepository;
use CoreShop\Bundle\AddressBundle\Form\Type\CountryTranslationType;
use CoreShop\Bundle\AddressBundle\Form\Type\CountryType;
use CoreShop\Bundle\AddressBundle\Form\Type\StateTranslationType;
use CoreShop\Bundle\AddressBundle\Form\Type\StateType;
use CoreShop\Bundle\AddressBundle\Form\Type\ZoneType;
use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Address\Model\Country;
use CoreShop\Component\Address\Model\CountryInterface;
use CoreShop\Component\Address\Model\CountryTranslation;
use CoreShop\Component\Address\Model\CountryTranslationInterface;
use CoreShop\Component\Address\Model\State;
use CoreShop\Component\Address\Model\StateInterface;
use CoreShop\Component\Address\Model\StateTranslation;
use CoreShop\Component\Address\Model\StateTranslationInterface;
use CoreShop\Component\Address\Model\Zone;
use CoreShop\Component\Address\Model\ZoneInterface;
use CoreShop\Component\Resource\Factory\Factory;
use CoreShop\Component\Resource\Factory\PimcoreFactory;
use CoreShop\Component\Resource\Factory\TranslatableFactory;
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
        $rootNode = $treeBuilder->root('coreshop_address');

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
                        ->arrayNode('country')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('permission')->defaultValue('country')->cannotBeOverwritten()->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Country::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(CountryInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(TranslatableFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(CountryRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(CountryType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                                ->arrayNode('translation')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->variableNode('options')->end()
                                        ->arrayNode('classes')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->defaultValue(CountryTranslation::class)->cannotBeEmpty()->end()
                                                ->scalarNode('interface')->defaultValue(CountryTranslationInterface::class)->cannotBeEmpty()->end()
                                                ->scalarNode('repository')->cannotBeEmpty()->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->scalarNode('form')->defaultValue(CountryTranslationType::class)->cannotBeEmpty()->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('zone')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('permission')->defaultValue('zone')->cannotBeOverwritten()->end()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Zone::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ZoneInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(ZoneType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('state')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('permission')->defaultValue('state')->cannotBeOverwritten()->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(State::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(StateInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(TranslatableFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(StateType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                                ->arrayNode('translation')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->variableNode('options')->end()
                                        ->arrayNode('classes')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->defaultValue(StateTranslation::class)->cannotBeEmpty()->end()
                                                ->scalarNode('interface')->defaultValue(StateTranslationInterface::class)->cannotBeEmpty()->end()
                                                ->scalarNode('repository')->cannotBeEmpty()->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->scalarNode('form')->defaultValue(StateTranslationType::class)->cannotBeEmpty()->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('pimcore')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('address')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('path')->defaultValue('addresses')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\DataObject\CoreShopAddress')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(AddressInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('install_file')->defaultValue('@CoreShopAddressBundle/Resources/install/pimcore/classes/CoreShopAddress.json')->end()
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
                            ->scalarNode('resource')->defaultValue('/bundles/coreshopaddress/pimcore/js/resource.js')->end()
                            ->scalarNode('resource_country')->defaultValue('/bundles/coreshopaddress/pimcore/js/resource/country.js')->end()
                            ->scalarNode('resource_state')->defaultValue('/bundles/coreshopaddress/pimcore/js/resource/state.js')->end()
                            ->scalarNode('resource_zone')->defaultValue('/bundles/coreshopaddress/pimcore/js/resource/zone.js')->end()
                            ->scalarNode('country_item')->defaultValue('/bundles/coreshopaddress/pimcore/js/country/item.js')->end()
                            ->scalarNode('country_panel')->defaultValue('/bundles/coreshopaddress/pimcore/js/country/panel.js')->end()
                            ->scalarNode('state_item')->defaultValue('/bundles/coreshopaddress/pimcore/js/state/item.js')->end()
                            ->scalarNode('state_panel')->defaultValue('/bundles/coreshopaddress/pimcore/js/state/panel.js')->end()
                            ->scalarNode('zone_item')->defaultValue('/bundles/coreshopaddress/pimcore/js/zone/item.js')->end()
                            ->scalarNode('zone_panel')->defaultValue('/bundles/coreshopaddress/pimcore/js/zone/panel.js')->end()
                            ->scalarNode('core_extension_data_country')->defaultValue('/bundles/coreshopaddress/pimcore/js/coreExtension/data/coreShopCountry.js')->end()
                            ->scalarNode('core_extension_tag_country')->defaultValue('/bundles/coreshopaddress/pimcore/js/coreExtension/tags/coreShopCountry.js')->end()
                            ->scalarNode('core_extension_data_country_multiselect')->defaultValue('/bundles/coreshopaddress/pimcore/js/coreExtension/data/coreShopCountryMultiselect.js')->end()
                            ->scalarNode('core_extension_tag_country_multiselect')->defaultValue('/bundles/coreshopaddress/pimcore/js/coreExtension/tags/coreShopCountryMultiselect.js')->end()
                            ->scalarNode('core_extension_data_state')->defaultValue('/bundles/coreshopaddress/pimcore/js/coreExtension/data/coreShopState.js')->end()
                            ->scalarNode('core_extension_tag_state')->defaultValue('/bundles/coreshopaddress/pimcore/js/coreExtension/tags/coreShopState.js')->end()
                        ->end()
                    ->end()
                    ->arrayNode('css')
                        ->addDefaultsIfNotSet()
                        ->ignoreExtraKeys(false)
                        ->children()
                            ->scalarNode('address')->defaultValue('/bundles/coreshopaddress/pimcore/css/address.css')->end()
                        ->end()
                    ->end()
                    ->arrayNode('editmode_js')
                        ->addDefaultsIfNotSet()
                        ->ignoreExtraKeys(false)
                        ->children()
                            ->scalarNode('core_extension_document_tag_country')->defaultValue('/bundles/coreshopaddress/pimcore/js/coreExtension/document/coreShopCountry.js')->end()
                            ->scalarNode('core_extension_document_tag_state')->defaultValue('/bundles/coreshopaddress/pimcore/js/coreExtension/document/coreShopState.js')->end()
                            ->scalarNode('core_extension_document_tag_zone')->defaultValue('/bundles/coreshopaddress/pimcore/js/coreExtension/document/coreShopZone.js')->end()
                        ->end()
                    ->end()
                    ->scalarNode('permissions')
                        ->cannotBeOverwritten()
                        ->defaultValue(['country', 'state', 'zone'])
                    ->end()
                    ->arrayNode('install')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->arrayNode('admin_translations')
                                ->treatNullLike([])
                                ->scalarPrototype()->end()
                                ->defaultValue(['@CoreShopAddressBundle/Resources/install/pimcore/admin-translations.yml'])
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }
}
