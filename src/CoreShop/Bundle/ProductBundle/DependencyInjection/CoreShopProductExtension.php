<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ProductBundle\DependencyInjection;

use CoreShop\Bundle\ProductBundle\DependencyInjection\Compiler\ProductDiscountCalculatorsPass;
use CoreShop\Bundle\ProductBundle\DependencyInjection\Compiler\ProductDiscountPriceCalculatorsPass;
use CoreShop\Bundle\ProductBundle\DependencyInjection\Compiler\ProductPriceRuleActionPass;
use CoreShop\Bundle\ProductBundle\DependencyInjection\Compiler\ProductPriceRuleConditionPass;
use CoreShop\Bundle\ProductBundle\DependencyInjection\Compiler\ProductRetailPriceCalculatorsPass;
use CoreShop\Bundle\ProductBundle\DependencyInjection\Compiler\ProductSpecificPriceRuleActionPass;
use CoreShop\Bundle\ProductBundle\DependencyInjection\Compiler\ProductSpecificPriceRuleConditionPass;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractModelExtension;
use CoreShop\Component\Product\Calculator\ProductDiscountCalculatorInterface;
use CoreShop\Component\Product\Calculator\ProductDiscountPriceCalculatorInterface;
use CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface;
use CoreShop\Component\Product\Calculator\ProductRetailPriceCalculatorInterface;
use CoreShop\Component\Product\Rule\Action\ProductActionProcessorInterface;
use CoreShop\Component\Product\Rule\Action\ProductSpecificActionProcessorInterface;
use CoreShop\Component\Product\Rule\Condition\ProductConditionCheckerInterface;
use CoreShop\Component\Product\Rule\Condition\ProductSpecificConditionCheckerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class CoreShopProductExtension extends AbstractModelExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->registerResources('coreshop', $config['driver'], $config['resources'], $container);
        $this->registerPimcoreModels('coreshop', $config['pimcore'], $container);

        if (array_key_exists('pimcore_admin', $config)) {
            $this->registerPimcoreResources('coreshop', $config['pimcore_admin'], $container);
        }

        if (array_key_exists('stack', $config)) {
            $this->registerStack('coreshop', $config['stack'], $container);
        }

        $bundles = $container->getParameter('kernel.bundles');

        if (array_key_exists('PimcoreDataHubBundle', $bundles)) {
            $loader->load('services/data_hub.yml');
        }

        $loader->load('services.yml');

        $container
            ->registerForAutoconfiguration(ProductDiscountCalculatorInterface::class)
            ->addTag(ProductDiscountCalculatorsPass::PRODUCT_DISCOUNT_CALCULATOR_TAG);

        $container
            ->registerForAutoconfiguration(ProductDiscountPriceCalculatorInterface::class)
            ->addTag(ProductDiscountPriceCalculatorsPass::PRODUCT_DISCOUNT_PRICE_CALCULATOR_TAG);

        $container
            ->registerForAutoconfiguration(ProductPriceCalculatorInterface::class)
            ->addTag(ProductRetailPriceCalculatorsPass::PRODUCT_RETAIL_PRICE_CALCULATOR_TAG);

        $container
            ->registerForAutoconfiguration(ProductRetailPriceCalculatorInterface::class)
            ->addTag(ProductRetailPriceCalculatorsPass::PRODUCT_RETAIL_PRICE_CALCULATOR_TAG);

        $container
            ->registerForAutoconfiguration(ProductActionProcessorInterface::class)
            ->addTag(ProductPriceRuleActionPass::PRODUCT_PRICE_RULE_ACTION_TAG);

        $container
            ->registerForAutoconfiguration(ProductConditionCheckerInterface::class)
            ->addTag(ProductPriceRuleConditionPass::PRODUCT_PRICE_RULE_CONDITION_TAG);

        $container
            ->registerForAutoconfiguration(ProductSpecificActionProcessorInterface::class)
            ->addTag(ProductSpecificPriceRuleActionPass::PRODUCT_SPECIFIC_PRICE_RULE_ACTION_TAG);

        $container
            ->registerForAutoconfiguration(ProductSpecificConditionCheckerInterface::class)
            ->addTag(ProductSpecificPriceRuleConditionPass::PRODUCT_SPECIFIC_PRICE_RULE_CONDITION_TAG);
    }
}
