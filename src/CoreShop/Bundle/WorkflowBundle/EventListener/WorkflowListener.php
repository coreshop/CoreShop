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

namespace CoreShop\Bundle\WorkflowBundle\EventListener;

use CoreShop\Component\Pimcore\DataObject\InheritanceHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ExpressionLanguage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class WorkflowListener implements EventSubscriberInterface
{
    private $callbackConfig;
    protected $container;

    public function __construct(array $callbackConfig, ContainerInterface $container)
    {
        $this->callbackConfig = $callbackConfig;
        $this->container = $container;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.enter' => ['onTransitionEnter'],
            'workflow.completed' => ['onTransitionCompleted'],
        ];
    }

    public function onTransitionEnter(Event $event): void
    {
        if (!isset($this->callbackConfig[$event->getWorkflowName()])) {
            return;
        }

        $workflowCallbacks = $this->callbackConfig[$event->getWorkflowName()];
        $transitionName = $event->getTransition()->getName();

        if (empty($workflowCallbacks['before'])) {
            return;
        }

        $beforeActions = $this->setCallbacksPriority($workflowCallbacks['before']);
        $this->applyTransition($transitionName, $event, $beforeActions);
    }

    public function onTransitionCompleted(Event $event): void
    {
        if (!isset($this->callbackConfig[$event->getWorkflowName()])) {
            return;
        }

        $workflowCallbacks = $this->callbackConfig[$event->getWorkflowName()];
        $transitionName = $event->getTransition()->getName();

        if (empty($workflowCallbacks['after'])) {
            return;
        }

        $afterActions = $this->setCallbacksPriority($workflowCallbacks['after']);
        $this->applyTransition($transitionName, $event, $afterActions);
    }

    public function applyTransition(string $transitionName, Event $event, array $actions): void
    {
        foreach ($actions as $callback) {
            if (!in_array($transitionName, $callback['on'])) {
                continue;
            }

            if (empty($callback['do'])) {
                continue;
            }

            InheritanceHelper::useInheritedValues(function () use ($event, $callback) {
                $this->call($event, $callback['do'], $callback['args']);
            });
        }
    }

    public function call(Event $event, array $callable, array $callableArgs = []): void
    {
        if (is_array($callable)
            && is_string($callable[0])
            && 0 === strpos($callable[0], '@')
        ) {
            $serviceId = substr($callable[0], 1);
            $callable[0] = $this->container->get($serviceId);
        }

        if (empty($callableArgs)) {
            $args = [$event];
        } else {
            $expr = new ExpressionLanguage();
            $args = array_map(
                function ($arg) use ($expr, $event) {
                    if (!is_string($arg)) {
                        return $arg;
                    }

                    return $expr->evaluate($arg, [
                        'object' => $event->getSubject(),
                        'event' => $event,
                        'container' => $this->container,
                    ]);
                },
                $callableArgs
            );
        }

        call_user_func_array($callable, $args);
    }

    protected function setCallbacksPriority(array $callbacks): array
    {
        uasort($callbacks, function ($a, $b) {
            if ($a['priority'] === $b['priority']) {
                return 0;
            }

            return $a['priority'] < $b['priority'] ? -1 : 1;
        });

        return $callbacks;
    }
}
