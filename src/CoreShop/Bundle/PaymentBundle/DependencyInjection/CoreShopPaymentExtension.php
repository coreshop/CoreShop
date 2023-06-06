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

namespace CoreShop\Bundle\PaymentBundle\DependencyInjection;

use CoreShop\Bundle\PaymentBundle\DependencyInjection\Compiler\PaymentCalculatorsPass;
use CoreShop\Bundle\PaymentBundle\DependencyInjection\Compiler\PaymentProviderRuleActionPass;
use CoreShop\Bundle\PaymentBundle\DependencyInjection\Compiler\PaymentProviderRuleConditionPass;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractModelExtension;
use CoreShop\Component\Payment\Calculator\PaymentPriceCalculatorInterface;
use CoreShop\Component\Payment\Rule\Condition\PaymentConditionCheckerInterface;
use CoreShop\Component\Payment\Rule\Processor\PaymentProviderRuleActionProcessorInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class CoreShopPaymentExtension extends AbstractModelExtension
{
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

        $loader->load('services.yml');

        $container
            ->registerForAutoconfiguration(PaymentConditionCheckerInterface::class)
            ->addTag(PaymentProviderRuleConditionPass::PAYMENT_PROVIDER_RULE_CONDITION_TAG)
        ;
        $container
            ->registerForAutoconfiguration(PaymentProviderRuleActionProcessorInterface::class)
            ->addTag(PaymentProviderRuleActionPass::PAYMENT_PROVIDER_RULE_ACTION_TAG)
        ;
        $container
            ->registerForAutoconfiguration(PaymentPriceCalculatorInterface::class)
            ->addTag(PaymentCalculatorsPass::PAYMENT_PRICE_CALCULATOR_TAG)
        ;
    }
}
