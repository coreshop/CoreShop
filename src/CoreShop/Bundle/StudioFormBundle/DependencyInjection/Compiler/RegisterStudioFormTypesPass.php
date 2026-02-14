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
    private const string FORM_TYPE_TAG = 'form.type';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(BlockPrefixFormTypeRegistry::class)) {
            return;
        }

        $registry = $container->getDefinition(BlockPrefixFormTypeRegistry::class);
        $taggedServices = array_merge(
            $container->findTaggedServiceIds(self::TAG),
            $container->findTaggedServiceIds(self::FORM_TYPE_TAG),
        );

        $formTypeClasses = [];

        foreach ($taggedServices as $id => $tags) {
            $definition = $container->getDefinition($id);
            $class = $definition->getClass();
            if (is_string($class) && $class !== '') {
                $formTypeClasses[] = $class;
            }
        }

        $registry->replaceArgument('$formTypeClasses', array_values(array_unique($formTypeClasses)));
    }
}
