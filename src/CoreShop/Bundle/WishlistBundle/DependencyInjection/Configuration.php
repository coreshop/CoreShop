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

namespace CoreShop\Bundle\WishlistBundle\DependencyInjection;

use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\WishlistBundle\Pimcore\Repository\WishlistItemRepository;
use CoreShop\Bundle\WishlistBundle\Pimcore\Repository\WishlistRepository;
use CoreShop\Component\Resource\Factory\PimcoreFactory;
use CoreShop\Component\Wishlist\Model\WishlistInterface;
use CoreShop\Component\Wishlist\Model\WishlistItemInterface;
use CoreShop\Component\Wishlist\Model\WishlistProductInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @inheritdoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('coreshop_wishlist');

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $this->addModelsSection($rootNode);
        $this->addStack($rootNode);

        return $treeBuilder;
    }

    private function addStack(ArrayNodeDefinition $node): void
    {
        $node->children()
            ->arrayNode('stack')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('wishlist')->defaultValue(WishlistInterface::class)->cannotBeEmpty()->end()
                    ->scalarNode('wishlist_item')->defaultValue(WishlistItemInterface::class)->cannotBeEmpty()->end()
                    ->scalarNode('wishlist_product')->defaultValue(WishlistProductInterface::class)->cannotBeEmpty()->end()
                ->end()
            ->end()
        ->end()
        ;
    }

    private function addModelsSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('pimcore')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('wishlist')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('path')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('wishlist')->defaultValue('wishlists')->end()
                                    ->end()
                                ->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\DataObject\CoreShopWishlist')->cannotBeEmpty()->end()
                                        ->scalarNode('pimcore_class_name')->end()
                                        ->scalarNode('interface')->defaultValue(WishlistInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(WishlistRepository::class)->end()
                                        ->scalarNode('install_file')->defaultValue('@CoreShopWishlistBundle/Resources/install/pimcore/classes/CoreShopWishlist.json')->end()
                                        ->scalarNode('type')->defaultValue(CoreShopResourceBundle::PIMCORE_MODEL_TYPE_OBJECT)->cannotBeOverwritten(true)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('wishlist_item')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('path')->defaultValue('items')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\DataObject\CoreShopWishlistItem')->cannotBeEmpty()->end()
                                        ->scalarNode('pimcore_class_name')->end()
                                        ->scalarNode('interface')->defaultValue(WishlistItemInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(WishlistItemRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('install_file')->defaultValue('@CoreShopWishlistBundle/Resources/install/pimcore/classes/CoreShopWishlistItem.json')->end()
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
}
