<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\PaymentBundle\DependencyInjection;

use CoreShop\Bundle\PaymentBundle\Controller\PaymentRuleController;
use CoreShop\Bundle\PaymentBundle\Doctrine\ORM\PaymentProviderRepository;
use CoreShop\Bundle\PaymentBundle\Doctrine\ORM\PaymentRepository;
use CoreShop\Bundle\PaymentBundle\Form\Type\PaymentProviderTranslationType;
use CoreShop\Bundle\PaymentBundle\Form\Type\PaymentProviderType;
use CoreShop\Bundle\PaymentBundle\Form\Type\PaymentRuleGroupType;
use CoreShop\Bundle\PaymentBundle\Form\Type\PaymentRuleType;
use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\RuleBundle\Doctrine\ORM\RuleRepository;
use CoreShop\Component\Payment\Model\Payment;
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Payment\Model\PaymentProvider;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Payment\Model\PaymentProviderTranslation;
use CoreShop\Component\Payment\Model\PaymentProviderTranslationInterface;
use CoreShop\Component\Payment\Model\PaymentRule;
use CoreShop\Component\Payment\Model\PaymentRuleGroup;
use CoreShop\Component\Payment\Model\PaymentRuleGroupInterface;
use CoreShop\Component\Payment\Model\PaymentRuleInterface;
use CoreShop\Component\Resource\Factory\Factory;
use CoreShop\Component\Resource\Factory\TranslatableFactory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('core_shop_payment');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('driver')->defaultValue(CoreShopResourceBundle::DRIVER_DOCTRINE_ORM)->end()
            ->end()
        ;
        $this->addModelsSection($rootNode);
        $this->addPimcoreResourcesSection($rootNode);

        return $treeBuilder;
    }

    private function addModelsSection(ArrayNodeDefinition $node): void
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
                        ->arrayNode('payment_rule')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('permission')->defaultValue('payment_rule')->cannotBeOverwritten()->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(PaymentRule::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(PaymentRuleInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->defaultValue(PaymentRuleController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(RuleRepository::class)->end()
                                        ->scalarNode('form')->defaultValue(PaymentRuleType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('payment_rule_group')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(PaymentRuleGroup::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(PaymentRuleGroupInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(PaymentRuleGroupType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addPimcoreResourcesSection(ArrayNodeDefinition $node): void
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
                        ->useAttributeAsKey('name')
                        ->prototype('scalar')->end()
                    ->end()
                    ->arrayNode('editmode_js')
                        ->useAttributeAsKey('name')
                        ->prototype('scalar')->end()
                    ->end()
                    ->arrayNode('editmode_css')
                        ->useAttributeAsKey('name')
                        ->prototype('scalar')->end()
                    ->end()
                    ->scalarNode('permissions')
                        ->cannotBeOverwritten()
                        ->defaultValue(['payment_provider', 'payment_rule'])
                    ->end()
                ->end()
            ->end()
        ->end()
        ;
    }
}
