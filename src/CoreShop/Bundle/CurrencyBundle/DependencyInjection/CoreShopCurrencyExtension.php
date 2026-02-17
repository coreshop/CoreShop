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

namespace CoreShop\Bundle\CurrencyBundle\DependencyInjection;

use CoreShop\Bundle\CurrencyBundle\Attribute\AsCurrencyContext;
use CoreShop\Bundle\CurrencyBundle\DependencyInjection\Compiler\CompositeCurrencyContextPass;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractModelExtension;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Registry\Autoconfiguration;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class CoreShopCurrencyExtension extends AbstractModelExtension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container): void
    {
        $bundles = $container->getParameter('kernel.bundles');

        if (array_key_exists('PimcoreStudioBackendBundle', $bundles)) {
            $container->prependExtensionConfig('pimcore_studio_backend', [
                'data_object_data_adapter_mapping' => [
                    'CoreShop\\Bundle\\ResourceBundle\\StudioBackend\\DataAdapter\\CoreShopSelectAdapter' => [
                        'coreShopCurrency',
                    ],
                    'CoreShop\\Bundle\\ResourceBundle\\StudioBackend\\DataAdapter\\CoreShopMultiSelectAdapter' => [
                        'coreShopCurrencyMultiselect',
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

        $container->setParameter('coreshop.currency.decimal_factor', $configs['money_decimal_factor']);
        $container->setParameter('coreshop.currency.decimal_precision', $configs['money_decimal_precision']);

        Autoconfiguration::registerForAutoConfiguration(
            $container,
            CurrencyContextInterface::class,
            CompositeCurrencyContextPass::CURRENCY_CONTEXT_SERVICE_TAG,
            AsCurrencyContext::class,
            $configs['autoconfigure_with_attributes'],
        );
    }
}
