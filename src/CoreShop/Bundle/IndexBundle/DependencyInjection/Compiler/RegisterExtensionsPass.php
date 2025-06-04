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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterExtensionsPass implements CompilerPassInterface
{
    public const INDEX_EXTENSION_TAG = 'coreshop.index.extension';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('coreshop.registry.index.extensions')) {
            return;
        }

        $registry = $container->getDefinition('coreshop.registry.index.extensions');

        foreach ($container->findTaggedServiceIds(self::INDEX_EXTENSION_TAG) as $id => $attributes) {
            $registry->addMethodCall('register', [$id, new Reference($id)]);
        }
    }
}
