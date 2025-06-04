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

namespace CoreShop\Component\Notification\Processor;

use CoreShop\Component\Notification\Messenger\NotificationMessage;
use CoreShop\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class RulesProcessor implements RulesProcessorInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {
    }

    public function applyRules(string $type, ResourceInterface $subject, array $params = []): void
    {
        //BC
        if (isset($params['order']) && $params['order'] instanceof ResourceInterface) {
            $params['order_id'] = $params['order']->getId();

            unset($params['order']);
        }

        //BC
        if (isset($params['store']) && $params['store'] instanceof ResourceInterface) {
            $params['store_id'] = $params['store']->getId();

            unset($params['store']);
        }

        $this->messageBus->dispatch(
            new NotificationMessage(
                $type,
                get_class($subject),
                $subject->getId(),
                $params,
            ),
        );
    }
}
