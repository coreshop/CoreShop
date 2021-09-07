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

declare(strict_types=1);

namespace CoreShop\Bundle\ShippingBundle\DependencyInjection;

use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractModelExtension;
use CoreShop\Bundle\ShippingBundle\DependencyInjection\Compiler\CompositeShippableValidatorPass;
use CoreShop\Bundle\ShippingBundle\DependencyInjection\Compiler\ShippingPriceCalculatorsPass;
use CoreShop\Bundle\ShippingBundle\DependencyInjection\Compiler\ShippingRuleActionPass;
use CoreShop\Bundle\ShippingBundle\DependencyInjection\Compiler\ShippingRuleConditionPass;
use CoreShop\Bundle\ShippingBundle\DependencyInjection\Compiler\ShippingTaxCalculationStrategyPass;
use CoreShop\Component\Shipping\Calculator\CarrierPriceCalculatorInterface;
use CoreShop\Component\Shipping\Resolver\DefaultCarrierResolverInterface;
use CoreShop\Component\Shipping\Rule\Condition\ShippingConditionCheckerInterface;
use CoreShop\Component\Shipping\Rule\Processor\ShippingRuleActionProcessorInterface;
use CoreShop\Component\Shipping\Taxation\TaxCalculationStrategyInterface;
use CoreShop\Component\Shipping\Validator\ShippableCarrierValidatorInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class CoreShopShippingExtension extends AbstractModelExtension
{
    public function load(array $config, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->registerResources('coreshop', CoreShopResourceBundle::DRIVER_DOCTRINE_ORM, $config['resources'], $container);

        if (array_key_exists('pimcore_admin', $config)) {
            $this->registerPimcoreResources('coreshop', $config['pimcore_admin'], $container);
        }

        $alias = new Alias($config['default_resolver']);
        $alias->setPublic(true);

        $container->setAlias(DefaultCarrierResolverInterface::class, $alias);

        $bundles = $container->getParameter('kernel.bundles');

        if (array_key_exists('PimcoreDataHubBundle', $bundles)) {
            $loader->load('services/data_hub.yml');
        }

        $loader->load('services.yml');

        $container
            ->registerForAutoconfiguration(ShippableCarrierValidatorInterface::class)
            ->addTag(CompositeShippableValidatorPass::SHIPABLE_VALIDATOR_TAG);

        $container
            ->registerForAutoconfiguration(CarrierPriceCalculatorInterface::class)
            ->addTag(ShippingPriceCalculatorsPass::SHIPPING_PRICE_CALCULATOR_TAG);

        $container
            ->registerForAutoconfiguration(ShippingRuleActionProcessorInterface::class)
            ->addTag(ShippingRuleActionPass::SHIPPING_RULE_ACTION_TAG);

        $container
            ->registerForAutoconfiguration(ShippingConditionCheckerInterface::class)
            ->addTag(ShippingRuleConditionPass::SHIPPING_RULE_CONDITION_TAG);

        $container
            ->registerForAutoconfiguration(TaxCalculationStrategyInterface::class)
            ->addTag(ShippingTaxCalculationStrategyPass::SHIPPING_TAX_STRATEGY_TAG);
    }
}
