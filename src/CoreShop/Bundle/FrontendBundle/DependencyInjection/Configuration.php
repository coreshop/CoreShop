<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\FrontendBundle\DependencyInjection;

use CoreShop\Bundle\FrontendBundle\Controller\CartController;
use CoreShop\Bundle\FrontendBundle\Controller\CategoryController;
use CoreShop\Bundle\FrontendBundle\Controller\CheckoutController;
use CoreShop\Bundle\FrontendBundle\Controller\CurrencyController;
use CoreShop\Bundle\FrontendBundle\Controller\CustomerController;
use CoreShop\Bundle\FrontendBundle\Controller\IndexController;
use CoreShop\Bundle\FrontendBundle\Controller\MailController;
use CoreShop\Bundle\FrontendBundle\Controller\OrderController;
use CoreShop\Bundle\FrontendBundle\Controller\ProductController;
use CoreShop\Bundle\FrontendBundle\Controller\QuoteController;
use CoreShop\Bundle\FrontendBundle\Controller\RegisterController;
use CoreShop\Bundle\FrontendBundle\Controller\SearchController;
use CoreShop\Bundle\FrontendBundle\Controller\SecurityController;
use CoreShop\Bundle\FrontendBundle\Controller\WishlistController;
use CoreShop\Bundle\PayumBundle\Controller\PaymentController;
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
        $rootNode = $treeBuilder->root('core_shop_frontend');

        $rootNode
            ->children()
                ->scalarNode('view_suffix')->defaultValue('twig')->end()
                ->scalarNode('view_bundle')->defaultValue('CoreShopFrontend')->end()
            ->end();

        $this->addPimcoreResourcesSection($rootNode);
        $this->addControllerSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addControllerSection(ArrayNodeDefinition $node)
    {
        $node->children()
            ->arrayNode('controllers')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('index')->defaultValue(IndexController::class)->end()
                    ->scalarNode('register')->defaultValue(RegisterController::class)->end()
                    ->scalarNode('customer')->defaultValue(CustomerController::class)->end()
                    ->scalarNode('currency')->defaultValue(CurrencyController::class)->end()
                    ->scalarNode('search')->defaultValue(SearchController::class)->end()
                    ->scalarNode('cart')->defaultValue(CartController::class)->end()
                    ->scalarNode('checkout')->defaultValue(CheckoutController::class)->end()
                    ->scalarNode('order')->defaultValue(OrderController::class)->end()
                    ->scalarNode('category')->defaultValue(CategoryController::class)->end()
                    ->scalarNode('product')->defaultValue(ProductController::class)->end()
                    ->scalarNode('quote')->defaultValue(QuoteController::class)->end()
                    ->scalarNode('security')->defaultValue(SecurityController::class)->end()
                    ->scalarNode('payment')->defaultValue(PaymentController::class)->end()
                    ->scalarNode('wishlist')->defaultValue(WishlistController::class)->end()
                    ->scalarNode('mail')->defaultValue(MailController::class)->end()
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
                    ->arrayNode('install')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->arrayNode('routes')
                                ->treatNullLike([])
                                ->scalarPrototype()->end()
                                ->defaultValue(['@CoreShopFrontendBundle/Resources/install/pimcore/staticroutes.yml'])
                            ->end()
                            ->arrayNode('documents')
                                ->treatNullLike([])
                                ->scalarPrototype()->end()
                                ->defaultValue(['@CoreShopFrontendBundle/Resources/install/pimcore/documents.yml'])
                            ->end()
                            ->arrayNode('image_thumbnails')
                                ->treatNullLike([])
                                ->scalarPrototype()->end()
                                ->defaultValue(['@CoreShopFrontendBundle/Resources/install/pimcore/image-thumbnails.yml'])
                            ->end()
                            ->arrayNode('translations')
                                ->treatNullLike([])
                                ->scalarPrototype()->end()
                                ->defaultValue(['@CoreShopFrontendBundle/Resources/install/pimcore/translations.yml'])
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }
}
