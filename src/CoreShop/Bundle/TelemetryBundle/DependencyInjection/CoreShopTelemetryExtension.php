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

namespace CoreShop\Bundle\TelemetryBundle\DependencyInjection;

use CoreShop\Bundle\TelemetryBundle\Contract\TelemetryDataProviderInterface;
use CoreShop\Bundle\TelemetryBundle\Pinger\TelemetryPinger;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class CoreShopTelemetryExtension extends Extension
{
    public const string PROVIDER_TAG = 'coreshop.telemetry.provider';

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configs = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $container->setParameter('coreshop.telemetry.enabled', $configs['enabled']);
        $container->setParameter('coreshop.telemetry.endpoint', $configs['endpoint']);
        $container->setParameter('coreshop.telemetry.timeout', $configs['timeout']);
        $container->registerForAutoconfiguration(TelemetryDataProviderInterface::class)->addTag(self::PROVIDER_TAG);

        $loader->load('services.yml');
        // lint:yaml runs without --parse-tags, so the provider iterator is wired here instead of via !tagged_iterator
        $container->getDefinition(TelemetryPinger::class)->replaceArgument(1, new TaggedIteratorArgument(self::PROVIDER_TAG));

        $env = (string) $container->getParameter('kernel.environment');
        if (str_contains($env, 'test')) {
            $loader->load('services_test.yml');
        }
    }
}
