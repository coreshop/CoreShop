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

namespace CoreShop\Bundle\StudioFormBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchemaGenerator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterFormSchemaEnricherPass implements CompilerPassInterface
{
    public const string TAG = 'coreshop_studio_form.enricher';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(FormSchemaGenerator::class)) {
            return;
        }

        $generator = $container->getDefinition(FormSchemaGenerator::class);

        $taggedServices = $container->findTaggedServiceIds(self::TAG);

        $enrichers = [];
        foreach ($taggedServices as $id => $tags) {
            $priority = $tags[0]['priority'] ?? 0;
            $enrichers[] = ['id' => $id, 'priority' => $priority];
        }

        usort($enrichers, static fn (array $a, array $b) => $b['priority'] <=> $a['priority']);

        foreach ($enrichers as $enricher) {
            $generator->addMethodCall('addEnricher', [new Reference($enricher['id'])]);
        }
    }
}
