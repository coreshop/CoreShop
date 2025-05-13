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

namespace CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

abstract class RegisterImplementationLoaderPass implements CompilerPassInterface
{
    public function __construct(
        protected string $implementationLoader,
        protected string $tag,
    ) {
    }

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition($this->implementationLoader)) {
            return;
        }

        $registry = $container->getDefinition($this->implementationLoader);

        foreach ($container->findTaggedServiceIds($this->tag) as $id => $attributes) {
            $registry->addMethodCall('addLoader', [new Reference($id)]);
        }
    }
}
