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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\UserBundle\DependencyInjection;

use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\UserBundle\Pimcore\Repository\UserRepository;
use CoreShop\Component\Resource\Factory\PimcoreFactory;
use CoreShop\Component\User\Model\UserInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('coreshop_user');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('driver')->defaultValue(CoreShopResourceBundle::DRIVER_DOCTRINE_ORM)->end()
            ->end()
        ;

        $this->addStack($rootNode);
        $this->addModelsSection($rootNode);
        $this->addPimcoreResourcesSection($rootNode);

        return $treeBuilder;
    }

    private function addModelsSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('pimcore')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('user')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('path')->defaultValue('user')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\DataObject\CoreShopUser')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(UserInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(UserRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('install_file')->defaultValue('@CoreShopUserBundle/Resources/install/pimcore/classes/CoreShopUser.json')->end()
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

    private function addStack(ArrayNodeDefinition $node): void
    {
        $node->children()
            ->arrayNode('stack')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('user')->defaultValue(UserInterface::class)->cannotBeEmpty()->end()
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
                ->end()
            ->end()
        ->end()
        ;
    }
}
