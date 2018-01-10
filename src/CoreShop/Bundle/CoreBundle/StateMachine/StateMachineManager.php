<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\StateMachine;

use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;

final class StateMachineManager
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param      $subject
     * @param null $workflowName
     * @return Workflow
     */
    public function get($subject, $workflowName = null)
    {
        return $this->registry->get($subject, $workflowName);
    }

    /**
     * {@inheritdoc}
     */
    public function getTransitionFromState(Workflow $workflow, $subject, string $fromState)
    {
        /** @var Transition $transition */
        foreach ($workflow->getEnabledTransitions($subject) as $transition) {
            if (in_array($fromState, $transition->getFroms(), true)) {
                return $transition;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getTransitionToState(Workflow $workflow, $subject, string $toState)
    {
        /** @var Transition $transition */
        foreach ($workflow->getEnabledTransitions($subject) as $transition) {
            if (in_array($toState, $transition->getTos(), true)) {
                return $transition;
            }
        }

        return null;
    }
}
