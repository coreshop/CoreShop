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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\MessengerBundle\DependencyInjection\CompilerPass;

use CoreShop\Bundle\MessengerBundle\Messenger\ReceiversRepository;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ReceiverPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition(ReceiversRepository::class)
            && $container->hasDefinition('console.command.messenger_consume_messages')) {
            $receiverLocatorDefinition = $container->getDefinition(ReceiversRepository::class);

            $consumeCommandDefinition = $container->getDefinition('console.command.messenger_consume_messages');
            $names = $consumeCommandDefinition->getArgument(4);
            $receiverLocatorDefinition->replaceArgument(1, $names);
        }
    }
}
