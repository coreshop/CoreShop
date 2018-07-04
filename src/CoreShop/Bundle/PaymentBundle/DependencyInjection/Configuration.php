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

namespace CoreShop\Bundle\PaymentBundle\DependencyInjection;

use CoreShop\Bundle\PaymentBundle\Doctrine\ORM\PaymentProviderRepository;
use CoreShop\Bundle\PaymentBundle\Doctrine\ORM\PaymentRepository;
use CoreShop\Bundle\PaymentBundle\Form\Type\PaymentProviderTranslationType;
use CoreShop\Bundle\PaymentBundle\Form\Type\PaymentProviderType;
use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Component\Payment\Model\Payment;
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Payment\Model\PaymentProvider;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Payment\Model\PaymentProviderTranslation;
use CoreShop\Component\Payment\Model\PaymentProviderTranslationInterface;
use CoreShop\Component\Resource\Factory\Factory;
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
        $rootNode = $treeBuilder->root('coreshop_payment');

        $rootNode
            ->children()
                ->scalarNode('driver')->defaultValue(CoreShopResourceBundle::DRIVER_DOCTRINE_ORM)->end()
                ->arrayNode('gateways')
                    ->useAttributeAsKey('name')
                    ->prototype('scalar')
                ->end()
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
                        ->arrayNode('payment_provider')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('permission')->defaultValue('payment_provider')->cannotBeOverwritten()->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(PaymentProvider::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(PaymentProviderInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(TranslatableFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(PaymentProviderRepository::class)->end()
                                        ->scalarNode('form')->defaultValue(PaymentProviderType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                                ->arrayNode('translation')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->variableNode('options')->end()
                                        ->arrayNode('classes')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->defaultValue(PaymentProviderTranslation::class)->cannotBeEmpty()->end()
                                                ->scalarNode('interface')->defaultValue(PaymentProviderTranslationInterface::class)->cannotBeEmpty()->end()
                                                ->scalarNode('repository')->cannotBeEmpty()->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->scalarNode('form')->defaultValue(PaymentProviderTranslationType::class)->cannotBeEmpty()->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('payment')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Payment::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(PaymentInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(PaymentRepository::class)->end()
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
                            ->scalarNode('resource')->defaultValue('/bundles/coreshoppayment/pimcore/js/resource.js')->end()
                            ->scalarNode('payment_item')->defaultValue('/bundles/coreshoppayment/pimcore/js/provider/item.js')->end()
                            ->scalarNode('payment_panel')->defaultValue('/bundles/coreshoppayment/pimcore/js/provider/panel.js')->end()
                            ->scalarNode('payment_gateway_abstract')->defaultValue('/bundles/coreshoppayment/pimcore/js/provider/gateways/abstract.js')->end()
                            ->scalarNode('payment_gateway_paypal')->defaultValue('/bundles/coreshoppayment/pimcore/js/provider/gateways/paypal_express_checkout.js')->end()
                            ->scalarNode('payment_gateway_sofort')->defaultValue('/bundles/coreshoppayment/pimcore/js/provider/gateways/sofort.js')->end()
                            ->scalarNode('core_extension_data_provider')->defaultValue('/bundles/coreshoppayment/pimcore/js/coreExtension/data/coreShopPaymentProvider.js')->end()
                            ->scalarNode('core_extension_tag_provider')->defaultValue('/bundles/coreshoppayment/pimcore/js/coreExtension/tags/coreShopPaymentProvider.js')->end()
                        ->end()
                    ->end()
                    ->arrayNode('css')
                        ->addDefaultsIfNotSet()
                        ->ignoreExtraKeys(false)
                        ->children()
                            ->scalarNode('payment')->defaultValue('/bundles/coreshoppayment/pimcore/css/payment.css')->end()
                        ->end()
                    ->end()
                    ->arrayNode('editmode_js')
                        ->addDefaultsIfNotSet()
                        ->ignoreExtraKeys(false)
                        ->children()
                            ->scalarNode('core_extension_document_tag_provider')->defaultValue('/bundles/coreshoppayment/pimcore/js/coreExtension/document/coreShopPaymentProvider.js')->end()
                        ->end()
                    ->end()
                    ->scalarNode('permissions')
                        ->cannotBeOverwritten()
                        ->defaultValue(['payment_provider'])
                    ->end()
                ->end()
            ->end()
        ->end();
    }
}
