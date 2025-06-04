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

namespace CoreShop\Bundle\NotificationBundle\Processor;

use CoreShop\Bundle\NotificationBundle\Events;
use CoreShop\Component\Notification\Processor\RulesProcessorInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class EventedRuleProcessor implements RulesProcessorInterface
{
    public function __construct(
        private RulesProcessorInterface $rulesProcessor,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function applyRules(string $type, $subject, array $params = []): void
    {
        $event = new GenericEvent($type, [
            'subject' => $subject,
            'params' => $params,
        ]);

        $this->eventDispatcher->dispatch($event, Events::PRE_APPLY);

        if ($event->isPropagationStopped()) {
            return;
        }

        $this->rulesProcessor->applyRules($type, $subject, $params);

        $this->eventDispatcher->dispatch($event, Events::POST_APPLY);
    }
}
