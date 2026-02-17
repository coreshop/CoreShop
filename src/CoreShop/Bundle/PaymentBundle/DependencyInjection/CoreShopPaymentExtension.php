<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\PaymentBundle\DependencyInjection;

use CoreShop\Bundle\PaymentBundle\Attribute\AsPaymentPriceCalculator;
use CoreShop\Bundle\PaymentBundle\Attribute\AsPaymentRuleActionProcessor;
use CoreShop\Bundle\PaymentBundle\Attribute\AsPaymentRuleConditionChecker;
use CoreShop\Bundle\PaymentBundle\DependencyInjection\Compiler\PaymentCalculatorsPass;
use CoreShop\Bundle\PaymentBundle\DependencyInjection\Compiler\PaymentProviderRuleActionPass;
use CoreShop\Bundle\PaymentBundle\DependencyInjection\Compiler\PaymentProviderRuleConditionPass;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractModelExtension;
use CoreShop\Component\Payment\Calculator\PaymentPriceCalculatorInterface;
use CoreShop\Component\Payment\Rule\Condition\PaymentConditionCheckerInterface;
use CoreShop\Component\Payment\Rule\Processor\PaymentProviderRuleActionProcessorInterface;
use CoreShop\Component\Registry\Autoconfiguration;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class CoreShopPaymentExtension extends AbstractModelExtension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container): void
    {
        $bundles = $container->getParameter('kernel.bundles');

        if (array_key_exists('PimcoreStudioBackendBundle', $bundles)) {
            $container->prependExtensionConfig('pimcore_studio_backend', [
                'data_object_data_adapter_mapping' => [
                    'CoreShop\\Bundle\\ResourceBundle\\StudioBackend\\DataAdapter\\CoreShopSelectAdapter' => [
                        'coreShopPaymentProvider',
                    ],
                    'CoreShop\\Bundle\\ResourceBundle\\StudioBackend\\DataAdapter\\CoreShopMultiSelectAdapter' => [
                        'coreShopPaymentProviderMultiselect',
                    ],
                ],
            ]);
        }
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configs = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->registerResources('coreshop', CoreShopResourceBundle::DRIVER_DOCTRINE_ORM, $configs['resources'], $container);
        //$this->registerPimcoreModels('coreshop', $configs['pimcore'], $container);

        if (array_key_exists('pimcore_admin', $configs)) {
            $this->registerPimcoreResources('coreshop', $configs['pimcore_admin'], $container);
        }

        $bundles = $container->getParameter('kernel.bundles');

        if (array_key_exists('PimcoreDataHubBundle', $bundles)) {
            $loader->load('services/data_hub.yml');
        }

        if (array_key_exists('PimcoreStudioUiBundle', $bundles)) {
            $loader->load('services/studio.yml');
        }

        $loader->load('services.yml');

        Autoconfiguration::registerForAutoConfiguration(
            $container,
            PaymentConditionCheckerInterface::class,
            PaymentProviderRuleConditionPass::PAYMENT_PROVIDER_RULE_CONDITION_TAG,
            AsPaymentRuleConditionChecker::class,
            $configs['autoconfigure_with_attributes'],
        );

        Autoconfiguration::registerForAutoConfiguration(
            $container,
            PaymentProviderRuleActionProcessorInterface::class,
            PaymentProviderRuleActionPass::PAYMENT_PROVIDER_RULE_ACTION_TAG,
            AsPaymentRuleActionProcessor::class,
            $configs['autoconfigure_with_attributes'],
        );

        Autoconfiguration::registerForAutoConfiguration(
            $container,
            PaymentPriceCalculatorInterface::class,
            PaymentCalculatorsPass::PAYMENT_PRICE_CALCULATOR_TAG,
            AsPaymentPriceCalculator::class,
            $configs['autoconfigure_with_attributes'],
        );
    }
}
