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

namespace CoreShop\Bundle\MessengerBundle\DependencyInjection\CompilerPass;

use CoreShop\Bundle\MessengerBundle\Messenger\ChainFailedAtResolver;
use Symfony\Component\DependencyInjection\Argument\IteratorArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class FailedAtResolverPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public const TAG_NAME = 'coreshop.messenger.failed_at_resolver';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(ChainFailedAtResolver::class)) {
            return;
        }

        $container
            ->getDefinition(ChainFailedAtResolver::class)
            ->replaceArgument(0, new IteratorArgument($this->findAndSortTaggedServices(self::TAG_NAME, $container)))
        ;
    }
}
