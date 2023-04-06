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

namespace CoreShop\Bundle\OrderBundle\DependencyInjection;

use CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler\CartPriceRuleActionPass;
use CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler\CartPriceRuleConditionPass;
use CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler\PurchasableDiscountCalculatorsPass;
use CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler\PurchasableDiscountPriceCalculatorsPass;
use CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler\PurchasablePriceCalculatorsPass;
use CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler\PurchasableRetailPriceCalculatorsPass;
use CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler\PurchasableWholesalePriceCalculatorsPass;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractModelExtension;
use CoreShop\Component\Order\Calculator\PurchasableDiscountCalculatorInterface;
use CoreShop\Component\Order\Calculator\PurchasableDiscountPriceCalculatorInterface;
use CoreShop\Component\Order\Calculator\PurchasablePriceCalculatorInterface;
use CoreShop\Component\Order\Calculator\PurchasableRetailPriceCalculatorInterface;
use CoreShop\Component\Order\Calculator\PurchasableWholesalePriceCalculatorInterface;
use CoreShop\Component\Order\Cart\Rule\Action\CartPriceRuleActionProcessorInterface;
use CoreShop\Component\Order\Cart\Rule\Condition\CartRuleConditionCheckerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class CoreShopOrderExtension extends AbstractModelExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configs = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.yml');

        $this->registerResources('coreshop', CoreShopResourceBundle::DRIVER_DOCTRINE_ORM, $configs['resources'], $container);
        $this->registerPimcoreModels('coreshop', $configs['pimcore'], $container);

        if (array_key_exists('pimcore_admin', $configs)) {
            $this->registerPimcoreResources('coreshop', $configs['pimcore_admin'], $container);
        }

        if (array_key_exists('stack', $configs)) {
            $this->registerStack('coreshop', $configs['stack'], $container);
        }

        $bundles = $container->getParameter('kernel.bundles');

        if (array_key_exists('PimcoreDataHubBundle', $bundles)) {
            $loader->load('services/data_hub.yml');
        }

        $container->setParameter('coreshop.order.legacy_serialization', $configs['legacy_serialization']);

        $loader->load('services.yml');

        $container
            ->registerForAutoconfiguration(CartPriceRuleActionProcessorInterface::class)
            ->addTag(CartPriceRuleActionPass::CART_PRICE_RULE_ACTION_TAG)
        ;

        $container
            ->registerForAutoconfiguration(CartRuleConditionCheckerInterface::class)
            ->addTag(CartPriceRuleConditionPass::CART_PRICE_RULE_CONDITION_TAG)
        ;

        $container
            ->registerForAutoconfiguration(PurchasableDiscountCalculatorInterface::class)
            ->addTag(PurchasableDiscountCalculatorsPass::PURCHASABLE_DISCOUNT_CALCULATOR_TAG)
        ;

        $container
            ->registerForAutoconfiguration(PurchasableDiscountPriceCalculatorInterface::class)
            ->addTag(PurchasableDiscountPriceCalculatorsPass::PURCHASABLE_DISCOUNT_PRICE_CALCULATOR_TAG)
        ;

        $container
            ->registerForAutoconfiguration(PurchasablePriceCalculatorInterface::class)
            ->addTag(PurchasablePriceCalculatorsPass::PURCHASABLE_PRICE_CALCULATOR_TAG)
        ;

        $container
            ->registerForAutoconfiguration(PurchasableRetailPriceCalculatorInterface::class)
            ->addTag(PurchasableRetailPriceCalculatorsPass::PURCHASABLE_RETAIL_PRICE_CALCULATOR_TAG)
        ;

        $container
            ->registerForAutoconfiguration(PurchasableWholesalePriceCalculatorInterface::class)
            ->addTag(PurchasableWholesalePriceCalculatorsPass::PURCHASABLE_WHOLESALE_PRICE_CALCULATOR_TAG)
        ;
    }
}
