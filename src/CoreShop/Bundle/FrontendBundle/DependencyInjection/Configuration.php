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

namespace CoreShop\Bundle\FrontendBundle\DependencyInjection;

use CoreShop\Bundle\FrontendBundle\Controller\CartController;
use CoreShop\Bundle\FrontendBundle\Controller\CategoryController;
use CoreShop\Bundle\FrontendBundle\Controller\CheckoutController;
use CoreShop\Bundle\FrontendBundle\Controller\CurrencyController;
use CoreShop\Bundle\FrontendBundle\Controller\CustomerController;
use CoreShop\Bundle\FrontendBundle\Controller\IndexController;
use CoreShop\Bundle\FrontendBundle\Controller\LanguageController;
use CoreShop\Bundle\FrontendBundle\Controller\ProductController;
use CoreShop\Bundle\FrontendBundle\Controller\QuoteController;
use CoreShop\Bundle\FrontendBundle\Controller\RegisterController;
use CoreShop\Bundle\FrontendBundle\Controller\SearchController;
use CoreShop\Bundle\FrontendBundle\Controller\SecurityController;
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
        $rootNode = $treeBuilder->root('coreshop_frontend');

        $this->addPimcoreResourcesSection($rootNode);
        $this->addControllerSection($rootNode);

        return $treeBuilder;
    }

    private function addControllerSection(ArrayNodeDefinition $node) {
        $node->children()
                ->arrayNode('controllers')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('index')->defaultValue(IndexController::class)->end()
                        ->scalarNode('register')->defaultValue(RegisterController::class)->end()
                        ->scalarNode('customer')->defaultValue(CustomerController::class)->end()
                        ->scalarNode('currency')->defaultValue(CurrencyController::class)->end()
                        ->scalarNode('language')->defaultValue(LanguageController::class)->end()
                        ->scalarNode('search')->defaultValue(SearchController::class)->end()
                        ->scalarNode('cart')->defaultValue(CartController::class)->end()
                        ->scalarNode('checkout')->defaultValue(CheckoutController::class)->end()
                        ->scalarNode('category')->defaultValue(CategoryController::class)->end()
                        ->scalarNode('product')->defaultValue(ProductController::class)->end()
                        ->scalarNode('quote')->defaultValue(QuoteController::class)->end()
                        ->scalarNode('security')->defaultValue(SecurityController::class)->end()
                        ->scalarNode('payment')->defaultValue(PaymentController::class)->end()
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
                            ->scalarNode('routes')->defaultValue(['@CoreShopFrontendBundle/Resources/install/pimcore/staticroutes.yml'])->end()
                            ->scalarNode('documents')->defaultValue(['@CoreShopFrontendBundle/Resources/install/pimcore/documents.yml'])->end()
                            ->scalarNode('image_thumbnails')->defaultValue(['@CoreShopFrontendBundle/Resources/install/pimcore/image-thumbnails.yml'])->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }
}
