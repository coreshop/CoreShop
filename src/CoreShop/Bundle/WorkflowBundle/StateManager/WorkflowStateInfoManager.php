<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\WorkflowBundle\StateManager;

use CoreShop\Bundle\OrderBundle\Event\WorkflowTransitionEvent;
use CoreShop\Bundle\WorkflowBundle\History\HistoryRepositoryInterface;
use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Translation\TranslatorInterface;

final class WorkflowStateInfoManager implements WorkflowStateInfoManagerInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var StateMachineManager
     */
    private $stateMachineManager;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var HistoryRepositoryInterface
     */
    private $historyRepository;

    /**
     * @var array
     */
    private $stateColors;

    /**
     * @param EventDispatcherInterface   $eventDispatcher
     * @param StateMachineManager        $stateMachineManager
     * @param TranslatorInterface        $translator
     * @param HistoryRepositoryInterface $historyRepository
     * @param array                      $stateColors
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        StateMachineManager $stateMachineManager,
        TranslatorInterface $translator,
        HistoryRepositoryInterface $historyRepository,
        $stateColors
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->stateMachineManager = $stateMachineManager;
        $this->historyRepository = $historyRepository;
        $this->translator = $translator;
        $this->stateColors = $stateColors;
    }

    /**
     * @param string $workflowName
     * @param mixed  $value
     * @param bool   $forFrontend
     *
     * @return array
     */
    public function getStateInfo($workflowName, $value, $forFrontend = true)
    {
        $transPrefix = $forFrontend ? 'coreshop.ui.workflow.state.' : 'coreshop_workflow_state_';
        $transValue = $transPrefix.$workflowName.($forFrontend ? '.' : '_').$value;

        $color = isset($this->stateColors[$workflowName]['place_colors'][$value]) ? $this->stateColors[$workflowName]['place_colors'][$value] : '#f6f1de';

        $data = [
            'label' => $this->translator->trans($transValue, [], $forFrontend ? null : 'admin'),
            'state' => $value,
            'color' => $color,
        ];

        return $data;
    }

    /**
     * @param string $workflowName
     * @param string $transition
     * @param bool   $forFrontend
     *
     * @return array
     */
    public function getTransitionInfo($workflowName, $transition, $forFrontend = true)
    {
        $transPrefix = $forFrontend ? 'coreshop.ui.workflow.transition.' : 'coreshop_workflow_transition_';
        $transValue = $transPrefix.$workflowName.($forFrontend ? '.' : '_').$transition;
        $color = isset($this->stateColors[$workflowName]['transition_colors'][$transition]) ? $this->stateColors[$workflowName]['transition_colors'][$transition] : '#999999';

        $data = [
            'label' => $this->translator->trans($transValue, [], $forFrontend ? null : 'admin'),
            'transition' => $transition,
            'color' => $color,
        ];

        return $data;
    }

    /**
     * @param string $subject
     * @param string $workflowName
     * @param array  $transitions
     * @param bool   $forFrontend
     *
     * @return array
     */
    public function parseTransitions($subject, $workflowName, $transitions = [], $forFrontend = true)
    {
        $event = new WorkflowTransitionEvent($transitions, $workflowName);
        $this->eventDispatcher->dispatch('coreshop.workflow.valid_transitions', $event);

        $valid = [];
        $workflow = $this->stateMachineManager->get($subject, $workflowName);
        foreach ($event->getAllowedTransitions() as $transition) {
            if ($workflow->can($subject, $transition)) {
                $valid[] = $this->getTransitionInfo($workflowName, $transition, $forFrontend);
            }
        }

        return $valid;
    }

    /**
     * {@inheritdoc}
     */
    public function getStateHistory($proposal)
    {
        return $this->historyRepository->getHistory($proposal);
    }
}
