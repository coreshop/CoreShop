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

use CoreShop\Bundle\StudioFormBundle\Form\Schema\BlockPrefixFormTypeRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class RegisterStudioFormTypesPass implements CompilerPassInterface
{
    public const string TAG = 'coreshop.studio_form';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(BlockPrefixFormTypeRegistry::class)) {
            return;
        }

        $registry = $container->getDefinition(BlockPrefixFormTypeRegistry::class);
        $taggedServices = $container->findTaggedServiceIds(self::TAG);

        $formTypeClasses = [];

        foreach ($taggedServices as $id => $tags) {
            $definition = $container->getDefinition($id);
            $formTypeClasses[] = $definition->getClass() ?? $id;
        }

        $registry->replaceArgument('$formTypeClasses', $formTypeClasses);
    }
}
