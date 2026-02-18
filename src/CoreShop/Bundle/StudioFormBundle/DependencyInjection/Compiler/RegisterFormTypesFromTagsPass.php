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

/**
 * Registers form type classes found in service tag attributes (e.g., condition/action form types)
 * into the BlockPrefixFormTypeRegistry so they can be served via the schema endpoint.
 *
 * Usage in bundle classes:
 *   $container->addCompilerPass(new RegisterFormTypesFromTagsPass('coreshop.cart_price_rule.condition'));
 *   $container->addCompilerPass(new RegisterFormTypesFromTagsPass('coreshop.cart_price_rule.action'));
 */
final class RegisterFormTypesFromTagsPass implements CompilerPassInterface
{
    public function __construct(
        private readonly string $tag,
        private readonly string $attributeName = 'form-type',
    ) {
    }

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(BlockPrefixFormTypeRegistry::class)) {
            return;
        }

        $registry = $container->getDefinition(BlockPrefixFormTypeRegistry::class);

        foreach ($container->findTaggedServiceIds($this->tag) as $id => $tags) {
            foreach ($tags as $attributes) {
                if (isset($attributes[$this->attributeName])) {
                    $registry->addMethodCall('addFormTypeClass', [$attributes[$this->attributeName]]);
                }
            }
        }
    }
}
