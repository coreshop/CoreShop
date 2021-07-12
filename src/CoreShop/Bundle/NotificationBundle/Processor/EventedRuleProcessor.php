<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\NotificationBundle\Processor;

use CoreShop\Bundle\NotificationBundle\Events;
use CoreShop\Component\Notification\Processor\RulesProcessorInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class EventedRuleProcessor implements RulesProcessorInterface
{
    private $rulesProcessor;
    private $eventDispatcher;

    public function __construct(
        RulesProcessorInterface $rulesProcessor,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->rulesProcessor = $rulesProcessor;
        $this->eventDispatcher = $eventDispatcher;
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
