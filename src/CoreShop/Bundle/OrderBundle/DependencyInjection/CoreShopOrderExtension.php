<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\OrderBundle\DependencyInjection;

use CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler\CartPriceRuleActionPass;
use CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler\CartPriceRuleConditionPass;
use CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler\PurchasableDiscountCalculatorsPass;
use CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler\PurchasableDiscountPriceCalculatorsPass;
use CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler\PurchasablePriceCalculatorsPass;
use CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler\PurchasableRetailPriceCalculatorsPass;
use CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler\PurchasableWholesalePriceCalculatorsPass;
use CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler\RegisterCartContextsPass;
use CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler\RegisterCartProcessorPass;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractModelExtension;
use CoreShop\Component\Order\Calculator\PurchasableDiscountCalculatorInterface;
use CoreShop\Component\Order\Calculator\PurchasableDiscountPriceCalculatorInterface;
use CoreShop\Component\Order\Calculator\PurchasablePriceCalculatorInterface;
use CoreShop\Component\Order\Calculator\PurchasableRetailPriceCalculatorInterface;
use CoreShop\Component\Order\Calculator\PurchasableWholesalePriceCalculatorInterface;
use CoreShop\Component\Order\Cart\Rule\Action\CartPriceRuleActionProcessorInterface;
use CoreShop\Component\Order\Cart\Rule\Condition\CartRuleConditionCheckerInterface;
use CoreShop\Component\Order\Context\CartContextInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class CoreShopOrderExtension extends AbstractModelExtension
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

        $container->setParameter('coreshop.cart.expiration.days', $config['expiration']['cart']['days']);
        $container->setParameter('coreshop.cart.expiration.anonymous', $config['expiration']['cart']['anonymous']);
        $container->setParameter('coreshop.cart.expiration.customer', $config['expiration']['cart']['customer']);

        $container->setParameter('coreshop.order.expiration.days', $config['expiration']['order']['days']);

        $loader->load('services.yml');

        $container
            ->registerForAutoconfiguration(CartPriceRuleActionProcessorInterface::class)
            ->addTag(CartPriceRuleActionPass::CART_PRICE_RULE_ACTION_TAG);

        $container
            ->registerForAutoconfiguration(CartRuleConditionCheckerInterface::class)
            ->addTag(CartPriceRuleConditionPass::CART_PRICE_RULE_CONDITION_TAG);

        $container
            ->registerForAutoconfiguration(PurchasableDiscountCalculatorInterface::class)
            ->addTag(PurchasableDiscountCalculatorsPass::PURCHASABLE_DISCOUNT_CALCULATOR_TAG);

        $container
            ->registerForAutoconfiguration(PurchasableDiscountPriceCalculatorInterface::class)
            ->addTag(PurchasableDiscountPriceCalculatorsPass::PURCHASABLE_DISCOUNT_PRICE_CALCULATOR_TAG);

        $container
            ->registerForAutoconfiguration(PurchasablePriceCalculatorInterface::class)
            ->addTag(PurchasablePriceCalculatorsPass::PURCHASABLE_PRICE_CALCULATOR_TAG);

        $container
            ->registerForAutoconfiguration(PurchasableRetailPriceCalculatorInterface::class)
            ->addTag(PurchasableRetailPriceCalculatorsPass::PURCHASABLE_RETAIL_PRICE_CALCULATOR_TAG);

        $container
            ->registerForAutoconfiguration(PurchasableWholesalePriceCalculatorInterface::class)
            ->addTag(PurchasableWholesalePriceCalculatorsPass::PURCHASABLE_WHOLESALE_PRICE_CALCULATOR_TAG);

        $container
            ->registerForAutoconfiguration(CartContextInterface::class)
            ->addTag(RegisterCartContextsPass::CART_CONTEXT_TAG);

        $container
            ->registerForAutoconfiguration(CartProcessorInterface::class)
            ->addTag(RegisterCartProcessorPass::CART_PROCESSOR_TAG);
    }
}
