<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\WorkflowBundle\StateManager;

use CoreShop\Bundle\WorkflowBundle\Event\WorkflowTransitionEvent;
use CoreShop\Bundle\WorkflowBundle\History\HistoryRepositoryInterface;
use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class WorkflowStateInfoManager implements WorkflowStateInfoManagerInterface
{
    private EventDispatcherInterface $eventDispatcher;
    private StateMachineManagerInterface $stateMachineManager;
    private TranslatorInterface $translator;
    private HistoryRepositoryInterface $historyRepository;
    private array $stateColors;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        StateMachineManagerInterface $stateMachineManager,
        TranslatorInterface $translator,
        HistoryRepositoryInterface $historyRepository,
        array $stateColors
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->stateMachineManager = $stateMachineManager;
        $this->historyRepository = $historyRepository;
        $this->translator = $translator;
        $this->stateColors = $stateColors;
    }

    public function getStateInfo(string $workflowName, string $value, bool $forFrontend = true): array
    {
        $transPrefix = $forFrontend ? 'coreshop.ui.workflow.state.' : 'coreshop_workflow_state_';
        $transValue = $transPrefix . $workflowName . ($forFrontend ? '.' : '_') . $value;

        $color = isset($this->stateColors[$workflowName]['place_colors'][$value]) ? $this->stateColors[$workflowName]['place_colors'][$value] : '#f6f1de';

        $data = [
            'label' => $this->translator->trans($transValue, [], $forFrontend ? null : 'admin'),
            'state' => $value,
            'color' => $color,
        ];

        return $data;
    }

    public function parseTransitions($subject, string $workflowName, array $transitions = [], bool $forFrontend = true): array
    {
        $event = new WorkflowTransitionEvent($transitions, $workflowName);
        $this->eventDispatcher->dispatch($event, 'coreshop.workflow.valid_transitions');

        $valid = [];
        $workflow = $this->stateMachineManager->get($subject, $workflowName);
        foreach ($event->getAllowedTransitions() as $transition) {
            if ($workflow->can($subject, $transition)) {
                $valid[] = $this->getTransitionInfo($workflowName, $transition, $forFrontend);
            }
        }

        return $valid;
    }

    public function getTransitionInfo($workflowName, $transition, $forFrontend = true)
    {
        $transPrefix = $forFrontend ? 'coreshop.ui.workflow.transition.' : 'coreshop_workflow_transition_';
        $transValue = $transPrefix . $workflowName . ($forFrontend ? '.' : '_') . $transition;
        $color = isset($this->stateColors[$workflowName]['transition_colors'][$transition]) ? $this->stateColors[$workflowName]['transition_colors'][$transition] : '#999999';

        $data = [
            'label' => $this->translator->trans($transValue, [], $forFrontend ? null : 'admin'),
            'transition' => $transition,
            'color' => $color,
        ];

        return $data;
    }

    public function getStateHistory($order): array
    {
        return $this->historyRepository->getHistory($order);
    }
}
