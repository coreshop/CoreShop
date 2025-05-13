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

namespace CoreShop\Bundle\WorkflowBundle\StateManager;

use CoreShop\Bundle\WorkflowBundle\Event\WorkflowTransitionEvent;
use CoreShop\Bundle\WorkflowBundle\History\HistoryRepositoryInterface;
use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManagerInterface;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class WorkflowStateInfoManager implements WorkflowStateInfoManagerInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private StateMachineManagerInterface $stateMachineManager,
        private TranslatorInterface $translator,
        private HistoryRepositoryInterface $historyRepository,
        private array $stateColors,
    ) {
    }

    public function getStateInfo(string $workflowName, string $value, bool $forFrontend = true, ?string $locale = null): array
    {
        $transPrefix = $forFrontend ? 'coreshop.ui.workflow.state.' : 'coreshop_workflow_state_';
        $transValue = $transPrefix . $workflowName . ($forFrontend ? '.' : '_') . $value;

        $color = $this->stateColors[$workflowName]['place_colors'][$value] ?? '#f6f1de';

        return [
            'label' => $this->translator->trans($transValue, [], $forFrontend ? null : 'admin', $locale),
            'state' => $value,
            'color' => $color,
        ];
    }

    public function parseTransitions(object $subject, string $workflowName, array $transitions = [], bool $forFrontend = true, ?string $locale = null): array
    {
        $event = new WorkflowTransitionEvent($transitions, $workflowName);
        $this->eventDispatcher->dispatch($event, 'coreshop.workflow.valid_transitions');

        $valid = [];
        $workflow = $this->stateMachineManager->get($subject, $workflowName);
        foreach ($event->getAllowedTransitions() as $transition) {
            if ($workflow->can($subject, $transition)) {
                $valid[] = $this->getTransitionInfo($workflowName, $transition, $forFrontend, $locale);
            }
        }

        return $valid;
    }

    public function getTransitionInfo(string $workflowName, string $transition, bool $forFrontend = true, ?string $locale = null): array
    {
        $transPrefix = $forFrontend ? 'coreshop.ui.workflow.transition.' : 'coreshop_workflow_transition_';
        $transValue = $transPrefix . $workflowName . ($forFrontend ? '.' : '_') . $transition;
        $color = $this->stateColors[$workflowName]['transition_colors'][$transition] ?? '#999999';

        return [
            'label' => $this->translator->trans($transValue, [], $forFrontend ? null : 'admin', $locale),
            'transition' => $transition,
            'color' => $color,
        ];
    }

    public function getStateHistory(Concrete $object): array
    {
        return $this->historyRepository->getHistory($object);
    }
}
