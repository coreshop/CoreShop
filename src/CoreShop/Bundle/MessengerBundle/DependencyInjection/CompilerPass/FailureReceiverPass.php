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

use CoreShop\Bundle\MessengerBundle\Messenger\FailureReceiversRepository;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class FailureReceiverPass implements CompilerPassInterface
{
    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(FailureReceiversRepository::class)) {
            return;
        }

        $failureReceivers = $container->getDefinition(FailureReceiversRepository::class);

        if (!$container->hasDefinition('messenger.failure.send_failed_message_to_failure_transport_listener')) {
            $emptyContainer = new Definition(Container::class);

            $failureReceivers->replaceArgument(0, $emptyContainer);
            $failureReceivers->replaceArgument(1, []);

            return;
        }

        $failedMessageToFailureTransportListener = $container->getDefinition(
            'messenger.failure.send_failed_message_to_failure_transport_listener',
        );
        $failureReceivers->replaceArgument(0, $failedMessageToFailureTransportListener->getArgument(0));

        if (!$container->hasDefinition('console.command.messenger_consume_messages')) {
            $failureReceivers->replaceArgument(1, []);

            return;
        }

        $consumeCommandDefinition = $container->getDefinition('console.command.messenger_consume_messages');
        $names = $consumeCommandDefinition->getArgument(4);
        $failureReceivers->replaceArgument(1, $names);
    }
}
