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

namespace CoreShop\Bundle\MessengerBundle\DependencyInjection\CompilerPass;

use CoreShop\Bundle\MessengerBundle\Messenger\ReceiversRepository;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class ReceiverPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(ReceiversRepository::class)) {
            return;
        }

        $receiverLocatorDefinition = $container->getDefinition(ReceiversRepository::class);

        if ($container->hasDefinition('console.command.messenger_consume_messages')) {
            $consumeCommandDefinition = $container->getDefinition('console.command.messenger_consume_messages');
            $names = $consumeCommandDefinition->getArgument(4);
            $receiverLocatorDefinition->replaceArgument(1, $names);
        } else {
            $emptyContainer = new Definition(Container::class);

            $receiverLocatorDefinition->replaceArgument(0, $emptyContainer);
            $receiverLocatorDefinition->replaceArgument(1, []);
        }
    }
}
