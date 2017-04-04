<?php

namespace CoreShop\Bundle\OrderBundle\DependencyInjection;

use CoreShop\Bundle\CurrencyBundle\Form\Type\CurrencyType;
use CoreShop\Bundle\OrderBundle\Controller\CartController;
use CoreShop\Bundle\ProductBundle\Controller\ProductController;
use CoreShop\Bundle\ProductBundle\Controller\ProductPriceRuleController;
use CoreShop\Bundle\ProductBundle\Form\Type\ProductPriceRuleType;
use CoreShop\Bundle\ResourceBundle\Controller\PimcoreFrontendController;
use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Product\Model\ProductPriceRule;
use CoreShop\Component\Product\Model\ProductPriceRuleInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Resource\Factory\Factory;
use CoreShop\Component\Currency\Model\Currency;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Resource\Factory\PimcoreFactory;
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
        $rootNode = $treeBuilder->root('coreshop_order');

        $rootNode
            ->children()
                ->scalarNode('driver')->defaultValue(CoreShopResourceBundle::DRIVER_DOCTRINE_ORM)->end()
            ->end()
        ;
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
                ->arrayNode('pimcore')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('cart')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\Object\CoreShopCart')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(CartInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('pimcore_controller')->defaultValue(CartController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('is_pimcore_class')->defaultValue(true)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('cart_item')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\Object\CoreShopCartItem')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(CartItemInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('is_pimcore_class')->defaultValue(true)->cannotBeEmpty()->end()
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
